<?php
include 'connect.php';
$courseID = $_POST['courseID'];
$subjectID = $_POST['subjectID'];
$select=$_POST['select'];

$SelectCourseSubjectID="SELECT courseSubjectID FROM courseSubject WHERE courseID='$courseID' AND subjectID='$subjectID'";
$getSelectCourseSubjectID=mysqli_query($connect,$SelectCourseSubjectID);


$response= array();
while($row=mysqli_fetch_array($getSelectCourseSubjectID)){
	$courseSubjectID=$row[0];
}

$response['chartData']=array();
if($select=="per"){
	$selectData = "SELECT assignID, Solved FROM EinsteinAssignments WHERE courseSubjectID='$courseSubjectID'";
	$resSelectData=mysqli_query($connect, $selectData);
	while($row=mysqli_fetch_array($resSelectData)){
		array_push($response['chartData'],array('assignID'=> $row[0], 'Solved'=>$row[1]));
	}
}
else{
	$selectData="SELECT NumOFSolved, mmyy FROM ChartAssignmentSolved WHERE courseSubjectID='$courseSubjectID'";
	$resSelectData=mysqli_query($connect, $selectData);
	while($row=mysqli_fetch_array($resSelectData)){
		array_push($response['chartData'],array('mmyy'=>$row[1], 'NumOFSolved'=> $row[0]));
	}
}






echo json_encode(array("server_response"=>$response));
mysqli_close($connect);

?>