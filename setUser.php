<html>
<title>User information page</title>
<?php
$servername = "localhost";
$username = "root";
$password = "A123456j*";
$dbname = "SJBOX";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
echo $conn->connect_error;
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error());
}
//echo "Connected successfully";

//print_r($_POST);
$u_name = $_POST["uname"];
$name = $_POST["name"];
$password = $_POST["password"];
$email = $_POST["email"];
$city = $_POST["city"];
$checkDup= false;


if(isset($u_name)){
    $u = $conn->prepare("SELECT * FROM User WHERE Username = ?");
    //echo $u;
    $u->bind_param('s', $u_name);
    $u->execute();
    $r = $u->get_result();
    if(!$r){
       die("Invalid query: ".mysql_error());
    }
    else{
	if($r->num_rows > 0){
	    echo "<script>alert('This user name has been used.');</script>";
	    echo "<script>window.location.href = 'register.php';</script>";
	    $checkDup = true;
	}
    }
}

if(!$checkDup){
    $r = $conn->prepare("INSERT INTO User VALUES(?, ?, ?, ?, ?)");
    $r->bind_param('sssss', $u_name, $name, $email, $city, $password);
    $r->execute();
    echo "<script>alert('Success! Please login again.');</script>";
    echo "<script>window.location.href = 'login.php';</script>";
    $r->close();
}

$conn->close();
?>
</html>
