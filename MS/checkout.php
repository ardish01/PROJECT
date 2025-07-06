<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Khalti sandbox public key (for client-side integrations, not used in server-side ePayment)
$khalti_public_key = 'a732515ed1834f3f9f67fd7657a98e41'; // Provided by user
// Khalti sandbox secret key
$khalti_secret_key = '9b72dcd1f0da4bb4a51f39a2ea0c7cc8'; // Provided by user
$return_url = 'http://localhost/MS/khalti_return.php'; // Callback after payment
$website_url = 'http://localhost/MS/';

// Get cart items and calculate total
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT c.product_id, c.quantity, p.name, p.price, p.image_url FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?');
$stmt->execute([$_SESSION['session_id']]);
$cart_items = $stmt->fetchAll();

if (!$cart_items) {
    header('Location: cart.php');
    exit();
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$price = $total;
$amount_paisa = (int) round($price * 100);
$order_id = uniqid('order_');
$order_name = 'R.D.S Gears Order';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_khalti'])) {
    // Prepare payload for Khalti ePayment
    $payload = [
        'return_url' => $return_url,
        'website_url' => $website_url,
        'amount' => $amount_paisa,
        'purchase_order_id' => $order_id,
        'purchase_order_name' => $order_name,
        'customer_info' => [
            'name' => $_SESSION['username'],
            'email' => '', // Add email if available
            'phone' => ''  // Add phone if available
        ]
    ];
    $ch = curl_init('https://dev.khalti.com/api/v2/epayment/initiate/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: key 9b72dcd1f0da4bb4a51f39a2ea0c7cc8',
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);
    if ($http_code === 200 && isset($result['payment_url'])) {
        // Redirect to Khalti payment portal
        header('Location: ' . $result['payment_url']);
        exit();
    } else {
        $error_message = isset($result['detail']) ? $result['detail'] : 'Unable to initiate Khalti payment.';
        // Show full API response for debugging
        $error_message .= '<br><pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - R.D.S Gears</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .payment-container { max-width: 500px; margin: 2rem auto; background: #fff; border-radius: 12px; box-shadow: 0 8px 32px rgba(44,62,80,0.10); padding: 2rem; }
        .payment-header { text-align: center; margin-bottom: 2rem; }
        .payment-header img { width: 200px; margin-bottom: 1rem; }
        .payment-header h1 { color: #2c3e50; margin-bottom: 0.5rem; }
        .order-summary { background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
        .order-summary h3 { margin-bottom: 0.5rem; color: #2c3e50; }
        .order-item { display: flex; justify-content: space-between; margin-bottom: 0.3rem; font-size: 0.9rem; }
        .price-display { background: #f8f9fa; padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; border: 1px solid #e9ecef; }
        .price-display .amount { font-size: 1.5rem; font-weight: bold; color: #5C2D91; }
        .btn { width: 100%; background: #5C2D91; color: white; border: none; padding: 1rem; border-radius: 6px; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #4a1f7a; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <img src="assets/images/khalti-logo.png" alt="Khalti" onerror="this.style.display='none'">
            <h1>Complete Payment</h1>
            <p>Secure payment via Khalti</p>
        </div>
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
                <script>setTimeout(function(){window.location.href='index.php';},3000);</script>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (Qty: <?php echo $item['quantity']; ?>)</span>
                    <span>NRS <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <hr style="margin: 0.5rem 0;">
            <div class="order-item" style="font-weight: bold;">
                <span>Total Amount:</span>
                <span>NRS <?php echo number_format($price, 2); ?></span>
            </div>
        </div>
        <form method="post" class="payment-form">
            <div class="price-display">
                <div class="amount">NRS <?php echo number_format($price, 2); ?></div>
                <small>Amount to be paid</small>
            </div>
            <button type="submit" name="pay_khalti" class="btn">Pay with Khalti</button>
        </form>
    </div>
</body>
</html>