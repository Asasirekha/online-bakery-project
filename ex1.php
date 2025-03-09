<?php
// Retrieve cake name and price from hidden inputs
$cakename = $_POST['cakename'];
$price = $_POST['price'];

// Database connection
$servername = "localhost";
$username = "username"; // Your MySQL username
$password = "password"; // Your MySQL password
$dbname = "bakery_orders";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example of inserting into orders table
$sql = "INSERT INTO orders (cake_name, price) VALUES ('$cakename', '$price')";

if ($conn->query($sql) === TRUE) {
    echo "Order placed successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
