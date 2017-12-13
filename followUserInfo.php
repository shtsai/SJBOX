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

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/followUserInfo.css">
</head>

<body>
<?php 
    include("./includes/navigation_bar.html"); 

    if (!isset($_GET['name']) || $_GET['name'] == "" || $_GET['name'] == $_SESSION['Username']) {
	$userName = $_SESSION['Username'];
	echo "<div id=\"title\">";
	echo "<h1>" . $userName . "'s Page</h1>";
	echo "</div><br><br><br>";
	
    } else {
	$userName = $_GET['name']; 

	// check whether following or not
	$check_follow = "SELECT *
			 FROM Follow
			 WHERE Username1 = \"" . $_SESSION["Username"] . 
			 "\" AND Username2 = \"" . $_GET["name"] . "\"";
	$check_result = $conn->query($check_follow);
	if (($check_result->num_rows) > 0) {
	    $status = "Unfollow";
	} else {
	    $status = "Follow";
	}
	echo "<div id=\"title\">";
	echo "<h1>" . $userName . "'s Page</h1>";
	echo "<div id=\"followbutton\">"; 
	echo "<form action=\"follow.php\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"followee\" value=" . $_GET["name"] . ">"; 
	echo "<input type=\"hidden\" name=\"action\" value=" . $status . ">"; 
	echo "<input type=\"submit\" value=" . $status . ">"; 
	echo "</form>";
	echo "</div>";
	echo "</div><br>";

    }
    //show user info 
    $r1 = $conn->prepare("SELECT * FROM User WHERE Username = ?");
    $r1->bind_param('s', $userName);
    $r1->execute();
   
    $info_result = $r1->get_result();
    echo "<div id=\"info\">";
    while ($row = $info_result->fetch_assoc()) {
	echo "<p>Name: " . $row['Name'] . "</p>";
	echo "<p>Email: " . $row['Email'] . "</p>";
	echo "<p>City: " . $row['City'] . "</p>";
    }
    echo "</div>";
    $r1->close();
    

    //show likes
    $likes = $conn->prepare("SELECT * 
			    FROM Likes NATURAL JOIN Artist
			    WHERE Username = ?");
    $likes->bind_param('s', $userName);
    $likes->execute();
    $likes_result = $likes->get_result();
    echo "<div id=\"artist\">";
    echo "<h4>The artists " . $userName . " likes: </h4>";
    echo "<table id=\"artisttable\">";
    echo "<tr>";
    echo "<th style=\"width: 25%\">Artist</th>";
    echo "<th style=\"width: 75%\">Description</th>";
    echo "</tr>";

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
			     FROM Follow
			     WHERE Username1 = ?");
    $follow->bind_param('s', $userName);
    $follow->execute();
    $follow_result = $follow->get_result();
    echo "<div id=\"follow\">";
    echo "<h4>The users " . $userName . " follows:</h4>";
    echo "<table id=\"followtable\">";
    while ($row = $follow_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"followUserInfo.php?name=" . $row['Username2'] . "\">" . $row['Username2'] . "</a></td>";  
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $follow->close();

    //show playlist 
    $playlist= $conn->prepare("SELECT *  
			       FROM Playlist
			       WHERE Username = ?");
    $playlist->bind_param('s', $userName);
    $playlist->execute();
    $playlist_result = $playlist->get_result();
    echo "<div id=\"playlist\">";
    echo "<h4>Your playlists:</h4>";
    if ($userName == $_SESSION['Username']) {
	echo "<div id=\"followbutton\">"; 
	echo "<form action=\"playlist_create.php\" method=\"get\">";
	echo "<input type=\"submit\" value=\"Create Playlist\">"; 
	echo "</form>";
	echo "</div>";
    }
    echo "<table id=\"playlisttable\">";
    while ($row = $playlist_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"playlist.php?playlist=" . $row['PlaylistId'] . "\">" . $row['PlaylistTitle'] . "</a></td>";  
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $playlist->close();

    $conn->close();
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
