<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change to your database username
define('DB_PASS', ''); // Change to your database password
define('DB_NAME', 'jewelry_orders');

// Create database and tables
try {
    echo "<h2>Setting up database...</h2>";
    
    // Connect to MySQL server without selecting a database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>Database created or already exists.</p>";
    
    // Select the database
    $pdo->exec("USE `" . DB_NAME . "`");
    
    // Create sales_representatives table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sales_representatives` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `avatar_url` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p>Sales representatives table created.</p>";
    
    // Create product_models table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `product_models` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `image_url` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p>Product models table created.</p>";
    
    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sales_representative_id` int(11) NOT NULL,
        `client_name` varchar(100) NOT NULL,
        `delivery_date` date NOT NULL,
        `model_id` int(11) NOT NULL,
        `metal_type` varchar(20) NOT NULL,
        `notes` text DEFAULT NULL,
        `image_urls` text DEFAULT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `sales_representative_id` (`sales_representative_id`),
        KEY `model_id` (`model_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p>Orders table created.</p>";
    
    // Add foreign key constraints if they don't exist
    try {
        $pdo->exec("ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`sales_representative_id`) REFERENCES `sales_representatives` (`id`)");
        $pdo->exec("ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `product_models` (`id`)");
        echo "<p>Foreign key constraints added.</p>";
    } catch (PDOException $e) {
        echo "<p>Foreign key constraints already exist or could not be added: " . $e->getMessage() . "</p>";
    }
    
    // Create uploads directory if it doesn't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
        chmod('uploads', 0777);
        echo "<p>Uploads directory created.</p>";
    } else {
        echo "<p>Uploads directory already exists.</p>";
    }
    
    echo "<h2>Database setup completed successfully!</h2>";
    echo "<p>You can now <a href='index.php'>return to the application</a>.</p>";
    
} catch(PDOException $e) {
    die("<h2>ERROR: Could not set up database.</h2><p>" . $e->getMessage() . "</p>");
}
?>
