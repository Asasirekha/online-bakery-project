<?php
// Database connection settings
$host = 'localhost:3306';  // Your database host
$db = 'bakery';  // Your database name
$user = 'root';  // Your database username
$pass = '';  // Your database password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

$orderID = isset($_GET['orderID']) ? intval($_GET['orderID']) : 0;
$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';

if ($orderID > 0) {
    // Fetch order details based on order ID
    $stmt = $pdo->prepare("
        SELECT o.id, o.phone, o.cakename, o.price, o.quantity, CONCAT(r.firstname, ' ', r.lastname) AS fullname, r.address 
        FROM orders o 
        JOIN register r ON o.phone = r.phone 
        WHERE o.id = :orderID
    ");
    $stmt->execute(['orderID' => $orderID]);
    $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($orderDetails) {
        $phone = htmlspecialchars($orderDetails[0]['phone']);
        $fullname = htmlspecialchars($orderDetails[0]['fullname']);
        $address = htmlspecialchars($orderDetails[0]['address']);
    } else {
        $orderDetails = [];
        $fullname = $address = '';
    }
}

if ($phone) {
    // Fetch previous orders based on phone number
    $stmt = $pdo->prepare("
        SELECT o.id, o.cakename, o.price, o.quantity, CONCAT(r.firstname, ' ', r.lastname) AS fullname, r.address 
        FROM orders o 
        JOIN register r ON o.phone = r.phone 
        WHERE o.phone = :phone
    ");
    $stmt->execute(['phone' => $phone]);
    $previousOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $previousOrders = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: auto; padding: 20px; }
        .order-summary { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; }
        .order-summary h2 { margin-top: 0; }
        .order-summary ul { list-style-type: none; padding: 0; }
        .order-summary li { margin-bottom: 10px; }
        .previous-orders { border: 1px solid #ddd; padding: 20px; }
        .previous-orders h2 { margin-top: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        
        <?php if ($orderID > 0 && !empty($orderDetails)): ?>
            <div class="order-summary">
                <h2>Thank you for your order!</h2>
                <p><strong>Full Name:</strong> <?php echo $fullname; ?></p>
                <p><strong>Address:</strong> <?php echo $address; ?></p>
                <p><strong>Phone:</strong> <?php echo $phone; ?></p>
                <h3>Current Order Details:</h3>
                <ul>
                    <?php foreach ($orderDetails as $item): ?>
                        <?php 
                        $itemName = htmlspecialchars($item['cakename']);
                        $itemPrice = htmlspecialchars($item['price']);
                        $itemQuantity = htmlspecialchars($item['quantity']);
                        $itemTotal = $itemPrice * $itemQuantity;
                        ?>
                        <li><?php echo $itemName; ?> - &#8377;<?php echo $itemPrice; ?> x <?php echo $itemQuantity; ?> = &#8377;<?php echo $itemTotal; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p>Sorry, we couldn't find the details for your order.</p>
        <?php endif; ?>

        <form method="post" action="">
            <h2>View Previous Orders</h2>
            <label for="phone">Enter your mobile number:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            <button type="submit">View Previous Orders</button>
        </form>

        <?php if (!empty($previousOrders)): ?>
            <div class="previous-orders">
                <h2>Previous Orders</h2>
                <ul>
                    <?php foreach ($previousOrders as $order): ?>
                        <?php 
                        $orderID = htmlspecialchars($order['id']);
                        $itemName = htmlspecialchars($order['cakename']);
                        $itemPrice = htmlspecialchars($order['price']);
                        $itemQuantity = htmlspecialchars($order['quantity']);
                        $itemTotal = $itemPrice * $itemQuantity;
                        ?>
                        <li>
                            <strong>Order ID:</strong> <?php echo $orderID; ?><br>
                            <?php echo $itemName; ?> - &#8377;<?php echo $itemPrice; ?> x <?php echo $itemQuantity; ?> = &#8377;<?php echo $itemTotal; ?><br>
                            <strong>Full Name:</strong> <?php echo htmlspecialchars($order['fullname']); ?><br>
                            <strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?><br>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p>No previous orders found for this phone number.</p>
        <?php endif; ?>
    </div>
</body>
</html>
