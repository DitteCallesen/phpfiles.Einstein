<?php
//File for fetching data for assignments from database to android app Einstein
//receive username, course and subject info from android app 
$course=$_POST['course'];
$subject=$_POST['subject'];
$username=$_POST['username'];

//establish connection to database
include 'connect.php';

//query statement to databse
//select right assignments from course and subject variable, array of task
$selectTask = "SELECT task, difficulty, assignID FROM EinsteinAssignments INNER JOIN courseSubject ON 
(courseSubject.courseSubjectID = EinsteinAssignments.courseSubjectID) INNER JOIN course 
ON(course.courseID=courseSubject.courseID) INNER JOIN subject 
ON(subject.subjectID=courseSubject.subjectID) WHERE subject.subject = '$subject'
AND course.course = '$course';";
$resTask = mysqli_query($connect,$selectTask);

//select ansInARow from user table -> integer
$selectStreak = "SELECT ansInARow, Solved FROM user2 WHERE username = '$username';";
$resStreak = mysqli_query($connect,$selectStreak);
//select user's trophies from trophy table -> might be an array or might not, if no trophies then empty array
$selectTrophy = "SELECT trophynum FROM trophies INNER JOIN user2 ON (trophies.userID = user2.user_ID)
 WHERE username = '$username';";
$resTrophy=mysqli_query($connect,$selectTrophy);
//select taskID for selected class and subject -> one integer
$selectTaskID ="SELECT taskID, courseSubject.courseSubjectID, correctOnFirstTry FROM userProgress INNER JOIN user2 ON (user2.user_ID = userProgress.userID)
 INNER JOIN courseSubject ON (courseSubject.courseSubjectID = userProgress.courseSubjectID) 
 INNER JOIN course ON(course.courseID=courseSubject.courseID) INNER JOIN subject 
 ON(subject.subjectID=courseSubject.subjectID) WHERE subject.subject = '$subject'
 AND course.course = '$course' AND username = '$username';";
$resTaskID=mysqli_query($connect,$selectTaskID);
$selectCSID = "SELECT courseSubjectID FROM courseSubject INNER JOIN subject ON(subject.subjectID=courseSubject.subjectID) 
INNER JOIN course ON (course.courseID=courseSubject.courseID) WHERE course='$course' AND subject='$subject'; ";
$resCSID = mysqli_query($connect,$selectCSID);

//make a array of the data and send it to android app
$response =array();


while($row = mysqli_fetch_array($resStreak)){
	$response['userdata']=array();
	if($row1= mysqli_fetch_array($resTaskID)){
		array_push($response['userdata'],array("username"=>$username,'taskID'=>$row1[0], 'courseSubjectID'=>$row1[1],"ansInARow"=>$row[0], 'Asolved'=>$row[1],"correctOnFirstTry"=>$row1[2]));
	}
	else{
		$row4=mysqli_fetch_array($resCSID);
		array_push($response['userdata'],array("username"=>$username,'taskID'=>'0', 'courseSubjectID'=>$row4[0],"ansInARow"=>$row[0],'Asolved'=>$row[1],"correctOnFirstTry"=>'0'));
	}
	
	$response['assignments'] = array();
	while($row2=mysqli_fetch_array($resTask)){

		array_push($response['assignments'],array('task'=>$row2[0], 'difficulty'=>$row2[1], 'assignID'=>$row2[2]));
	}
	
	$response['trophy']=array();
	while($row3=mysqli_fetch_array($resTrophy)){
		array_push($response['trophy'],array('trophynum'=>$row3[0]));
	}
	
}

echo json_encode(array("server_response"=>$response));
mysqli_close($connect);

?>