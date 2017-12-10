<?php
    // check whether the user has logged in
    session_start();
    if (isset($_SESSION['Username'])) {
	header("Location: userInfo.php");
    }
?>

<!doctype html>
<html lang="en">
<head>
    <title>Welcome to SJBOX!</title>
    <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      <link rel="stylesheet" href="./CSS/login.css">	

<script>
function showPassword() {
    
    var key_attr = $('#key').attr('type');
    
    if(key_attr != 'text') {
        
        $('.checkbox').addClass('show');
        $('#key').attr('type', 'text');
        
    } else {
        
        $('.checkbox').removeClass('show');
        $('#key').attr('type', 'password');
        
    }
    
}
</script>
</head>
<body>
<section id="login">
    <div class="container">
    	<div class="row">
    	    <div class="col-xs-12">
        	<div class="form-wrap">
		    <h1>Log in with your name and passward</h1>
                    <form role="form" action="login.php" method="post" id="login-form" autocomplete="off">
                    <div class="form-group">
                        <label for="name" class="sr-only">Name</label>
                        <input type="name" name="name" id="name" class="form-control" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                     </div>
                     <div class="checkbox"> <span class="character-checkbox" onclick="showPassword()"></span> <span class="label">Show password</span> </div>
                        <input type="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Log in">
                    </form>
		    <a href="register.php">Register now</a>
        	</div>
    	    </div> <!-- /.col-xs-12 -->
    	</div> <!-- /.row -->
    </div> <!-- /.container -->
</section>

<?php
    $servername = "localhost";
    $username = "root";
    $password = "A123456j*";
    $dbname = "SJBOX";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['name'])) {
	// get artist info
	$userName = $_POST['name'];
        $password = $_POST['password'];
	$user_info = $conn->prepare("SELECT Username 
				       FROM User 
				       WHERE Username = ?");
	$user_info->bind_param('s', $userName);
	$user_info->execute();
        $r = $user_info->get_result();
        $r2 = $r->fetch_assoc();
        if(!$r2){
            echo "<script>alert('Invalid Username or Password.');</script>";
            echo "<script>window.location.href= 'login.php';</script>";
        }
        else{
            $r->close();
            $r1 = $conn->prepare("SELECT * FROM User WHERE Username =? AND Password=?");
            $r1->bind_param("ss", $userName, $password);
            $r1->execute();
            $r3 = $r1->get_result();
            $result = $r3->fetch_assoc();
            if(!$result){
                echo "<script>alert('Invalid Username or Password.');</script>";
                echo "<script>window.location.href= 'login.php';</script>";
            }
	    else{
                //set session
                $_SESSION['Username'] = $result['Username'];
                $r1->close();
		echo "<script>alert('Success!');</script>";
                echo "<script>window.location.href= 'userInfo.php';</script>";

	    }
	}
    }

?>

      <!-- Optional JavaScript -->
      <!-- jQuery first, then Popper.js, then Bootstrap JS -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    </body>
</html>


