<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['playlist']) || $_POST['playlist'] == '' 
               || !isset($_POST['track']) || $_POST['track'] == '') {
	header("Location: userInfo.php");
    }
    include('ini_db.php');

    $remove= $conn->prepare("DELETE FROM PlaylistSong WHERE PlaylistId = ? AND TrackId = ?");
    $remove->bind_param('ss', $_POST['playlist'], $_POST['track']);
    $remove->execute();
    $remove->close(); 
    $conn->close();
    header("Location: playlist.php?playlist=" . $_POST['playlist']);
?>
