<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/track.css">
</head>

<body>
    <div class="container">
	<div class="row">
	    <h1>Track Page</h1>
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

    if (isset($_GET['track'])) {
	$trackId = $_GET['track'];
	$search_track = $conn->prepare("SELECT TrackName, ArtistTitle, AlbumName, TrackDuration
					FROM Artist NATURAL JOIN Track NATURAL JOIN Album
					WHERE TrackId = ?");
	$search_track->bind_param('s', $trackId);
	$search_track->execute();
	$result = $search_track->get_result();
	echo "<div id=\"info\">";
	while ($row = $result->fetch_assoc()) {
	    echo "<p id=\"trackname\">" . $row['TrackName'] . "</p>";
	    echo "<p id=\"artistTitle\">Artist: " . $row['ArtistTitle'] . "</p>";
	    echo "<p id=\"albumname\">Album: " . $row['AlbumName'] . "</p>";
	    echo "<p id=\"duration\">Duration: " . $row['TrackDuration'] . "ms</p>";
	}
	echo "</div>";
	
	echo "<div id=\"playwindow\">";
	echo "<iframe src=\"https://open.spotify.com/embed?uri=spotify:track:" . $trackId 
		. "\" frameborder=\"0\" width=\"720\" width=\"640\" allowtransparency=\"true\"></iframe>";
	echo "</div>";

	$search_track->close();
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
