<!DOCTYPE HTML>

<?php
    //Log in page for webpage for inserting and editing assignments in android app Einstein
    // Start the session
    session_start();
	//connect to host
	$con = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
	
    // Error message
    $error = "";

    // Checks to see if the user is already logged in. If so, redirect to correct page.
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
        $error = "success";
        header('Location: success.php');
    } 
        
    // Checks to see if the username and password have been entered.
    // If so and are equal to the username and password defined above, log them in.
    if (isset($_POST['username']) && isset($_POST['password'])) {
        //search database for user with position admin
			if(validUser()){
				$_SESSION['loggedIn'] = true;
                                    //send username to success.php
                                $_SESSION['username'] = $_POST['username']; 
            header('Location: success.php');
			}
			else{
				 $_SESSION['loggedIn'] = false;
            $error = "Invalid username and password!";
			}
		

    }
        //check if user is in the database with the position admin, if yes give access
	function validUser() {	
        global $con, $username,$password;
        $statement = mysqli_prepare($con, "SELECT * FROM user2 where username= ? and password = ? and position ='admin'"); 
        mysqli_stmt_bind_param($statement, "ss", $_POST['username'], $_POST['password']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        $count = mysqli_stmt_num_rows($statement);
        mysqli_stmt_close($statement); 
        if ($count > 0){
            return true; 
        }else {
            return false; 
        }
    }
?>
<!-- html layout for webpage-->
<html>
    <head>
        <title>Login Page</title>
    </head>
    
    <body>
         <h1>Einstein app webpage for inserting and editing assignments</h1>
          <h2>Loggin nedenfor</h2>
        <!-- Output error message if any -->
        <?php echo $error; ?>
        
        <!-- form for login -->
        <form method="post" action="index.php">
            <label for="username">Username:</label><br/>
            <input type="text" name="username" id="username"><br/>
            <label for="password">Password:</label><br/>
            <input type="password" name="password" id="password"><br/>
            <input type="submit" value="Log In!">
        </form>
    </body>
</html>