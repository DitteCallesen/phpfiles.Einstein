<?php
    // Start the session
    ob_start();
    session_start();
    $msg='Welcome  ';
    
    //some strings to check if something happens, like updating, editing and deleting data from table
	$epr=' ';
	
	$error_t ="";
	//take username from the previous session, which is from the index.php and display a welcome message
	$username = $_SESSION['username'];
	echo $msg; echo $username; 
	//check if epr has been difficulty, if not set it as hei
	if(isset($_GET['epr'])){
		$epr=$_GET['epr'];
	}
	else{$epr='hei';}
	
    // Check to see if actually logged in. If not, redirect to login page
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false) {
        header("Location: index.php");
    }
    //initiate some variables to use for reading, submitting and editing data from table assignments
	$class = "";
    $subject = "";
    $difficulty = "";
    $task = "";
    include 'connect.php';
    //check if save button has been clicked
	//function to find courseSubjectID
	function findCSID($class, $subject){
		global $connect, $difficulty, $task;
		$findCourseSubjectID="SELECT courseSubjectID FROM courseSubject INNER JOIN course ON
		(course.courseID=courseSubject.courseID) INNER JOIN subject ON 
		(subject.subjectID=courseSubject.subjectID) WHERE subject='$subject' AND course='$class';";
		$resCSID = mysqli_query($connect, $findCourseSubjectID);
		if($row=mysqli_fetch_array($resCSID)):
			//course subject combo is in the table
			$courseSubjectID=$row[0];
		//course subject combo is not in the table must insert
		else:
			//check if class is in the table
			$check = "SELECT courseID FROM course WHERE course='$class';";
			$resCheck= mysqli_query($connect, $check);
			if($row1=mysqli_fetch_array($resCheck)):
				//found courseID
				$courseID = $row1[0];
			else:
				//insert and find course ID
				$insertCourse ="INSERT INTO course (course) VALUES ('$class'); ";
				$resInsertC=mysqli_query($connect,$insertCourse);
				$resCheck= mysqli_query($connect, $check);
				$courseID=mysqli_fetch_array($resCheck)[0];
			endif;
			
			//check if subject is in the table
			$check = "SELECT subjectID FROM subject WHERE subject='$subject';";
			$resCheck= mysqli_query($connect, $check);
			if($row1=mysqli_fetch_array($resCheck)):
				//found subjectID
				$subjectID = $row1[0];
			else:
				//insert and find course ID
				$insertCourse ="INSERT INTO subject (subject) VALUES ('$subject'); ";
				$resInsertC=mysqli_query($connect,$insertCourse);
				$resCheck= mysqli_query($connect, $check);
				$subjectID=mysqli_fetch_array($resCheck)[0];
			endif;
			
			//insert into courseSubnject table to get courseSubjectID
			$insertCS = "INSERT INTO courseSubject (courseID, subjectID) VALUES ('$courseID', '$subjectID');";
			$resInsertCS=mysqli_query($connect, $insertCS);
			$resCSID = mysqli_query($connect, $findCourseSubjectID);
			$courseSubjectID=mysqli_fetch_array($resCSID)[0];
		endif;
		return $courseSubjectID;
	}
	
	//function to check if task already exist in the table
    function taskDontExsist() {
        global $connect, $task, $class, $subject;
		$courseSubjectID=findCSID($class, $subject);
        $statement = mysqli_prepare($connect, "SELECT * FROM EinsteinAssignments WHERE courseSubjectID = ? AND task = ?"); 
        mysqli_stmt_bind_param($statement, "is", $courseSubjectID,$task);
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
    //check if one input field is empty, if yes give error message
    function CheckInputData(){
			$class = ($_POST["class"]);
			$subject = $_POST["subject"];
			$difficulty = ($_POST["difficulty"]);
			$task = ($_POST["assignments"]);
			if($task==""||$class==""||$difficulty==""||$subject==""):
			   return false;
			else:
			   return true;
			endif;
    }
	//insert new assignment to database
	if($epr=='save'):
    //read data from inputbox on the webpage
    $class = ($_POST["class"]);
    $subject = $_POST["subject"];
    $difficulty = ($_POST["difficulty"]);
    $task = ($_POST["assignments"]);
			
	//function for submitting data into the table
   	function registerTask() {
        global $connect, $class, $subject, $difficulty, $task;
		$courseSubjectID=findCSID($class, $subject);
        $statement = mysqli_prepare($connect, "INSERT INTO EinsteinAssignments (courseSubjectID, difficulty, task) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($statement, "iis", $courseSubjectID,$difficulty, $task);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);     
   }
   		//executing part of inserting data to table, checking if all fields are filled out
		//and that the assignments does not exist in the table
		$courseSubjectID = findCSID($class, $subject);
		if(CheckInputData()):
			if(taskDontExsist()):
				registerTask();
				echo nl2br (" \r\n Assignment has been added.");
				
			else:
				echo nl2br(" \r\n Assignment does exist in the database");
				echo nl2br (" \r\n Try again.");
				
			endif;
		else:
			echo nl2br (" \r\n You did not fill out the required fields.");
			echo nl2br (" \r\n Try again.");
		endif;
	endif;
	
	//check if delete is pressed, if yes connect to database and get delete
	if($epr=='delete'):
		$id=$_GET['id'];
		$delete=mysqli_query($connect,"DELETE FROM EinsteinAssignments WHERE assignID='$id'");
		if($delete):
            echo nl2br(" \r\n Delete complete.");
			header("location:success.php");
		else:	
		
		    echo nl2br(" \r\n Error in deletion.");
		endif;
	endif;
	
	//check if save update is clicked
	if($epr=='saveup'):
	$assignID = ($_POST["assignID"]);
	$class = ($_POST["class"]);
    $subject = $_POST["subject"];
    $difficulty = ($_POST["difficulty"]);
    $task = ($_POST["assignments"]);
	
		//executing part of inserting data to table, checking if all fields are filled out
		//and that the assignments does not exist in the table
		if(CheckInputData()):
		$courseSubjectID=findCSID($class, $subject);
		$update=mysqli_query($connect, "UPDATE EinsteinAssignments SET courseSubjectID='$courseSubjectID', 
				difficulty='$difficulty', task='$task' WHERE assignID='$assignID'");
			if($update):			
				echo nl2br (" \r\n Update complete");
			else:
				echo nl2br (" \r\n Error in updating");
				echo nl2br (" \r\n Try again.");
			endif;
		else:
			echo nl2br (" \r\n You did not fill out the required fields.");
			echo nl2br (" \r\n Try again.");
		
		endif;
	endif; 
	
?>



<!-- layout for the webpage-->
<html>
    <head>
        <title>Einstein</title>
    </head>
    <body>
		<?php
			if(isset($_GET['epr'])){
			$epr=$_GET['epr'];
			}
			else{$epr='hei';}
		
		// Update button is clicked, and select data from table
		if($epr=='update'): 
		    include 'connect.php';
			$id=$_GET['id'];
			$row=mysqli_query($connect,"SELECT assignID, course, subject, difficulty, task FROM EinsteinAssignments 
			INNER JOIN courseSubject ON (courseSubject.courseSubjectID=EinsteinAssignments.courseSubjectID) INNER JOIN
			course ON (course.courseID=courseSubject.courseID) INNER JOIN subject ON (courseSubject.subjectID=
			subject.subjectID) WHERE assignID='$id'");
			$st_row=mysqli_fetch_array($row);
				?>		
			<h1>Update assignments</h1>
								
				<!-- form for update data, input boxes filled out with data from table -->
				<form method="post" action="success.php?epr=saveup">
					<label for="assignID">assignID:</label><br/>
					<input type="text" name="assignID" id="assignID" value="<?php echo htmlentities($st_row['assignID']) ?>"><br/>
					<label for="class">Class:</label><br/>
					<input type="text" name="class" id="class" value="<?php echo htmlentities($st_row['course']) ?>"><br/>
					<label for="subject">Subject:</label><br/>
					<input type="text" name="subject" id="subject" value="<?php echo htmlentities($st_row['subject']) ?>"><br/>
					<label for="difficulty">Assignment difficulty:</label><br/>
					<input type="text" name="difficulty" id="difficulty" value="<?php echo htmlentities($st_row['difficulty']) ?>"><br/>
					<label for="assignments">Assignment with answers:</label><br/>
					<input class="textbox" size="250%" type="text" name="assignments" id="assignments" Value="<?php echo htmlentities($st_row['task']) ?>"><br/>
					<input type="submit" name="saveup">
				</form>
				
		<!--if not update do this-->
		
		<?php else: ?>
		
				<h1>Insert into table for assignments</h1>
				
				<!-- form for input data to database -->
				<form method="post" action="success.php?epr=save">
					<label for="class">Class:</label><br/>
					<input type="text" name="class" id="class" value="<?php echo htmlentities($class) ?>"><br/>
					<label for="subject">Subject:</label><br/>
					<input type="text" name="subject" id="subject" value="<?php echo htmlentities($subject) ?>"><br/>					
					<label for="difficulty">Assignment difficulty:</label><br/>
					<input type="text" name="difficulty" id="difficulty" value="<?php echo htmlentities($difficulty) ?>"><br/>		 
					<label for="assignments">Assignment with answers:</label><br/>
					<input class="text" size="250%" type="text" name="assignments" id="assignments"><br/>
					<input type="submit" name="save">
				</form>
		<?php endif; ?>
    </body>
</html>	


<!--Create table and show data from database-->
<html>
	<body>
	<!--make the table-->
		<h2 align="center"> Tabell for assignments</h2>
		<table align="center" boder="1" cellspacing="0" width="1500">
			<tread>
				<th>assignID</th>
				<th>Course</th>
				<th>Subject</th>
				<th>Difficulty</th>
				<th>Assignment</th>
				<th>Action</th>
			</tread>
			
			<!--insert data from database into the table-->
			
			<?php
			    include 'connect.php';
				$statement = mysqli_query($connect, "SELECT assignID, course, subject, difficulty, task FROM 
				EinsteinAssignments INNER JOIN courseSubject ON (courseSubject.courseSubjectID=EinsteinAssignments.courseSubjectID)
				INNER JOIN subject ON (courseSubject.subjectID=subject.subjectID) INNER JOIN course ON
				(course.courseID=courseSubject.courseID);");
				
				while($row=mysqli_fetch_array($statement)){
					echo"<tr> 
							<td>".$row['assignID']."</td>
							<td>".$row['course']."</td>
							<td>".$row['subject']."</td>
							<td>".$row['difficulty']."</td>
							<td>".$row['task']."</td>
					<td align ='center'> 
					<!--buttons for delete and update a particular data input-->
						<a href='success.php?epr=delete&id=".$row['assignID']."'>DELETE</a> |
						<a href='success.php?epr=update&id=".$row['assignID']."'>UPDATE</a>
					</td>										
					</tr>";
				}
			?>
			</table>
		</body>
	</html>






<!--log out button-->
<html>
	<body>
<form method="post" action="logout.php">
    <input type="submit" value="Logout">
</form>
</body>
</html>