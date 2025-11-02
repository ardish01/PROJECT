<?php
session_start();
require_once '../config.inc.php';
require_once '../check_admin.php';

$message = '';

// Handle application status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id']) && isset($_POST['action'])) {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        // Get application details
        $sql = "SELECT * FROM agent_applications WHERE application_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $application = $result->fetch_assoc();
        
        if ($application) {
            // Insert into agent table
            $sql = "INSERT INTO agent (agent_name, agent_address, agent_contact, agent_email) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", 
                $application['full_name'],
                $application['address'],
                $application['phone'],
                $application['email']
            );
            
            if ($stmt->execute()) {
                // Update application status
                $sql = "UPDATE agent_applications SET status = 'approved' WHERE application_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $application_id);
                $stmt->execute();
                $message = "Application approved successfully!";
            } else {
                $message = "Error approving application.";
            }
        }
    } elseif ($action === 'reject') {
        $sql = "UPDATE agent_applications SET status = 'rejected' WHERE application_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $application_id);
        if ($stmt->execute()) {
            $message = "Application rejected successfully!";
        } else {
            $message = "Error rejecting application.";
        }
    }
}

// Fetch all applications
$sql = "SELECT aa.*, u.username 
        FROM agent_applications aa 
        JOIN users u ON aa.user_id = u.user_id 
        ORDER BY aa.created_at DESC";
$result = $conn->query($sql);

// Fetch only pending applications
$sql_pending = "SELECT aa.*, u.username 
        FROM agent_applications aa 
        JOIN users u ON aa.user_id = u.user_id 
        WHERE aa.status = 'pending' 
        ORDER BY aa.created_at DESC";
$result_pending = $conn->query($sql_pending);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agent Applications - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/admin-header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Manage Agent Applications</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- New Agent Requests Section -->
        <div class="mb-5">
            <h4>New Agent Requests</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Experience</th>
                            <th>CV</th>
                            <th>Applied On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pending->num_rows === 0): ?>
                            <tr><td colspan="10" class="text-center">No new agent requests.</td></tr>
                        <?php else: ?>
                        <?php while ($row = $result_pending->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['application_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#experienceModalPending<?php echo $row['application_id']; ?>">
                                        View
                                    </button>
                                </td>
                                <td>
                                    <a href="../<?php echo $row['cv_path']; ?>" class="btn btn-sm btn-primary" target="_blank">
                                        Download
                                    </a>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <!-- Experience Modal for Pending -->
                            <div class="modal fade" id="experienceModalPending<?php echo $row['application_id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Experience Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo nl2br(htmlspecialchars($row['experience'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Experience</th>
                        <th>CV</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['application_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#experienceModal<?php echo $row['application_id']; ?>">
                                    View
                                </button>
                            </td>
                            <td>
                                <a href="../<?php echo $row['cv_path']; ?>" class="btn btn-sm btn-primary" target="_blank">
                                    Download
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status'] === 'pending' ? 'warning' : 
                                        ($row['status'] === 'approved' ? 'success' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                            Reject
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Experience Modal -->
                        <div class="modal fade" id="experienceModal<?php echo $row['application_id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Experience Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo nl2br(htmlspecialchars($row['experience'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 