<?php
session_start();
require_once 'includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $experience = $_POST['experience'];
    
    // Handle CV upload
    $target_dir = "uploads/cv/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $cv_file = $_FILES['cv'];
    $cv_path = $target_dir . time() . '_' . basename($cv_file["name"]);
    $allowed_types = array('pdf', 'doc', 'docx');
    $file_type = strtolower(pathinfo($cv_file["name"], PATHINFO_EXTENSION));
    
    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($cv_file["tmp_name"], $cv_path)) {
            $sql = "INSERT INTO agent_applications (user_id, full_name, email, phone, address, experience, cv_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("issssss", $user_id, $full_name, $email, $phone, $address, $experience, $cv_path);
            
            if ($stmt->execute()) {
                $message = "Application submitted successfully! We will review your application and get back to you soon.";
            } else {
                $message = "Error submitting application. Please try again.";
            }
            $stmt->close();
        } else {
            $message = "Error uploading CV. Please try again.";
        }
    } else {
        $message = "Only PDF, DOC, and DOCX files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become an Agent - Real Estate</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Become an Agent</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="experience" class="form-label">Real Estate Experience</label>
                                <textarea class="form-control" id="experience" name="experience" rows="5" required 
                                    placeholder="Please describe your experience in real estate, including any relevant qualifications or achievements"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="cv" class="form-label">Upload CV (PDF, DOC, or DOCX)</label>
                                <input type="file" class="form-control" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 