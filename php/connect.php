<?php  
$host = 'localhost:3306';  
$user = 'root';  
$pass = '';  
$conn = mysqli_connect($host, $user, $pass);  
if(! $conn )  
{  
  die('Could not connect: ' . mysqli_error());  
}  
echo 'Connected successfully';  
mysqli_close($conn);  
?>  