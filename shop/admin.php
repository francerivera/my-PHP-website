<?php
session_start();
include("connections.php");

// Redirect if not logged in or not an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["account_type"] != "1") {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION["user_id"];
$error = "";
$success = "";

// Handle form submissions (delete, update, add)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete user
    if (isset($_POST["delete"])) {
        $user_id = (int)$_POST["user_id"];
        if ($user_id == $admin_id) {
            $error = "You cannot delete your own account.";
        } else {
            $delete_query = "DELETE FROM users WHERE id = ?";
            $delete_stmt = $connections->prepare($delete_query);
            $delete_stmt->bind_param("i", $user_id);
            if ($delete_stmt->execute()) {
                $success = "User deleted successfully!";
            } else {
                $error = "Failed to delete user.";
            }
            $delete_stmt->close();
        }
    }

    // Update user (including password)
    if (isset($_POST["update"])) {
        $user_id = (int)$_POST["user_id"];
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $account_type = $_POST["account_type"];
        $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

        // Validate inputs
        if (empty($name) || empty($email) || !in_array($account_type, ["1", "2"])) {
            $error = "Please fill in all fields correctly.";
        } else {
            // Check if email is already in use by another user
            $email_query = "SELECT id FROM users WHERE email = ? AND id != ?";
            $email_stmt = $connections->prepare($email_query);
            $email_stmt->bind_param("si", $email, $user_id);
            $email_stmt->execute();
            $email_result = $email_stmt->get_result();
            if ($email_result->num_rows > 0) {
                $error = "Email is already in use by another user.";
            } else {
                if (!empty($password)) {
                    // Store the password in plain text
                    $update_query = "UPDATE users SET name = ?, email = ?, account_type = ?, password = ? WHERE id = ?";
                    $update_stmt = $connections->prepare($update_query);
                    $update_stmt->bind_param("sssis", $name, $email, $account_type, $password, $user_id);
                } else {
                    $update_query = "UPDATE users SET name = ?, email = ?, account_type = ? WHERE id = ?";
                    $update_stmt = $connections->prepare($update_query);
                    $update_stmt->bind_param("sssi", $name, $email, $account_type, $user_id);
                }
                if ($update_stmt->execute()) {
                    $success = "User updated successfully!" . (!empty($password) ? " New password set." : "");
                } else {
                    $error = "Failed to update user.";
                }
                $update_stmt->close();
            }
            $email_stmt->close();
        }
    }

    // Add new user
    if (isset($_POST["add_user"])) {
        $name = trim($_POST["new_name"]);
        $email = trim($_POST["new_email"]);
        $password = $_POST["new_password"];
        $account_type = $_POST["new_account_type"];

        // Validate inputs
        if (empty($name) || empty($email) || empty($password) || !in_array($account_type, ["1", "2"])) {
            $error = "Please fill in all fields correctly.";
        } else {
            // Check if email is already in use
            $email_query = "SELECT id FROM users WHERE email = ?";
            $email_stmt = $connections->prepare($email_query);
            $email_stmt->bind_param("s", $email);
            $email_stmt->execute();
            $email_result = $email_stmt->get_result();
            if ($email_result->num_rows > 0) {
                $error = "Email is already in use.";
            } else {
                // Store the password in plain text
                $insert_query = "INSERT INTO users (name, email, password, account_type) VALUES (?, ?, ?, ?)";
                $insert_stmt = $connections->prepare($insert_query);
                $insert_stmt->bind_param("ssss", $name, $email, $password, $account_type);
                if ($insert_stmt->execute()) {
                    $success = "New user added successfully!";
                } else {
                    $error = "Failed to add new user.";
                }
                $insert_stmt->close();
            }
            $email_stmt->close();
        }
    }
}

// Fetch all users from the database
$query = "SELECT id, name, email, account_type, password, created_at FROM users ORDER BY created_at DESC";
$result = $connections->query($query);
if (!$result) {
    die("Query failed: " . $connections->error);
}
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - France Rivera Manila</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .add-user-form {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .add-user-form h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .error {
            color: red;
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: rgb(100, 100, 100);
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
        }
        table td {
            color: #333;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .account-type {
            font-weight: bold;
        }
        .account-type.admin {
            color: #e74c3c; /* Red for admin */
        }
        .account-type.user {
            color: #2ecc71; /* Green for regular user */
        }
        .action-buttons form {
            display: inline-block;
            margin-right: 5px;
        }
        .action-buttons button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .action-buttons .edit-btn {
            background-color: #3498db;
            color: white;
        }
        .action-buttons .edit-btn:hover {
            background-color: #2980b9;
        }
        .action-buttons .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
        .action-buttons .delete-btn:hover {
            background-color: #c0392b;
        }
        .no-users {
            text-align: center;
            color: #666;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        .modal-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: #333;
            margin-bottom: 15px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5em;
            color: #666;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            table th, table td {
                padding: 8px;
                font-size: 0.9em;
            }
            .action-buttons button {
                padding: 4px 8px;
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?>

    <div class="container">
        <h2>Admin Dashboard - User Management</h2>

        <?php if ($error) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="success"><?php echo $success; ?></div>
        <?php } ?>

        <!-- Add New User Form -->
        <div class="add-user-form">
            <h3>Add New User</h3>
            <form method="POST" action="admin.php">
                <div class="form-group">
                    <label for="new_name">Name</label>
                    <input type="text" id="new_name" name="new_name" required>
                </div>
                <div class="form-group">
                    <label for="new_email">Email</label>
                    <input type="email" id="new_email" name="new_email" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password</label>
                    <input type="text" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="new_account_type">Account Type</label>
                    <select id="new_account_type" name="new_account_type" required>
                        <option value="1">Admin</option>
                        <option value="2" selected>User</option>
                    </select>
                </div>
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>

        <!-- User Table -->
        <?php if (empty($users)) { ?>
            <div class="no-users">No users found.</div>
        <?php } else { ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Account Type</th>
                        <th>Password</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="account-type <?php echo $user['account_type'] == '1' ? 'admin' : 'user'; ?>">
                                    <?php echo $user['account_type'] == '1' ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['password']); ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($user['created_at'])); ?></td>
                            <td class="action-buttons">
                                <button class="edit-btn" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>', '<?php echo $user['account_type']; ?>')">Edit</button>
                                <form method="POST" action="admin.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">×</span>
            <h3>Edit User</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit_account_type">Account Type</label>
                    <select id="edit_account_type" name="account_type" required>
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_password">New Password (Leave blank to keep unchanged)</label>
                    <input type="text" id="edit_password" name="password" placeholder="Enter new password">
                </div>
                <button type="submit" name="update">Update User</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(userId, name, email, accountType) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_account_type').value = accountType;
            document.getElementById('edit_password').value = ''; // Clear password field
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>