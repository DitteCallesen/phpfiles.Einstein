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
    
    //check if save button has been clicked
	//if(isset($_POST['save'])){
		if($epr=='save'):
	
	//connect to database
	$connect = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
    
    //read data from inputbox on the webpage
    $class = ($_POST["class"]);
    $subject = $_POST["subject"];
    $difficulty = ($_POST["difficulty"]);
    $task = ($_POST["assignments"]);
	
	//function for submitting data into the table
   	function registerTask() {
        global $connect, $class, $subject, $difficulty, $task;
        $statement = mysqli_prepare($connect, "INSERT INTO assignments (course, subject, difficulty, task) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($statement, "ssis", $class, $subject,$difficulty, $task);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);     
   }
   //function to check if task already exist in the table
    function taskDontExsist() {
        global $connect, $task, $class, $subject;
        $statement = mysqli_prepare($connect, "SELECT * FROM assignments WHERE course = ? AND subject = ? AND task = ?"); 
        mysqli_stmt_bind_param($statement, "sss", $class, $subject,$task);
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
		//executing part of inserting data to table, checking if all fields are filled out
		//and that the assignments does not exist in the table
		if(CHeckInputData()):
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
	
	//check if delete is presset, if yes connect to database and get delete
	if($epr=='delete'):
	$connect = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
		$id=$_GET['id'];
		$delete=mysqli_query($connect,"DELETE FROM assignments WHERE assign_id='$id'");
		if($delete):
            echo nl2br(" \r\n Delete complete.");
			header("location:success.php");
		else:	
		
		    echo nl2br(" \r\n Error in deletion.");
		endif;
	endif;
	
	//check if save update is clicked
	if($epr=='saveup'):
	$connect = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
		
	$assign_id = ($_POST["assign_id"]);
	$class = ($_POST["class"]);
    $subject = $_POST["subject"];
    $difficulty = ($_POST["difficulty"]);
    $task = ($_POST["assignments"]);
	
		//executing part of inserting data to table, checking if all fields are filled out
		//and that the assignments does not exist in the table
		if(CHeckInputData()):
			if(update()):
				$update=mysqli_query($connect, "UPDATE assignments SET course='$class', subject='$subject', 
				difficulty='$difficulty', task='$task' WHERE assign_id='$assign_id'");
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
		    $con = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
			$id=$_GET['id'];
			$row=mysqli_query($con,"SELECT * FROM assignments WHERE assign_id=$id");
			$st_row=mysqli_fetch_array($row);
				?>		
			<h1>Update assignments</h1>
								
				<!-- form for update data, input boxes filled out with data from table -->
				<form method="post" action="success.php?epr=saveup">
					<label for="assign_id">Assign_id:</label><br/>
					<input type="text" name="assign_id" id="assign_id" value="<?php echo htmlentities($st_row['assign_id']) ?>"><br/>
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
				<th>assign_id</th>
				<th>Course</th>
				<th>Subject</th>
				<th>Difficulty</th>
				<th>Assignment</th>
				<th>Action</th>
			</tread>
			
			<!--insert data from database into the table-->
			
			<?php
			    $con = mysqli_connect("localhost", "id939471_admin", "admin", "id939471_manage");
				$statement = mysqli_query($con, "SELECT * FROM assignments");
				
				while($row=mysqli_fetch_array($statement)){
					echo"<tr> 
							<td>".$row['assign_id']."</td>
							<td>".$row['course']."</td>
							<td>".$row['subject']."</td>
							<td>".$row['difficulty']."</td>
							<td>".$row['task']."</td>
					<td align ='center'> 
					<!--buttons for delete and update a particular data input-->
						<a href='success.php?epr=delete&id=".$row['assign_id']."'>DELETE</a> |
						<a href='success.php?epr=update&id=".$row['assign_id']."'>UPDATE</a>
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