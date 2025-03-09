

<?php
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "bakery";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];

    // Check if the user already exists
    $check_sql = "SELECT * FROM register WHERE firstname = ? AND lastname = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $firstname, $lastname);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "You have already registered. Please try registering with a different name.";
    } else {
        // Proceed with insertion if no user found
        $sql = "INSERT INTO register (firstname, lastname, phone, address, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $firstname, $lastname, $phone, $address, $city, $state, $pincode);

        if ($stmt->execute()) {
            header('location:order1.html');
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
