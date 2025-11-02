<?php
session_start();
include_once "includes/connection.php";
include_once "includes/functions.php";


$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Contact Us - Real Estate Management System";

// Initialize message variable
$status_msg = '';

// Get user details if logged in
$user_name = '';
$user_email = '';
if (isset($_SESSION['user_id'])) {
    $user_query = "SELECT full_name, email FROM users WHERE user_id = ?";
    $stmt = $con->prepare($user_query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user_data = $result->fetch_assoc()) {
        $user_name = $user_data['full_name'];
        $user_email = $user_data['email'];
    }
}

// Handle form submission
if (isset($_POST['submit'])) {
    // Get and sanitize form data
    if (isset($_SESSION['user_id'])) {
        $name = $user_name;
        $email = $user_email;
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
    }
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    // Get user_id if logged in, otherwise use a default guest user
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Using admin (ID 1) as default for guest messages

    // Insert message into database with user_id
    $query = "INSERT INTO messages (name, email, subject, message, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssi", $name, $email, $subject, $message, $user_id);

    if ($stmt->execute()) {
        $status_msg = '<div class="alert alert-success">Your message has been sent successfully!</div>';
    } else {
        $status_msg = '<div class="alert alert-danger">Sorry, there was an error sending your message. Please try again.</div>';
    }
}

include 'includes/nav.php';
?>
<style>
   .social-links img {
    width: 20px;
    height: 20px;
    margin-top: 7px;
}
</style>
<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Contact Us</h2>
    </div>
</div>
<!-- banner -->

<div class="container">
    <div class="spacer">
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <h2>Contact Information</h2>
                <div class="well">
                    <p><b>Address:</b><br>
                        Jaggamandu<br>
                        Bhaktapur, Nepal<br>
                        Phone: +123456789<br>
                        Email: info@jaggamandubkt.com</p>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <h2>Send us a Message</h2>
                <?php if ($status_msg): ?>
                    <?php echo $status_msg; ?>
                <?php endif; ?>
                <form action="contact.php" method="post">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                    <?php else: ?>
                        <div class="well well-sm">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>