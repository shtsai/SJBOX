<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['title']) || $_POST['title'] == '') {
	header("Location: playlist_create.php");
    }
    include('ini_db.php');

    $playlist = $conn->prepare("INSERT INTO Playlist (Username, PlaylistTitle, PlaylistDate, PlaylistStatus) VALUES (?, ?, ?, ?)");
    $playlist->bind_param('ssss', $_SESSION['Username'], $_POST['title'], date('Y-m-d H:i:s', time()), $_POST['status']);
    $playlist->execute();
    $playlist->close();
    $conn->close();

    header("Location: userInfo.php");
?>
