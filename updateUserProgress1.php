<?php

//file for getting trophies from database to android app Einstein
//When trophy room button has been pushed

$username=$_GET['username'];
$ansInARow=$_GET['ansInARow'];
$trophynum=$_GET['trophynum'];
$taskID=$_GET['taskID'];
$correctOnFirstTry=$_GET['correctOnFirstTry'];
$courseSubjectID=$_GET['courseSubjectID'];
$assignID =$_GET['assignID'];
$solved =$_GET['solved'];
$mmyy=$_GET['mmyy'];

include 'connect.php';


//update userprogres
//first see if user has data in the table for course subject combo
$getProgressID = "SELECT userProgress.id FROM userProgress INNER JOIN user2 ON
(user2.user_id=userProgress.userID) WHERE username='$username' AND courseSubjectID
= '$courseSubjectID';";
$resGetPID = mysqli_query($connect,$getProgressID);
$getUserID = "SELECT user_id FROM user2 WHERE username = '$username';";
$resGetUID = mysqli_query($connect,$getUserID);
$success = false;
$response=array();
$row2=mysqli_fetch_array($resGetUID);
$userID = $row2[0];
if($row=mysqli_fetch_array($resGetPID)){
	//user has data in table, update data
	mysqli_query($connect, "UPDATE userProgress SET taskID = '$taskID', correctOnFirstTry='$correctOnFirstTry' WHERE id='$row[0]';");
	$success=true;
}
else{
	mysqli_query($connect, "INSERT INTO userProgress (userID, courseSubjectID, taskID, correctOnFirstTry) VALUES ('$userID', 
	'$courseSubjectID','$taskID','$correctOnFirstTry');");
	$success=true;
}

//set in trophy if new
$trophyDExsist=true;
$getUserTrophies = mysqli_query($connect,"SELECT trophynum FROM trophies WHERE userid='$userID'");
while($tro=mysqli_fetch_array($getUserTrophies)){
	if($tro[0]=$trophynum){
	$trophyDExsist=false;
	}
}

$updateT ="INSERT INTO trophies (userid, trophynum) VALUES ('$userID', '$trophynum')";
if($trophynum>0 and $trophyDExsist){
	mysqli_query($connect,$updateT);
}
$ThatAssignID=0;

//update solved column in EinsteinAssignments
$i=0;
$suc=false;
$arrayAID = explode(',',$assignID);
$arraySolved=explode(',',$solved);
$NumSolved=0;
$size = count($arrayAID);
for($i=0;$i<$size;$i++){
	if($arraySolved[$i]>0):
		$NumSolved++;
		$ThatAssignID=$arrayAID[$i];
$addToSolved="UPDATE EinsteinAssignments SET Solved= Solved+1 WHERE assignID='$ThatAssignID'";
		mysqli_query($connect, $addToSolved);
           $test=$arrayAID[$i];
$suc=true;
	endif;
}

//set data into ChartAssignmentSolved
//see if current month data is there, if yes update and if no insert ned row
$selectCSsolvedID ="SELECT CSsolvedID FROM ChartAssignmentSolved WHERE courseSubjectID='$courseSubjectID' AND mmyy='$mmyy'";
$resCSsolvedID=mysqli_query($connect, $selectCSsolvedID);
	if($rowSolved=mysqli_fetch_array($resCSsolvedID)):
		mysqli_query($connect, "UPDATE ChartAssignmentSolved SET NumOfSolved=NumOfSolved+'$NumSolved' WHERE courseSubjectID = '$courseSubjectID' AND mmyy='$mmyy'");
	else:
		mysqli_query($connect, "INSERT INTO ChartAssignmentSolved (courseSubjectID, NumOfSolved, mmyy) VALUES ('$courseSubjectID','$NumSolved','$mmyy')");
		
	endif;

	//update userdata
$updateAIR = "UPDATE user2 SET ansInARow='$ansInARow', Solved = Solved + '$NumSolved' WHERE username='$username';";
//select from database
$selectUser="SELECT username, ansInARow, user_id FROM user2 WHERE username='$username';";
$rescheck=mysqli_query($connect,$selectUser);

$response=array();

//if user is registered if yes, update info
if(mysqli_fetch_array($rescheck)){
	mysqli_query($connect,$updateAIR);
}
else{
	$response['success'] = false;
}
//check if ansInARow has been updated
$rescheck=mysqli_query($connect,$selectUser);
$row=mysqli_fetch_array($rescheck);
if($row[1]==$ansInARow){
	$response['success'] = true;
}

$response['succes']=$arrayAID;
$response['array']=$addToSolved;
$response['array1']=$arrayAID[$i-1];

echo json_encode(array("server_response"=>$response));
mysqli_close($connect);




?>