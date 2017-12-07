<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/search.css">
</head>

<body>
    <div class="container">
	<div class="row">
	    <h1>Search Page</h1>
	</div>
    </div>

    <div class="container" id="searchbar">
	<form action="search.php" method="get">
	    <input type="text" name="keyword" placeholder="Enter anything you like">
	    <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search">Go</span></button>
	</form>
    </div>
    <div id="searchresult">
<?php
    $servername = "localhost";
    $username = "root";
    $password = "A123456j*";
    $dbname = "SJBOX";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
    }

    $search_track = $conn->prepare("SELECT TrackId, TrackName, ArtistTitle, ArtistId, AlbumId, AlbumName
				    FROM Artist NATURAL JOIN Track NATURAL JOIN Album
				    WHERE ArtistTitle LIKE ?");

    if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
	$keyword = "%" . $_GET['keyword'] . "%";
	$search_track->bind_param('s', $keyword);
	$search_track->execute();
	$result = $search_track->get_result();
	echo $result->num_rows . "results:";
	echo "<table id=\"resultTable\">";
	echo "<tr>";
	echo "<th>Track Name</th>";
	echo "<th>Album</th>";
	echo "<th>Artist</th>";
	echo "</tr>";
	while ($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" . $row['TrackName'] . "</a></td>";
	    echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	    echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	    echo "</tr>";
	}	
	echo "</table>";
    
    } else {
	echo "Welcome to SJBOX! Start by searching your favoriate songs or artists.";    
    }

    $search_track->close();
    $conn->close();

?>
    </div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
