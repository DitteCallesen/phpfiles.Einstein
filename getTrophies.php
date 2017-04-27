<?php
//file for getting trophies from database to android app Einstein
//When trophy room button has been pushed

$username=$_GET['username'];

include 'connect.php';

//select from database
$selectTrophy = "SELECT trophynum FROM trophies INNER JOIN user2 ON (trophies.userID = user2.user_ID)
 WHERE username = '$username';";
$resTrophy=mysqli_query($connect,$selectTrophy);


$response['trophy']=array();
//if there are trophies registered for user

while($row=mysqli_fetch_array($resTrophy)){
	array_push($response['trophy'],array('trophynum'=>$row[0]));
}
	
echo json_encode(array("server_response"=>$response));
mysqli_close($connect);

?>