<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_GET['artist']) || $_GET['artist'] == '') {
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
    <link rel="stylesheet" href="./CSS/artist.css">
</head>

<body>
<?php 
    include("./includes/navigation_bar.html"); 

    // get artist info
    $artistId = $_GET['artist'];
    $artist_info = $conn->prepare("SELECT * 
				   FROM Artist 
				   WHERE ArtistId = ?");
    $artist_info->bind_param('s', $artistId);
    $artist_info->execute();
    $info_result = $artist_info->get_result();
    echo "<div id=\"info\">";
    echo "<h1>Artist Page</h1>";
    while ($row = $info_result->fetch_assoc()) {
	echo "<p id=\"artistTitle\"><a href=\"artist.php?artist=" . $artistId . "\">" .$row['ArtistTitle'] . "</a></p>";
	echo "<p id=\"description\">" . $row['ArtistDescription'] . "</p>";
	$artistTitle = $row['ArtistTitle'];
    }
    echo "</div>";
    $artist_info->close();

    // check like status
    $check_like =   "SELECT *
		     FROM Likes
		     WHERE Username = \"" . $_SESSION["Username"] . 
		     "\" AND ArtistId = \"" . $artistId . "\"";
    $check_result = $conn->query($check_like);
    if (($check_result->num_rows) > 0) {
	$status = "Unlike";
    } else {
	$status = "Like";
    }

    echo "<div id=\"likebutton\">";
    echo "<form action=\"likes.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"artist\" value=" . $artistId . ">"; 
    echo "<input type=\"hidden\" name=\"action\" value=" . $status . ">"; 
    echo "<input type=\"submit\" value=" . $status . ">"; 
    echo "</form>";
    echo "</div>";

    // get artist albums
    $albums= $conn->prepare("SELECT DISTINCT AlbumId, AlbumName, AlbumReleaseDate
			     FROM Artist NATURAL JOIN Track NATURAL JOIN Album
			     WHERE ArtistId = ?
			     ORDER BY AlbumReleaseDate DESC, AlbumName");
    $albums->bind_param('s', $artistId);
    $albums->execute();
    $albums_result = $albums->get_result();
    echo "<div id=\"albums\">";
    echo "<h4>" . $artistTitle . " has " . $albums_result->num_rows . " albums:</h4>";
    echo "<table id=\"albumtable\">";
    echo "<tr>";
    echo "<th style=\"width: 10%\"></th>";
    echo "<th style=\"width: 75%\">Album Title</th>";
    echo "<th style=\"width: 15%\">Release Date</th>";
    echo "</tr>";
    $index = 1;
    while ($row = $albums_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td>" . $index++ . "</td>";
	echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	echo "<td>" . $row['AlbumReleaseDate'] . "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $albums->close();

    // get artist tracks
    $tracks= $conn->prepare("SELECT Track.TrackId, TrackName, AlbumId, AlbumName, COUNT(PlayTime) AS PlayCount
			     FROM (Artist NATURAL JOIN Track NATURAL JOIN Album)
			     LEFT OUTER JOIN Play ON Track.TrackId = Play.TrackId
			     WHERE ArtistId = ?
			     GROUP BY Track.TrackId
			     ORDER BY PlayCount DESC
			     LIMIT 20");
    $tracks->bind_param('s', $artistId);
    $tracks->execute();
    $tracks_result = $tracks->get_result();
    echo "<div id=\"tracks\">";
    echo "<h4>" . $artistTitle . "'s Top 20 songs</h4>";
    echo "<table id=\"tracktable\">";
    $index = 1;
    echo "<tr>";
    echo "<th style=\"width: 5%\"></th>";
    echo "<th style=\"width: 55%\">Track Title</th>";
    echo "<th style=\"width: 35%\">Album</th>";
    echo "<th style=\"width: 5%\">Played</th>";
    echo "</tr>";
    while ($row = $tracks_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td>" . $index++ . "</td>";
	echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" . $row['TrackName'] . "</a></td>";
	echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	echo "<td>" . $row['PlayCount']  . "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "<p><a href=\"search.php?keyword=" . $artistTitle . "&searchtype=ArtistTitle\">See full list</a><p>";
    echo "</div>";
    $tracks->close();

    // get similar artist 
    $similar = $conn->prepare("SELECT L1.ArtistId AS aid, L2.ArtistId, Artist.ArtistTitle atitle
			       FROM Likes L1 JOIN Likes L2 ON L1.Username = L2.Username 
			       JOIN Artist ON L1.ArtistId = Artist.ArtistId
			       WHERE L2.ArtistId = ?
			       GROUP BY L1.ArtistId, L2.ArtistId
			       HAVING COUNT(*) >= 3
			       AND L1.ArtistId != L2.ArtistId");
    $similar->bind_param('s', $artistId);
    $similar->execute();
    $similar_result = $similar->get_result();
    echo "<div id=\"tracks\">";
    echo "<h4>Similar Artists:</h4>";
    echo "<table id=\"tracktable\">";
    while ($row = $similar_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['aid'] . "\">" . $row['atitle'] . "</a></td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $similar->close();

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
