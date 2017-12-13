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
<?php 
    include("./includes/navigation_bar.html");

    $userName = $_SESSION['Username'];
    echo "<div id=\"title\">";
    echo "<h1>Hey " . $userName . "!</h1>";
    echo "Here is the feed we created specially for you!";
    echo "</div>";
    
    //show new tracks by artists they like
    $likes = $conn->prepare("SELECT ArtistId, ArtistTitle, Track.TrackName, Track.TrackId 
			    FROM User NATURAL JOIN Likes NATURAL JOIN Artist NATURAL JOIN Track 
			    LEFT OUTER JOIN Play ON Play.Username = User.Username AND Play.TrackId = Track.TrackId
			    WHERE User.Username = ?
			    GROUP BY Track.TrackId
			    HAVING COUNT(PlayTime) = 0
			    LIMIT 10");
    $likes->bind_param("s", $userName);
    $likes->execute();
    $likes_result = $likes->get_result();
    echo "<div id=\"artist\">";
    echo "<h4>New Tracks by Artists You Like:</h4>";
    echo "<table id=\"artisttable\">";
    echo "<tr>";
    echo "<th style=\"width: 30%\">Artist</th>";
    echo "<th style=\"width: 70%\">Track Name</th>";
    echo "</tr>";
    while ($row = $likes_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" .$row['TrackName'] . "</a></td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $likes->close();

    // show playlist created by people you follow
    $follow= $conn->prepare("SELECT Username2, PlaylistTitle, P.PlaylistId, COUNT(PlayTime) AS playCount
			     FROM Follow F JOIN Playlist P ON Username2 = P.Username
			     LEFT OUTER JOIN Play ON P.PlaylistId = Play.PlaylistId
			     WHERE F.Username1 = ?
    			     GROUP BY P.PlaylistId
			     ORDER BY playCount DESC");
    $follow->bind_param('s', $userName);
    $follow->execute();
    $follow_result = $follow->get_result();
    echo "<div id=\"follow\">";
    echo "<h4>New Playlist Created by Users You Follow:</h4>";
    echo "<table id=\"followtable\">";
    echo "<tr>";
    echo "<th style=\"width: 30%\">Username</th>";
    echo "<th style=\"width: 60%\">Playlist Name</th>";
    echo "<th style=\"width: 10%\">Played</th>";
    echo "</tr>";
    while ($row = $follow_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"followUserInfo.php?name=" . $row['Username2'] . "\">" . $row['Username2'] . "</a></td>";  
	echo "<td><a href=\"playlist.php?playlist=" . $row['PlaylistId'] . "\">" . $row['PlaylistTitle'] . "</a></td>";  
	echo "<td>" . $row['playCount'] . "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $follow->close();

    //show top 10 by rating 
    $rate = "SELECT ArtistId, ArtistTitle, Track.TrackName, Track.TrackId, AVG(Score) AS avgScore
	     FROM Rate NATURAL JOIN Track NATURAL JOIN Artist
	     GROUP BY TrackId
	     ORDER BY avgScore DESC
	     LIMIT 10";
    $rate_result = $conn->query($rate);
    echo "<div id=\"highestrate\">";
    echo "<h4>Top 10 Tracks by User Ratings:</h4>";
    echo "<table id=\"ratetable\">";
    echo "<tr>";
    echo "<th style=\"width: 30%\">Artist</th>";
    echo "<th style=\"width: 60%\">Track Name</th>";
    echo "<th style=\"width: 10%\">Score</th>";
    echo "</tr>";
    while ($row = $rate_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" .$row['TrackName'] . "</a></td>";
	echo "<td>" . $row['avgScore'] . "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

    // Top 10 most played tracks
    $play= "SELECT ArtistId, ArtistTitle, Track.TrackName, Track.TrackId, COUNT(PlayTime) AS playCount 
	     FROM Play NATURAL JOIN Track NATURAL JOIN Artist
	     GROUP BY TrackId
	     ORDER BY playCount DESC
	     LIMIT 10";
    $play_result = $conn->query($play);
    echo "<div id=\"mostplayed\">";
    echo "<h4>Top 10 Most Played Tracks:</h4>";
    echo "<table id=\"playtable\">";
    echo "<tr>";
    echo "<th style=\"width: 30%\">Artist</th>";
    echo "<th style=\"width: 60%\">Track Name</th>";
    echo "<th style=\"width: 10%\">Played</th>";
    echo "</tr>";
    while ($row = $play_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" .$row['TrackName'] . "</a></td>";
	echo "<td>" . $row['playCount'] . "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

    $conn->close();
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
</body>
</html>
