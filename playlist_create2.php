<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['title']) || $_POST['title'] == '') {
	header("Location: playlist_create.php");
    }
    include('ini_db.php');

    $playlist = $conn->prepare("INSERT INTO Playlist (PlaylistId, Username, PlaylistTitle, PlaylistDate, PlaylistStatus) VALUES (?, ?, ?, ?, ?)");
    $date = date('Y-m-d H:i:s', time());
    $playlistId = $_SESSION['Username'] . $date;
    $playlist->bind_param('sssss', $playlistId, $_SESSION['Username'], htmlspecialchars($_POST['title']), $date, $_POST['status']);
    $playlist->execute();
    $playlist->close();

    $conn->close();

    header("Location: playlist.php?playlist=" . $playlistId);
?>
