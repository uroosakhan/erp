<?PHP 

$servername = "localhost";
$username = "cloudso1_demo";
$password = "myz47m";
$dbname = "cloudso1_demo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "INSERT INTO 0_info (name, email, message)
VALUES ('".$_POST['name']."', '".$_POST['email']."', '".$_POST['message']."')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>