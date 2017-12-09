<!doctype html>
<html lang="en">
<head>
    <title>Info</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <!-- login CSS -->
      <style><?php include 'CSS/login.css'; ?></style> 

    <!--<link rel="stylesheet" href="./CSS/user.css">-->
</head>

<body>
    <ul class="nav nav-tabs">
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          Back to<span class="caret"></span>
        </a>
        <ul class="dropdown-menu"><a href="search.php">Search page</a>

        </ul>
      </li>
    </ul>

    <div class="container">
	<div class="row">
	    <h1>User Page</h1>
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
    session_start();
    echo "The user of this section is: " . $_SESSION['Username'];

    if (isset($_GET['name'])) {
	// get artist info
	$userName = $_GET['name'];

        //show user info 
        $r1 = $conn->prepare("SELECT * FROM User WHERE Username = ?");
        $r1->bind_param('s', $userName);
        $r1->execute();
       
        $info_result = $r1->get_result();
        //echo "<div id=\"info\">";
        while ($row = $info_result->fetch_assoc()) {
            echo "<p>" . $row['Name'] . "!</p>";
            //echo "<p id=\"description\">" . $row['ArtistDescription'] . "</p>";
            //$artistTitle = $row['ArtistTitle'];
        }
        //echo "</div>";
        $r1->close();
        

        //show likes
        $likes = $conn->prepare("SELECT ArtistTitle, ArtistDescription
                                FROM User NATURAL JOIN Likes NATURAL JOIN Artist
                                WHERE uname = ?");
        $likes->bind_param('s', $userName);
        $likes->execute();
        $likes_result = $likes->get_result();
        echo "<div id=\"albums\">";
        echo "The artist you like: ";
        echo "<table id=\"albumtable\">";

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
                                 FROM User NATURAL JOIN Follow
                                 WHERE Username1 = ?");
        $follow->bind_param('s', $userName);
        $follow->execute();
        $follow_result = $follow->get_result();
        echo "<div id=\"follow\">";
        echo "The user you follow:";
        echo "<table id=\"followtable\">";
        while ($row = $follow_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><a href=\"FollowUserInfo.php?track=" . $row['Username2'] . "\">" . $row['UserName'] . "</a></td>";  //here need to change
            //echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        //echo "<p><a href=\"search.php?keyword=" . $artistTitle . "\">See full list</a><p>";
        echo "</div>";
        $follow->close();
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
