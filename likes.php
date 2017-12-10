<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['artist']) || $_POST['artist'] == '') {
	header("Location: userInfo.php");
    }
    include('ini_db.php');

    $check_followee = $conn->prepare("SELECT * FROM Artist WHERE ArtistId = ?");
    $check_followee->bind_param('s', $_POST['artist']);
    $check_followee->execute();
    $check_result = $check_followee->get_result();
    if ($check_result->num_rows == 0) {  // artist doesn't exist
	header("Location: userInfo.php");
    } else {
	if ($_POST['action'] == "Like") {
	    $query = $conn->prepare("INSERT INTO `Likes` VALUES (?, ?, ?)");
	    $query->bind_param('sss', $_POST['artist'], $_SESSION['Username'], date('Y-m-d H:i:s', time()));
	    $query->execute();
	} else if ($_POST['action'] == "Unlike") {
	    $query = $conn->prepare("DELETE FROM Likes WHERE Username = ? AND ArtistId = ?");
	    $query->bind_param('ss', $_SESSION['Username'], $_POST['artist']);
	    $query->execute();
	}
    }

    header("Location: artist.php?artist=" . $_POST['artist']);
?>
