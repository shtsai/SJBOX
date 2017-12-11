<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_POST['followee']) || $_POST['followee'] == '') {
	header("Location: userInfo.php");
    }
    include('ini_db.php');

    $check_followee = $conn->prepare("SELECT * FROM User WHERE Username = ?");
    $check_followee->bind_param('s', $_POST['followee']);
    $check_followee->execute();
    $check_result = $check_followee->get_result();
    if ($check_result->num_rows == 0) {  // followee doesn't exist
	header("Location: userInfo.php");
    } else {
	if ($_POST['action'] == "Follow") {
	    $query = $conn->prepare("INSERT INTO `Follow` VALUES (?, ?, ?)");
	    $query->bind_param('sss', $_SESSION['Username'], $_POST['followee'],  date('Y-m-d H:i:s', time()));
	    $query->execute();
	    $query->close();
	} else if ($_POST['action'] == "Unfollow") {
	    $query = $conn->prepare("DELETE FROM Follow WHERE Username1 = ? AND Username2 = ?");
	    $query->bind_param('ss', $_SESSION['Username'], $_POST['followee']);
	    $query->execute();
	    $query->close();
	}
    }
    $check_followee->close();
    $conn->close();
    header("Location: followUserInfo.php?name=" . $_POST['followee']);
?>
