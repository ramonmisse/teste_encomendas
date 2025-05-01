<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?page=orders">Orders</a></li>
                    <li><a href="index.php?page=new-order">New Order</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="index.php?page=admin">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="actions/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>

    </main>
</body>
</html>