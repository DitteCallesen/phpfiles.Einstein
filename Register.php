<?php
    //php file for registeration of user from Android app Einstein
    //establish connection to database
    $connect = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
    
    //recieve data from android app
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $position = $_POST["position"];
   
    //function for inserting userdata into database
    function registerUser() {
        global $connect, $name, $username, $email, $password,$position;
        
        $statement = mysqli_prepare($connect, "INSERT INTO user2 (name, username, email, password, position) VALUES (?, ?, ?, ?,?)");
        mysqli_stmt_bind_param($statement, "sssss", $name, $username,$email, $password, $position);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);     
    }

    //function for checking if username is taken
    function usernameAvailable() {
        global $connect, $username;
        $statement = mysqli_prepare($connect, "SELECT * FROM user2 WHERE username = ?"); 
        mysqli_stmt_bind_param($statement, "s", $username);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        $count = mysqli_stmt_num_rows($statement);
        mysqli_stmt_close($statement); 
        if ($count < 1){
            return true; 
        }else {
            return false; 
        }
    }
    //function to check if email is taken
    function emailAvailable() {
        global $connect, $email;
        $statement = mysqli_prepare($connect, "SELECT * FROM user2 WHERE email = ?"); 
        mysqli_stmt_bind_param($statement, "s", $email);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        $count = mysqli_stmt_num_rows($statement);
        mysqli_stmt_close($statement); 
        if ($count < 1){
            return true; 
        }else {
            return false; 
        }
    }
    //executing part of file, if username and email are avaliable, send data to app
    $response = array();
    $response["success"] = false;  
    $response["nameAvil"]=usernameAvailable();
    $response["emailAvil"]=emailAvailable();
    if (usernameAvailable() and emailAvailable()){
        registerUser();
        $response["success"] = true;  
    }
	
    echo json_encode($response);
?>