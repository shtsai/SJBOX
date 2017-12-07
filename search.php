<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
</head>

<body>
    <div class="container">
	<div class="row">
	    <h1>Home Page</h1>
	</div>
    </div>

    <div class="container" id="searchbar">
	<div class="row">
	    <div class="col-lg-3">
		<div class="input-group custom-search-form">
		    <form action="search.php" method="get">
		    <input type="text" name="keyword" placeholder="Enter anything you like">
		    <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search">Go</span></button>
		    </form>
		</div>
	    </div>
	</div>
    </div>

    <div id=searchresult>
<?php
    $servername = "localhost";
    $username = "root";
    $password = "A123456j*";
    $dbname = "SJBOX";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
    }

    $search_track = $conn->prepare("SELECT TrackName, ArtistTitle 
				    FROM Artist NATURAL JOIN Track
				    WHERE ArtistTitle LIKE ?");

    $keyword = "%" . $_GET['keyword'] . "%";
    echo $keyword;
    $search_track->bind_param('s', $keyword);

    $search_track->execute();
    $result = $search_track->get_result();
    echo $result->num_rows;
    echo "helloworld";

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