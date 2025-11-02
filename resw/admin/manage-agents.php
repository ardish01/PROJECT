<?php
session_start();
include_once "../includes/connection.php";
include_once "../includes/functions.php";

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$isSubDirectory = true;
$page_title = "Manage Agents - Admin Dashboard";

// Initialize variables
$edit_agent = array(
    'agent_id' => '',
    'agent_name' => '',
    'agent_address' => '',
    'agent_contact' => '',
    'agent_email' => ''
);

// Handle agent deletion
if (isset($_POST['delete_agent'])) {
    $agent_id = mysqli_real_escape_string($con, $_POST['agent_id']);
    
    // First check if the agent exists
    $check_query = "SELECT agent_id FROM agent WHERE agent_id = '$agent_id'";
    $check_result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Check if agent has any associated properties
        $check_properties = "SELECT COUNT(*) as count FROM properties WHERE agent_id = '$agent_id'";
        $properties_result = mysqli_query($con, $check_properties);
        $properties_count = mysqli_fetch_assoc($properties_result)['count'];
        
        if ($properties_count > 0) {
            $_SESSION['error_msg'] = "Cannot delete agent. Please reassign or delete their properties first.";
            header("Location: manage-agents.php");
            exit();
        }
        
        // Now delete the agent since there are no associated properties
        $query = "DELETE FROM agent WHERE agent_id = '$agent_id'";
        if (mysqli_query($con, $query)) {
            $_SESSION['success_msg'] = "Agent deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Error deleting agent: " . mysqli_error($con);
        }
    } else {
        $_SESSION['error_msg'] = "Agent not found!";
    }
    
    header("Location: manage-agents.php");
    exit();
}

// Handle agent addition/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_agent'])) {
    $agent_name = mysqli_real_escape_string($con, $_POST['agent_name']);
    $agent_address = mysqli_real_escape_string($con, $_POST['agent_address']);
    $agent_contact = mysqli_real_escape_string($con, $_POST['agent_contact']);
    $agent_email = mysqli_real_escape_string($con, $_POST['agent_email']);
    $agent_id = isset($_POST['agent_id']) ? mysqli_real_escape_string($con, $_POST['agent_id']) : null;
    
    // Validate email format
    if (!filter_var($agent_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_msg'] = "Invalid email format!";
        header("Location: manage-agents.php");
        exit();
    }
    
    // Check for duplicate email
    $check_email = "SELECT agent_id FROM agent WHERE agent_email = '$agent_email'";
    if ($agent_id) {
        $check_email .= " AND agent_id != '$agent_id'";
    }
    $email_result = mysqli_query($con, $check_email);
    if (mysqli_num_rows($email_result) > 0) {
        $_SESSION['error_msg'] = "This email is already registered with another agent!";
        header("Location: manage-agents.php");
        exit();
    }
    
    // Check for duplicate contact number
    $check_contact = "SELECT agent_id FROM agent WHERE agent_contact = '$agent_contact'";
    if ($agent_id) {
        $check_contact .= " AND agent_id != '$agent_id'";
    }
    $contact_result = mysqli_query($con, $check_contact);
    if (mysqli_num_rows($contact_result) > 0) {
        $_SESSION['error_msg'] = "This contact number is already registered with another agent!";
        header("Location: manage-agents.php");
        exit();
    }
    
    if ($agent_id) {
        // Update existing agent
        $query = "UPDATE agent SET 
                  agent_name = '$agent_name',
                  agent_address = '$agent_address',
                  agent_contact = '$agent_contact',
                  agent_email = '$agent_email'
                  WHERE agent_id = '$agent_id'";
        $message = "Agent updated successfully!";
    } else {
        // Add new agent
        $query = "INSERT INTO agent (agent_name, agent_address, agent_contact, agent_email) 
                  VALUES ('$agent_name', '$agent_address', '$agent_contact', '$agent_email')";
        $message = "Agent added successfully!";
    }
    
    if (mysqli_query($con, $query)) {
        $_SESSION['success_msg'] = $message;
        header("Location: manage-agents.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Error: " . mysqli_error($con);
    }
}

// Get agent for editing if ID is provided
if (isset($_GET['edit'])) {
    $agent_id = mysqli_real_escape_string($con, $_GET['edit']);
    $query = "SELECT * FROM agent WHERE agent_id = '$agent_id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_agent = mysqli_fetch_assoc($result);
    }
}

