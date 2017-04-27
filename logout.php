<?php
//--logout for webpage-->
    session_start();
    $_SESSION['loggedIn'] = false;
    header("Location: index.php");
?>
