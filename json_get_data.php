<?php
//File for fetching assignments from database to android app Einstein
//receive course and subject info from android app 
$course=$_GET['course'];
$subject=$_GET['subject'];

//establish connection to database
$connect = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");

//query statement to databse
$sql = "select * from assignments WHERE course ='$course' AND subject='$subject';";
$result = mysqli_query($connect,$sql);

//make a array of the data and send it to android app
$response =array();
while($row = mysqli_fetch_array($result)){
	array_push($response, array("class"=>$row[1], "subject"=>$row[2],"sett"=>$row[3], "task"=>$row[4]));
}

echo json_encode(array("server_response"=>$response));
mysqli_close($connect);

?>