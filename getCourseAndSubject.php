<?php
include 'connect.php';

$sqlGetCourse="SELECT * FROM course";
$sqlGetSubject="SELECT * FROM subject";

$resGetCourse=mysqli_query($connect,$sqlGetCourse);
$resGetSubject=mysqli_query($connect,$sqlGetSubject);

$response= array();

$response['course']=array();
$response['subject']=array();

while($row=mysqli_fetch_array($resGetCourse)){
	array_push($response['course'],array('courseID'=>$row[0], 'course'=>$row[1]));
}

while($row1=mysqli_fetch_array($resGetSubject)){
	array_push($response['subject'],array('subjectID'=>$row1[0], 'subject'=>$row1[1]));
	
}



echo json_encode(array("server_response"=>$response));
mysqli_close($connect);

?>