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
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = json_decode($_POST['cart'], true);
    $phone = $_POST['phone'];

    if (!$cart || !$phone) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Check if phone number exists in the register table
    $stmt = $pdo->prepare("SELECT CONCAT(firstname, ' ', lastname) AS fullname, address FROM register WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userDetails) {
        // Store order data in the orders table
        $stmt = $pdo->prepare("INSERT INTO orders (phone, cakename, price, quantity) VALUES (:phone, :cakename, :price, :quantity)");

        foreach ($cart as $item) {
            $stmt->execute([
                'phone' => $phone,
                'cakename' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }

        // Prepare the response
        $response = [
            'success' => true,
            'orderDetails' => $cart,
            'userDetails' => $userDetails,
            'phone' => $phone
        ];
        
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
