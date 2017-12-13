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
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/search.css">
</head>

<body>
<?php include("includes/navigation_bar.html"); ?>
    <div id="title">
	<h1>Search Page</h1>
    </div>

    <div id="search">
	<form action="search.php" method="get">
	    <input id="searchbar" type="text" name="keyword" placeholder="Enter anything you like">
	    <button id="searchbutton" type="submit">&#9906</button><br>
	    <label for="option1"><input id="option1" type="radio" name="searchtype" value="TrackName" checked/>Track</label>
	    <label for="option2"><input id="option2" type="radio" name="searchtype" value="ArtistTitle" />Artist</label>
	    <label for="option3"><input id="option3" type="radio" name="searchtype" value="AlbumName" />Album</label>
	    <label for="option4"><input id="option4" type="radio" name="searchtype" value="Username" />User</label>
	</form>
    </div>
    <div id="searchresult">
<?php
    if (isset($_GET['searchtype']) && $_GET['searchtype'] == "Username") {
	$search_user= $conn->prepare("SELECT * 
				      FROM User 
				      WHERE Username LIKE ? 
				      OR Name LIKE ?
				      OR City LIKE ?");
	$keyword = "%" . $_GET['keyword'] . "%";
	$search_user->bind_param('sss', $keyword, $keyword, $keyword);
	$search_user->execute();
	$result = $search_user->get_result();
	echo $result->num_rows . " results:";
	echo "<table id=\"resultTable\">";
	echo "<tr>";
	echo "<th>Username</th>";
	echo "<th>Name</th>";
	echo "<th>City</th>";
	echo "</tr>";
	while ($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><a href=\"followUserInfo.php?name=" . $row['Username'] . "\">" . $row['Username'] . "</a></td>";
	    echo "<td>" . $row['Name'] . "</td>";
	    echo "<td>" .$row['City'] . "</td>";
	    echo "</tr>";
	}	
	echo "</table>";
	$search_user->close();
    
    } else if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
	$searchtype = $_GET['searchtype'];
	$search_track = $conn->prepare("SELECT TrackId, TrackName, ArtistTitle, ArtistId, AlbumId, AlbumName
					FROM Artist NATURAL JOIN Track NATURAL JOIN Album
					WHERE " . $searchtype . " LIKE ?");
	$keyword = "%" . $_GET['keyword'] . "%";
	$search_track->bind_param('s', $keyword);
	$search_track->execute();
	$result = $search_track->get_result();
	echo $result->num_rows . " results:";
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
	$search_track->close();
    
    } else {
	echo "Welcome to SJBOX! Start by searching your favoriate songs or artists.";    
	echo "<div id=\"post\">";
	echo "<img src=\"images/poster.png\" alt=\"poster\">";
	echo "</div>";
    }

    $conn->close();

    include("./includes/footer.html");
?>
    </div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
