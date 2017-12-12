<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_GET['playlist']) || $_GET['playlist'] == '') {
	header("location: userInfo.php");
    }
    include('ini_db.php');
?>

<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/playlist.css">
</head>

<body>
<?php include("./includes/navigation_bar.html"); ?>

    <div class="container">
	<div class="row">
	    <h1>Playlist Page</h1>
	</div>
    </div>

<?php
    // get creator info
    $playlistId = $_GET['playlist'];
    $playlist_info = $conn->prepare("SELECT *
				     FROM Playlist 
				     WHERE PlaylistId = ?");
    $playlist_info->bind_param('s', $playlistId);
    $playlist_info->execute();
    $info_result = $playlist_info->get_result();
    $row = $info_result->fetch_assoc();
    if ($info_result->num_rows == 0) {
	echo "<p id=\"error\">This playlist doesn't exist.</p>";
    } else if ($row['PlaylistStatus'] == "private" && $row['Username'] != $_SESSION['Username']) {
	echo "<p id=\"error\">Sorry, this is a private playlist.</p>";
    } else {
	echo "<div id=\"info\">";
	echo "<p id=\"playlistname\"><a href=\"playlist.php?playlist=" . $playlistId . "\">" .$row['PlaylistTitle'] . "</a></p>";
	echo "<p id=\"creator\">Created by: <a href=\"followUserInfo.php?name=" . $row['Username'] . "\">" .$row['Username'] . "</a></p>";
	echo "<p id=\"date\">Create date: " . $row['PlaylistDate'] . "</p>";
	echo "<p id=\"status\">Status: " . $row['PlaylistStatus'] . "</p>";
	echo "</div>";

	// get playlist tracks
	$tracks= $conn->prepare("SELECT *
				 FROM Playlist NATURAL JOIN PlaylistSong NATURAL JOIN Track
				 WHERE PlaylistId = ?");
	$tracks->bind_param('s', $playlistId);
	$tracks->execute();
	$tracks_result = $tracks->get_result();
	echo "<div id=\"tracks\">";
	echo "Here are the tracks in this playlist.";
	echo "<table id=\"tracktable\">";
	while ($row = $tracks_result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" . $row['TrackName'] . "</a></td>";
	    echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	$tracks->close();
  
    }
    $playlist_info->close();
    $conn->close();

?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
