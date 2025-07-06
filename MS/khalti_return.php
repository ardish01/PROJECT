<?php
session_start();
require_once 'config/database.php';

// Khalti secret key (must match checkout.php)
$khalti_secret_key = '9b72dcd1f0da4bb4a51f39a2ea0c7cc8';

// Get pidx from Khalti callback
$pidx = $_GET['pidx'] ?? $_POST['pidx'] ?? null;

$success = false;
$message = '';

if ($pidx) {
    // Verify payment with Khalti
    $payload = [ 'pidx' => $pidx ];
    $ch = curl_init('https://dev.khalti.com/api/v2/epayment/lookup/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: key ' . $khalti_secret_key,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);

    if ($http_code === 200 && isset($result['status']) && $result['status'] === 'Completed') {
        // Payment successful, create order if not already created
        $pdo = getDBConnection();
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = $_SESSION['session_id'] ?? null;
        
        // Get cart items and total
        $stmt = $pdo->prepare('SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?');
        $stmt->execute([$session_id]);
        $cart_items = $stmt->fetchAll();
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Insert order
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status, payment_method, payment_type, payment_reference, order_date) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $user_id,
            $total,
            'completed',
            'Khalti',
            'khalti',
            $pidx
        ]);
        $order_id = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }
        
        // Clear cart
        if ($session_id) {
            $stmt = $pdo->prepare('DELETE FROM cart WHERE session_id = ?');
            $stmt->execute([$session_id]);
        }
        $success = true;
        $message = 'Payment successful! Thank you for your purchase.';
    } else {
        $message = 'Payment verification failed or not completed.';
        if (isset($result['status'])) {
            $message .= ' Status: ' . htmlspecialchars($result['status']);
        }
        if (isset($result['detail'])) {
            $message .= ' Detail: ' . htmlspecialchars($result['detail']);
        }
    }
} else {
    $message = 'Missing payment reference (pidx).';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Khalti Payment Result</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .result-container { max-width: 500px; margin: 4rem auto; background: #fff; border-radius: 12px; box-shadow: 0 8px 32px rgba(44,62,80,0.10); padding: 2rem; text-align: center; }
        .result-container h1 { color: <?php echo $success ? '#27ae60' : '#e74c3c'; ?>; margin-bottom: 1rem; }
        .result-container p { color: #2c3e50; }
        .btn { margin-top: 2rem; background: #5C2D91; color: white; border: none; padding: 1rem 2rem; border-radius: 6px; font-size: 1.1rem; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #4a1f7a; }
    </style>
</head>
<body>
    <div class="result-container">
        <h1><?php echo $success ? 'Payment Successful!' : 'Payment Failed'; ?></h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php" class="btn">Go to Home</a>
    </div>
</body>
</html> 