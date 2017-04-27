<?php
   //For Android app Einstein 
   //check if input username is in the database, if yes send userinfo to app to compare
     //establish link to database
    $con = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
    
    //username from app 
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    //fecth data from database
    $statement = mysqli_prepare($con, "SELECT * FROM user2 WHERE username = ?");
    mysqli_stmt_bind_param($statement, "s", $username);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    mysqli_stmt_bind_result($statement, $userID, $name, $username, $email, $password, $position, $ansInARow, $Solved);
    
    $response = array();
    $response["success"] = false;  
    //if username is in the database
    while(mysqli_stmt_fetch($statement)){
        $response["success"] = true;  
        $response["name"] = $name;
        $response["username"] = $username;
        $response["email"] = $email;
        $response["password"] = $password;
        $response["position"] = $position;
        $response["Asolved"] = $Solved;
    }
    //send data to app 
    echo json_encode($response);
?>