// Fetch all agents
$query = "SELECT * FROM agent ORDER BY agent_id DESC";
$result = mysqli_query($con, $query);
$agents = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $agents[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    
    <style>
    :root {
        --primary-color: #572a00;
        --hover-color: #562700;
        --white: #ffffff;
        --light-bg: #f8f9fa;
        --shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .inside-banner {
        background-color: var(--primary-color);
        color: var(--white);
        padding: 40px 0;
        margin-bottom: 40px;
    }

    .inside-banner h2 {
        margin: 0;
        color: var(--white);
    }

    .panel {
        border-radius: 10px;
        box-shadow: var(--shadow);
        border: none;
        margin-bottom: 30px;
    }

    .panel-heading {
        background-color: var(--primary-color) !important;
        color: var(--white) !important;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-start;
        align-items: center;
    }

    .action-buttons .btn {
        padding: 6px 12px;
        font-size: 13px;
        line-height: 1.5;
        border-radius: 4px;
        white-space: nowrap;
    }

    .btn-sm {
        margin: 2px;
    }

    .table > thead > tr > th {
        background-color: var(--light-bg);
        border-bottom: 2px solid var(--primary-color);
        padding: 12px 8px;
    }

    .table > tbody > tr > td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active,
    .btn-primary.active {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }

    .btn-danger:hover,
    .btn-danger:focus,
    .btn-danger:active,
    .btn-danger.active {
        background-color: #c9302c;
        border-color: #ac2925;
    }

    .btn-default:hover,
    .btn-default:focus,
    .btn-default:active,
    .btn-default.active {
        background-color: #e6e6e6;
        border-color: #adadad;
    }
    
    img {
        margin-top: 3px;
    }
    </style>
</head>
<body>
    <?php include '../includes/nav.php'; ?>

<!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Manage Agents</h2>
    </div>
</div>
<!-- banner -->

<div class="container">
    <div class="properties-listing spacer">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_msg'];
                unset($_SESSION['success_msg']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_msg'];
                unset($_SESSION['error_msg']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add/Edit Agent Form -->
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo isset($edit_agent['agent_id']) && $edit_agent['agent_id'] ? 'Edit Agent' : 'Add New Agent'; ?></h3>
                    </div>
                    <div class="panel-body">
                            <form action="" method="POST" id="agentForm" onsubmit="return validateForm()">
                            <?php if (isset($edit_agent['agent_id']) && $edit_agent['agent_id']): ?>
                                <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($edit_agent['agent_id']); ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="agent_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($edit_agent['agent_name']); ?>" 
                                           required minlength="3" maxlength="150">
                                    <div id="nameError" class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="agent_address" class="form-control" 
                                           value="<?php echo htmlspecialchars($edit_agent['agent_address']); ?>" 
                                           required minlength="5" maxlength="250">
                                    <div id="addressError" class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Contact</label>
                                <input type="text" name="agent_contact" class="form-control" 
                                           value="<?php echo htmlspecialchars($edit_agent['agent_contact']); ?>" 
                                           required pattern="[0-9\s+()-]{10,20}">
                                    <div id="contactError" class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="agent_email" class="form-control" 
                                           value="<?php echo htmlspecialchars($edit_agent['agent_email']); ?>" 
                                           required maxlength="25">
                                    <div id="emailError" class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="submit_agent" class="btn btn-primary">
                                    <?php echo isset($edit_agent['agent_id']) && $edit_agent['agent_id'] ? 'Update Agent' : 'Add Agent'; ?>
                                </button>
                                <?php if (isset($edit_agent['agent_id']) && $edit_agent['agent_id']): ?>
                                    <a href="manage-agents.php" class="btn btn-default">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Agents List -->
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">All Agents</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agents as $agent): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($agent['agent_name']); ?></td>
                                            <td><?php echo htmlspecialchars($agent['agent_address']); ?></td>
                                            <td><?php echo htmlspecialchars($agent['agent_contact']); ?></td>
                                            <td><?php echo htmlspecialchars($agent['agent_email']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="?edit=<?php echo $agent['agent_id']; ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class=""></i> Edit
                                                    </a>
                                                    <form action="" method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this agent?');">
                                                        <input type="hidden" name="agent_id" value="<?php echo $agent['agent_id']; ?>">
                                                        <button type="submit" name="delete_agent" class="btn btn-danger btn-sm">
                                                            <i class="glyphicon glyphicon-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Core JavaScript -->
    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
    <script src="../assets/script.js"></script>

    <script>
    // Real-time validation functions
    function validateName(input) {
        var name = input.value;
        var nameError = document.getElementById('nameError');
        
        if (name.length < 3) {
            nameError.textContent = 'Name must be at least 3 characters long';
            input.classList.add('is-invalid');
            return false;
        } else if (name.length > 150) {
            nameError.textContent = 'Name cannot exceed 150 characters';
            input.classList.add('is-invalid');
            return false;
        } else {
            nameError.textContent = '';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    function validateAddress(input) {
        var address = input.value;
        var addressError = document.getElementById('addressError');
        
        if (address.length < 5) {
            addressError.textContent = 'Address must be at least 5 characters long';
            input.classList.add('is-invalid');
            return false;
        } else if (address.length > 250) {
            addressError.textContent = 'Address cannot exceed 250 characters';
            input.classList.add('is-invalid');
            return false;
        } else {
            addressError.textContent = '';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    function validateContact(input) {
        var contact = input.value;
        var contactError = document.getElementById('contactError');
        var contactPattern = /^[0-9\s+()-]{10}$/;
        
        if (!contactPattern.test(contact)) {
            contactError.textContent = 'Please enter a valid contact number (10 characters)';
            input.classList.add('is-invalid');
            return false;
        } else {
            contactError.textContent = '';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    function validateEmail(input) {
        var email = input.value;
        var emailError = document.getElementById('emailError');
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email)) {
            emailError.textContent = 'Please enter a valid email address';
            input.classList.add('is-invalid');
            return false;
        } else if (email.length > 25) {
            emailError.textContent = 'Email cannot exceed 25 characters';
            input.classList.add('is-invalid');
            return false;
        } else {
            emailError.textContent = '';
            input.classList.remove('is-invalid');
            return true;
        }
    }

    // Form submission validation
    function validateForm() {
        var nameInput = document.getElementsByName('agent_name')[0];
        var addressInput = document.getElementsByName('agent_address')[0];
        var contactInput = document.getElementsByName('agent_contact')[0];
        var emailInput = document.getElementsByName('agent_email')[0];
        
        var isNameValid = validateName(nameInput);
        var isAddressValid = validateAddress(addressInput);
        var isContactValid = validateContact(contactInput);
        var isEmailValid = validateEmail(emailInput);
        
        return isNameValid && isAddressValid && isContactValid && isEmailValid;
    }

    // Add event listeners when the document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        var nameInput = document.getElementsByName('agent_name')[0];
        var addressInput = document.getElementsByName('agent_address')[0];
        var contactInput = document.getElementsByName('agent_contact')[0];
        var emailInput = document.getElementsByName('agent_email')[0];
        
        // Add real-time validation listeners
        nameInput.addEventListener('input', function() { validateName(this); });
        addressInput.addEventListener('input', function() { validateAddress(this); });
        contactInput.addEventListener('input', function() { validateContact(this); });
        emailInput.addEventListener('input', function() { validateEmail(this); });
    });
    </script>

    <style>
    .is-invalid {
        border-color: #dc3545;
    }
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    </style>

    <?php include '../includes/footer.php'; ?>
</body>
</html> 