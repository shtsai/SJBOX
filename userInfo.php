<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    }
    include('ini_db.php');
?>

<!doctype html>
<html lang="en">
<head>
    <title>Info</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/userInfo.css"> 
</head>
<body>
<?php include("./includes/navigation_bar.html"); ?>

    <div id="title">
	<?php echo "<h1>Hello! " . $_SESSION['Username'] . "</h1>"; ?>
    </div>

<?php
    // get userName from session
    $userName = $_SESSION['Username'];

    //show user info 
    $r1 = $conn->prepare("SELECT * FROM User WHERE Username = ?");
    $r1->bind_param('s', $userName);
    $r1->execute();
   
    $info_result = $r1->get_result();
    echo "<div id=\"info\">";
    while ($row = $info_result->fetch_assoc()) {
	echo "<p>Username: " . $row['Username'] . "</p>";
	echo "<p>Name: " . $row['Name'] . "</p>";
	echo "<p>Email: " . $row['Email'] . "</p>";
	echo "<p>City: " . $row['City'] . "</p>";
    }
    echo "</div>";
    $r1->close();

    
    //show likes
    $likes = $conn->prepare("SELECT ArtistId, ArtistTitle, ArtistDescription
			    FROM User NATURAL JOIN Likes NATURAL JOIN Artist
			    WHERE Username = ?");
    $likes->bind_param("s", $userName);
    $likes->execute();
    $likes_result = $likes->get_result();
    echo "<div id=\"artist\">";
    echo "The artists you like: ";
    echo "<table id=\"artisttable\">";

    while ($row = $likes_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	echo "<td>" . $row['ArtistDescription'] . "</td>"; 
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $likes->close();

    //show follow user
    $follow= $conn->prepare("SELECT Username2  
			     FROM User, Follow
			     WHERE Username1 = ? AND Username = Follow.Username1");
    $follow->bind_param('s', $userName);
    $follow->execute();
    $follow_result = $follow->get_result();
    echo "<div id=\"follow\">";
    echo "The user you follow:";
    echo "<table id=\"followtable\">";
    while ($row = $follow_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"followUserInfo.php?name=" . $row['Username2'] . "\">" . $row['Username2'] . "</a></td>";  
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $follow->close();
    $conn->close();
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
</body>
</html>
