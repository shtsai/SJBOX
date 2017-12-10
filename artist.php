<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/artist.css">
</head>

<body>
<?php include("./includes/navigation_bar.html"); ?>

    <div class="container">
	<div class="row">
	    <h1>Artist Page</h1>
	</div>
    </div>

<?php
    $servername = "localhost";
    $username = "root";
    $password = "A123456j*";
    $dbname = "SJBOX";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['artist'])) {
	// get artist info
	$artistId = $_GET['artist'];
	$artist_info = $conn->prepare("SELECT * 
				       FROM Artist 
				       WHERE ArtistId = ?");
	$artist_info->bind_param('s', $artistId);
	$artist_info->execute();
	$info_result = $artist_info->get_result();
	echo "<div id=\"info\">";
	while ($row = $info_result->fetch_assoc()) {
	    echo "<p id=\"artistTitle\"><a href=\"artist.php?artist=" . $artistId . "\">" .$row['ArtistTitle'] . "</a></p>";
	    echo "<p id=\"description\">" . $row['ArtistDescription'] . "</p>";
	    $artistTitle = $row['ArtistTitle'];
	}
	echo "</div>";
 	$artist_info->close();

	// get artist albums
	$albums= $conn->prepare("SELECT DISTINCT AlbumId, AlbumName, AlbumReleaseDate
				 FROM Artist NATURAL JOIN Track NATURAL JOIN Album
				 WHERE ArtistId = ?
				 ORDER BY AlbumReleaseDate DESC, AlbumName");
	$albums->bind_param('s', $artistId);
	$albums->execute();
	$albums_result = $albums->get_result();
	echo "<div id=\"albums\">";
	echo $artistTitle . " has " . $albums_result->num_rows . " albums:";
	echo "<table id=\"albumtable\">";
	while ($row = $albums_result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	    echo "<td>" . $row['AlbumReleaseDate'] . "</td>";
	    echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
 	$albums->close();

	// get artist tracks
	$tracks= $conn->prepare("SELECT TrackId, TrackName, AlbumId, AlbumName  
				 FROM Artist NATURAL JOIN Track NATURAL JOIN Album
				 WHERE ArtistId = ?
				 LIMIT 20");
	$tracks->bind_param('s', $artistId);
	$tracks->execute();
	$tracks_result = $tracks->get_result();
	echo "<div id=\"tracks\">";
	echo $artistTitle . "'s Top 20 songs";
	echo "<table id=\"tracktable\">";
	while ($row = $tracks_result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" . $row['TrackName'] . "</a></td>";
	    echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	    echo "</tr>";
	}
	echo "</table>";
	echo "<p><a href=\"search.php?keyword=" . $artistTitle . "\">See full list</a><p>";
	echo "</div>";
 	$albums->close();
    }

    $conn->close();

?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
