<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_GET['track']) || $_GET['track'] == '') {
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
    <link rel="stylesheet" href="./CSS/track.css">
</head>

<body>
<?php include("./includes/navigation_bar.html"); ?>

    <div class="container">
	<div class="row">
	</div>
    </div>

<?php
    // get track info
    $trackId = $_GET['track'];
    $search_track = $conn->prepare("SELECT TrackName, ArtistTitle, ArtistId, AlbumName, AlbumId, TrackDuration
				    FROM Artist NATURAL JOIN Track NATURAL JOIN Album
				    WHERE TrackId = ?");
    $search_track->bind_param('s', $trackId);
    $search_track->execute();
    $result = $search_track->get_result();
    echo "<div id=\"info\">";
    while ($row = $result->fetch_assoc()) {
	echo "<h1 style=\"font-weight: bold\">" . $row['TrackName'] . "</h1>";
	echo "<p id=\"artistTitle\">Artist: <a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></p>";
	echo "<p id=\"albumname\">Album: <a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></p>";
	$seconds = $row['TrackDuration'] / 1000;
	$minutes = intdiv($seconds, 60);
	$seconds = $seconds % 60;
	echo "<p id=\"duration\">Duration: " . $minutes . "m " . $seconds . "s</p>";
    }
    echo "</div>";
    
    // show rating info
    echo "<div id=\"rating\">";
    echo "<p>Average Rating: </p>";
    $avg_rating = $conn->prepare("SELECT AVG(Score) AS avgScore
				  FROM Rate 
				  WHERE TrackId = ?
				  GROUP BY TrackId");
    $avg_rating->bind_param('s', $trackId);
    $avg_rating->execute();
    $avg_result = $avg_rating->get_result();
    $avgScore = 0;
    while ($row = $avg_result->fetch_assoc()) {
	$avgScore = $row['avgScore'];
    }
    for ($i = 0; $i < $avgScore; $i++) {
	echo "&#9733";
    }
    for ($i = $avgScore; $i < 10; $i++) {
	echo "&#9734";
    }
    $rating_check = $conn->prepare("SELECT * 
				    FROM Rate 
				    WHERE Username = ? 
				    AND TrackId = ? 
				    ORDER BY RateTime");
    $rating_check->bind_param('ss', $_SESSION['Username'], $trackId);
    $rating_check->execute();
    $rating_result = $rating_check->get_result();
    $score = 0;
    while ($row = $rating_result->fetch_assoc()) {
	$score = $row['Score'];
    } 
    $search_track->close();

    if ($score == 0) {
	echo "<p>You haven't rated this track.</p>";
    } else {
	echo "<p>Your rating for this track is " . $score . "</p>";
    }
    echo "<form action=\"rate.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"track\" value=" . $trackId  . ">";
    echo "<select name=\"score\">";
    echo "<option value=10>10</option>";
    echo "<option value=9>9</option>";
    echo "<option value=8>8</option>";
    echo "<option value=7>7</option>";
    echo "<option value=6>6</option>";
    echo "<option value=5>5</option>";
    echo "<option value=4>4</option>";
    echo "<option value=3>3</option>";
    echo "<option value=2>2</option>";
    echo "<option value=1>1</option>";
    echo "</select>";
    echo "<input type=\"submit\" value=\"Rate\">";
    echo "</form>";
    echo "</div>";

    // show play window
    echo "<div id=\"playwindow\">";
    echo "<iframe src=\"https://open.spotify.com/embed?uri=spotify:track:" . $trackId 
	    . "\" frameborder=\"0\" width=\"720\" width=\"640\" allowtransparency=\"true\"></iframe>";
    echo "</div>";

    // show add to playlist
    $playlist = $conn->prepare("SELECT *
			        FROM Playlist
			        WHERE Username = ?");
    $playlist->bind_param('s', $_SESSION['Username']);
    $playlist->execute();
    $playlist_result = $playlist->get_result();
    if ($playlist_result->num_rows > 0) {
	echo "<div id=\"playlist\">";
	echo "Add to your playlists:";
	echo "<table>";
	while ($row = $playlist_result->fetch_assoc()) {
	    $check_exist = $conn->prepare("SELECT * 
					   FROM PlaylistSong 
					   WHERE PlaylistId = ? AND TrackId = ?");
	    $check_exist->bind_param('ss', $row['PlaylistId'], $trackId);
	    $check_exist->execute();
	    $check_result = $check_exist->get_result();
	    if ($check_result->num_rows > 0) {
		$status = "Remove";
		$sign = "&#10005";
	    } else {
		$status = "Add";
		$sign = "+";
	    }
	    echo "<tr>";
	    echo "<td><form action=\"playlist_add.php\" method=\"post\">";  
	    echo "<input type=\"hidden\" name=\"playlist\" value=\"". $row['PlaylistId'] . "\">";
	    echo "<input type=\"hidden\" name=\"track\" value=\"". $trackId . "\">";
	    echo "<input type=\"hidden\" name=\"action\" value=\"". $status . "\">";
	    echo "<input type=\"submit\" value=\"" . $sign . "\">";
	    echo "</form></td>";
	    echo "<td ><a href=\"playlist.php?playlist=" . $row['PlaylistId'] . "\">" .$row['PlaylistTitle'] . "</a>";
	    echo "</tr>";
	    $check_exist->close();
	}
	echo "</table>";
	echo "</div>";
    }
    $playlist->close();

    // add play record
    $check_playlist = $conn->prepare("SELECT * 
				      FROM Playlist
				      WHERE PlaylistId = ?");
    $check_playlist->bind_param('s', $_GET['playlist']);
    $check_playlist->execute();
    $check_playlist_result = $check_playlist->get_result();
    if ($check_playlist_result->num_rows > 0) {  // valid playlist
	$playlistId = $_GET['playlist'];
    } else {
	$playlistId = NULL;
    } 
    $currenttime = date('Y-m-d H:i:s', time());
    $add_play = $conn->prepare("INSERT INTO Play VALUES (?, ?, ?, ?)");
    $add_play->bind_param('ssss', $_SESSION['Username'], $trackId, $currenttime,$playlistId);
    $add_play->execute();
    $add_play->close();
    $check_playlist->close();
    $conn->close();

    include("./includes/footer.html");
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
