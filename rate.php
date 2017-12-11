<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['track']) || $_POST['track'] == '') {
	header("Location: userInfo.php");
    }
    include('ini_db.php');

    $check_rate = $conn->prepare("SELECT * FROM Rate WHERE Username = ? AND TrackId = ?");
    $check_rate->bind_param('ss', $_SESSION['Username'], $_POST['track']);
    $check_rate->execute();
    $check_result = $check_rate->get_result();
    if ($check_result->num_rows != 0) {  // followee doesn't exist
	$delete_query = $conn->prepare("DELETE FROM Rate WHERE Username = ? AND TrackId = ?");
	$delete_query->bind_param('ss', $_SESSION['Username'], $_POST['track']);
	$delete_query->execute();
    } 
    $query = $conn->prepare("INSERT INTO `Rate` VALUES (?, ?, ?, ?)");
    $query->bind_param('ssss', $_SESSION['Username'], $_POST['track'], $_POST['score'], date('Y-m-d H:i:s', time()));
    $query->execute();

    header("Location: track.php?track=" . $_POST['track']);
?>
