<?php
// Database Installation Script

// Configuration variables
$host = 'localhost'; // Database host
$username = ''; // Database username
$password = ''; // Database password
$database = 'jewelry_orders'; // Database name

// Connection status
$connection_success = false;
$database_created = false;
$tables_created = false;
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update configuration with form data
    if (isset($_POST['host'])) $host = $_POST['host'];
    if (isset($_POST['username'])) $username = $_POST['username'];
    if (isset($_POST['password'])) $password = $_POST['password'];
    if (isset($_POST['database'])) $database = $_POST['database'];
    
    // Step 1: Test connection
    try {
        $conn = new mysqli($host, $username, $password);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        $connection_success = true;
        
        // Step 2: Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
        if ($conn->query($sql) === TRUE) {
            $database_created = true;
            $conn->select_db($database);
            
            // Step 3: Create tables
            $tables_sql = [
                // Sales Representatives table
                "CREATE TABLE IF NOT EXISTS `sales_representatives` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL,
                    `email` VARCHAR(100),
                    `phone` VARCHAR(20),
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                // Product Models table
                "CREATE TABLE IF NOT EXISTS `product_models` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL,
                    `description` TEXT,
                    `preview_image` VARCHAR(255),
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                // Orders table
                "CREATE TABLE IF NOT EXISTS `orders` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `sales_rep_id` INT,
                    `client_name` VARCHAR(100) NOT NULL,
                    `client_email` VARCHAR(100),
                    `client_phone` VARCHAR(20),
                    `delivery_date` DATE,
                    `product_model_id` INT,
                    `metal_type` ENUM('Gold', 'Silver', 'Not Applicable') DEFAULT 'Not Applicable',
                    `notes` TEXT,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (`sales_rep_id`) REFERENCES `sales_representatives`(`id`) ON DELETE SET NULL,
                    FOREIGN KEY (`product_model_id`) REFERENCES `product_models`(`id`) ON DELETE SET NULL
                )",
                
                // Order Images table
                "CREATE TABLE IF NOT EXISTS `order_images` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `order_id` INT NOT NULL,
                    `image_path` VARCHAR(255) NOT NULL,
                    `description` VARCHAR(255),
                    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
                )"
            ];
            
            $tables_created = true;
            foreach ($tables_sql as $sql) {
                if ($conn->query($sql) !== TRUE) {
                    $tables_created = false;
                    $error_message = "Error creating tables: " . $conn->error;
                    break;
                }
            }
            
            // Create admin user if tables were created successfully
            if ($tables_created && isset($_POST['admin_username']) && isset($_POST['admin_password'])) {
                $admin_username = $_POST['admin_username'];
                $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
                
                // Users table
                $users_sql = "CREATE TABLE IF NOT EXISTS `users` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `username` VARCHAR(50) NOT NULL UNIQUE,
                    `password` VARCHAR(255) NOT NULL,
                    `role` ENUM('admin', 'staff') DEFAULT 'staff',
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                
                if ($conn->query($users_sql) === TRUE) {
                    // Insert admin user
                    $insert_admin = "INSERT INTO `users` (`username`, `password`, `role`) VALUES (?, ?, 'admin')";
                    $stmt = $conn->prepare($insert_admin);
                    $stmt->bind_param("ss", $admin_username, $admin_password);
                    $stmt->execute();
                } else {
                    $error_message = "Error creating users table: " . $conn->error;
                }
            }
        } else {
            $error_message = "Error creating database: " . $conn->error;
        }
        
        $conn->close();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry Order Management System - Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .step {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .step h2 {
            margin-top: 0;
            color: #444;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .status.success {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
        }
        .status.error {
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Jewelry Order Management System Installation</h1>
        
        <?php if ($tables_created): ?>
            <div class="status success">
                <p><strong>Installation Successful!</strong></p>
                <p>The database and all required tables have been created successfully.</p>
                <p>You can now <a href="index.php">login to your system</a>.</p>
            </div>
        <?php else: ?>
            <?php if ($error_message): ?>
                <div class="status error">
                    <p><strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="step">
                    <h2>Step 1: Database Configuration</h2>
                    <div class="form-group">
                        <label for="host">Database Host:</label>
                        <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($host); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Database Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Database Password:</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                    </div>
                    <div class="form-group">
                        <label for="database">Database Name:</label>
                        <input type="text" id="database" name="database" value="<?php echo htmlspecialchars($database); ?>" required>
                    </div>
                </div>
                
                <div class="step">
                    <h2>Step 2: Admin User Setup</h2>
                    <div class="form-group">
                        <label for="admin_username">Admin Username:</label>
                        <input type="text" id="admin_username" name="admin_username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Admin Password:</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                    </div>
                </div>
                
                <button type="submit">Install System</button>
            </form>
        <?php endif; ?>
        
        <?php if ($connection_success && !$tables_created): ?>
            <div class="status success">
                <p>Database connection successful!</p>
            </div>
        <?php endif; ?>
        
        <?php if ($database_created && !$tables_created): ?>
            <div class="status success">
                <p>Database created successfully!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>