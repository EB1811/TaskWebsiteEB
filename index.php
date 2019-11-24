<!DOCTYPE html>
<html lang="en-GB">
	<head>
		<meta charset="UTF-8">
		<title>Main Content</title>
		<link rel="stylesheet" href="style.css">
		
		<script>
			// Toggle the visibility of the newTaskForm panel.
			function toggleVisibility(id, className)
			{
				if(className === "none")
				{
					var panel = document.getElementById(id);
				}
				else if(className != "none")
				{
					var div = document.getElementById(id);
					var panel = div.getElementsByClassName(className)[0];
				}
				if (panel.style.display === "none") 
				{
					panel.style.display = "block";
				}
				else 
				{
					panel.style.display = "none";
				}
			}
			
			// Expand div.
			function expand(id, expandClassName,contractClassName, mode)
			{
				// Get the individual task panel and the expandTask and contractTask divs.
				var panel = document.getElementById(id);
				var expandTaskDiv = panel.getElementsByClassName(expandClassName)[0];
				var contractTaskDiv = panel.getElementsByClassName(contractClassName)[0];
				
				// If the mode is 1 and the panel has not yet expanded, expand and show contractTask.
				if (mode === 1 && panel.style.zIndex === "0") 
				{
					panel.style.transform = "scale(2.25)";
					panel.style.boxShadow = "0 4px 8px 0 rgba(0, 0, 0, 0.1), 0 6px 20px 0 rgba(0, 0, 0, 0.05)";
					panel.style.zIndex = "1";
					expandTaskDiv.style.display = "none";
					contractTaskDiv.style.display = "block";
				}
				// If the mode is 0 and the panel has already expanded, contract and hide contractTask.
				else if (mode === 0 && panel.style.zIndex === "1")
				{
					panel.style.transform = "scale(1)";
					panel.style.boxShadow = "none";
					panel.style.zIndex = "0";
					expandTaskDiv.style.display = "block";
					contractTaskDiv.style.display = "none";
				}
			}

			function newTask() {
				// Create the new document elements for the new task panel.
				var newTaskPanel = document.createElement("div");
				
				// Get the 'taskList' div.
				var taskListElement = document.getElementById("taskList");
				var listOfTasks = document.getElementsByClassName("task");
				
				// Set the class names for the new elements and append it onto 'taskList' div.
				newTaskPanel.className = "task";
				newTaskPanel.id = "a" + (listOfTasks.length + 1);
				taskListElement.appendChild(newTaskPanel);
				
				
				
			}
			
		</script>
		
	</head>

	<body>
	
		<?php
			// --SESSION-- \\
			session_start();
		
			// SQL connection.
			$serverName = "localhost";
			$username = "root";
			$password = "";
			$dbName = "taskstore";
			
			$connection = new mysqli($serverName, $username, $password, $dbName);
			
			if ($connection->connect_error) {
				die("Connection failed: " . $connection->connect_error);
			}
			
			
			// --FUNCITONS-- \\
			
			// Outline color function.
			function colorChange($dateA, $statusA)
			{
				$todayStart = strtotime('today');
				$todayEnd = strtotime('tomorrow');
				$dateTimestamp = strtotime($dateA);
				
				// If date occurs before today change color to red.
				if(($dateTimestamp < $todayStart) && $statusA != "Finished")
				{
					$style = "outline: 2px solid #e60000;";
					return $style;
				}
				
				// If status is finished, change color to green.
				if($statusA == "Finished")
				{
					$style = "outline: 2px solid #00b300;";
					return $style;
					
				}
				elseif($statusA == "inProgress")
				{
					$style = "outline: 2px solid #e6b800;";
					return $style;
				}
			}
			// Adding a new row query function.
			function addData($conn)
			{
				$sql = "INSERT INTO Tasks(name, description, priority, dateDue, status) VALUES ('".$_POST["name"]."', '".$_POST["description"]."', '".$_POST["priority"]."', '".$_POST["dueDate"]."', 'inProgress');";
				if ($conn->query($sql) === TRUE) 
				{
				} else {
					echo "Error adding record: " . $conn->error;
				}
				
				// Clear the POST superglobal.
				$_POST['name'] = "done";
			}
			// Updating a row query function.
			function updateData($conn)
			{
				$sql = "UPDATE Tasks SET description = '".$_POST["uDescription"]."', priority = '".$_POST["uPriority"]."', dateDue = '".$_POST["uDueDate"]."', status = '".$_POST["uStatus"]."' WHERE ID = ".$_POST["cID"].";";
				if ($conn->query($sql) === TRUE) 
				{
				} else {
					echo "Error updating record: " . $conn->error;
				}
				
				// Clear the POST superglobal.
				$_POST['update'] = "done";
			}
			// Deleting a row query function.
			function deleteData($conn)
			{
				$delete = "DELETE FROM Tasks WHERE id = \"".$_POST["tID"]."\";";
				if ($conn->query($delete) === TRUE) 
				{
				} else {
					echo "Error deleting record: " . $conn->error;
				}
			}
			// Ordering by date function.
			function orderByDate()
			{
				
			}
			function orderByPriority()
			{
				
			}
			
			
			// --RUNING FUNCITONS-- \\
			
			// Running the delete function.
			if(isset($_POST['delete']))
			{
				if($_POST['delete'] != "done")
				{
					deleteData($connection);
					$_POST['delete'] = "done";
					echo "<meta http-equiv='refresh' content='0' url='index.php'>";
				}
			}
			// Running the add data function.
			if(isset($_POST['name']))
			{
				// Runs if query has not run yet.
				if($_POST['name'] != "done")
				{
					addData($connection);
					$_POST['name'] = "done";
					echo "<meta http-equiv='refresh' content='0' url='index.php'>";
				}
			}
			// Running the update data function.
			if(isset($_POST['update']))
			{
				// Runs if query has not run yet.
				if($_POST['update'] != "done")
				{
					updateData($connection);
					$_POST['update'] = "done";
					echo "<meta http-equiv='refresh' content='0' url='index.php'>";
				}
			}
			
			
			
			// --ORDERING ROWS-- \\
			
			// Order by date.
			if(isset($_POST['dateOrder']))
			{
				$_SESSION['newSQL'] = "SELECT * FROM Tasks ORDER BY dateDue;";
				
				// Clear the POST superglobal.
				$_POST['dateOrder'] = array();
				echo "<meta http-equiv='refresh' content='0' url='index.php'>";
			}
			// Order by priority.
			if(isset($_POST['priorityOrder']))
			{
				$_SESSION['newSQL'] = "SELECT * FROM Tasks ORDER BY CASE WHEN priority = 'high' THEN 1 WHEN priority = 'medium' THEN 2 WHEN priority = 'low' THEN 3 ELSE priority END ASC;";
				
				// Clear the POST superglobal.
				$_POST['priorityOrder'] = array();
				echo "<meta http-equiv='refresh' content='0' url='index.php'>";
			}
			// Order by status.
			if(isset($_POST['statusOrder']))
			{
				$_SESSION['newSQL'] = "SELECT * FROM Tasks ORDER BY CASE WHEN status = 'Finished' THEN 1 WHEN status = 'inProgress' THEN 2 ELSE status END ASC;";
				
				// Clear the POST superglobal.
				$_POST['statusOrder'] = array();
				echo "<meta http-equiv='refresh' content='0' url='index.php'>";
			}
			// Query SQL.
			if($_SESSION['newSQL'] == "SELECT * FROM Tasks ORDER BY dateDue;" || 
			$_SESSION['newSQL'] == "SELECT * FROM Tasks ORDER BY CASE WHEN priority = 'high' THEN 1 WHEN priority = 'medium' THEN 2 WHEN priority = 'low' THEN 3 ELSE priority END ASC;" ||
			$_SESSION['newSQL'] == "SELECT * FROM Tasks ORDER BY CASE WHEN status = 'Finished' THEN 1 WHEN status = 'inProgress' THEN 2 ELSE status END ASC;")
			{
				$sql = $_SESSION['newSQL'];
				$query = $connection->query($sql);
			}
			else
			{
				$sql = "SELECT * FROM Tasks";
				$query = $connection->query($sql) or die($connection->error);
			}
		?>
		
		<header>
			<ul id="Nav">
				<li><a href="index.php">CE154 - Task Manager</a></li>
			</ul>
		</header>
		
		<div id="main">
			<!-- Left panel. -->
			<div id="buttonDiv">
				<!-- Show new task creator div. -->
				<div id="newTaskText">
					<button class="button" onclick="toggleVisibility('newTaskFormPanel', 'none');">
						New Task
					</button>
				</div>
				<div id="ordering">
					<!-- Order by date. -->
					<form class="order" method="post">
						<div class="oButton">
							<input type="hidden" class="updateTaskText" name="dateOrder" value="dateOrder">
							<input type="submit" value="Order By Date">
						</div>
					</form>
					<!-- Order by priority. -->
					<form class="order" method="post">
						<div class="oButton">
							<input type="hidden" class="updateTaskText" name="priorityOrder" value="priorityOrder">
							<input type="submit" value="Order By Priority">
						</div>
					</form>
					<!-- Order by status. -->
					<form class="order" method="post">
						<div class="oButton">
							<input type="hidden" class="updateTaskText" name="statusOrder" value="statusOrder">
							<input type="submit" value="Order By Status">
						</div>
					</form>
				</div>
			</div>
			<!-- Create new task div. -->
			<div id="newTaskFormPanel" style="display: none;">
				<div id="newTaskExit" onclick="toggleVisibility('newTaskFormPanel', 'none')">
					x
				</div>
				<p class="title">New Task</p>
				<form id="newTaskForm" method="post">
					<input type="text" name="name" value="Name"><br>
					<input type="text" name="description" value="Description"><br>
					<div id="priorityRadio">
						<p id="priorityRadioTitle">Priority:</p>
						<input type="radio" name="priority" value="High"> High
						<input type="radio" name="priority" value="Medium"> Medium
						<input type="radio" name="priority" value="Low"> Low<br>
					</div>
					<div id="dueDateDiv">
						<p id="dueDateDivTitle">Date Due:</p>
						<input type="date" name="dueDate" id="newDueDateInput"><br>
					</div>
					<div id="addTaskText">
						<input type="submit" value="Add Task", 'none'>
					</div>
				</form>
			</div>
			<!-- Right margin div. -->
			<div id="rightPanel">
				
			</div>
			<!-- Div to hold each task. -->
			<div id="taskList">
				<!-- PHP loop to create a task div for each row in the database i.e. each task -->
				<?php
					if($query->num_rows > 0)
					{
						// Create task div for each row of database.
						while($row = $query->fetch_assoc())
						{
							echo '
				<div class="task" id="'.$row["ID"].'" style="z-index: 0; '.colorChange($row["dateDue"], $row["status"]).'">
					<div class="expandTask" onclick="expand(this.parentNode.id, \'expandTask\',\'contractTask\', 1)">
					
					</div>
					<div class="contractTask" onclick="expand(this.parentNode.id, \'expandTask\', \'contractTask\', 0)" style="display:none;">
						<div class="contractTaskText">
							x
						</div>
					</div>
					<div class="taskName">
						'.$row["name"].'
					</div>
					<div class="hideDescription" onclick="toggleVisibility(this.parentNode.id, \'taskDescription\')">
						-
					</div>
					<form class="info" method = "post">
						<div class="taskDescription">
							<textarea name="uDescription" class="taskDescriptionTextArea">'.$row["description"].'</textarea>
						</div>
					
						<div class="taskPriority">
							<select class="select" name ="uPriority">
								'//Makes value for the select the same as the value on the table.
							.'
								<option value="high"'.(($row["priority"] == "high") ? " selected=\"selected\"" : " ").'>High</option>
								<option value="medium"'.(($row["priority"] == "medium") ? " selected=\"selected\"" : " ").'>Medium</option>
								<option value="low"'.(($row["priority"] == "low") ? " selected=\"selected\"" : " ").'>Low</option>
							</select>
						</div>
						<div class="taskStatus">
							<select class="select" name="uStatus" >
								<option value="inProgress"'.(($row["status"] == "inProgress") ? " selected=\"selected\"" : " ").'>In Progress</option>
								<option value="Finished"'.(($row["status"] == "Finished") ? " selected=\"selected\"" : " ").'>Completed</option>
							</select>
						</div>
						<div class="taskDueDate">
							<div class="taskDueDateForm">
								Due:
								<input type="date" class="dueDateInput" name="uDueDate" value="'.$row["dateDue"].'">
							</div>
						</div>
						<div class="updateTask">
							<input type="hidden" class="updateTaskText" name="update" value="update">
							<input type="hidden" class="updateTaskText" name="cID" value="'.$row["ID"].'">
							<input type="submit" class="updateTaskText" value="Update Task">
						</div>
					</form>
					<form class="taskDeletion" method="post">
						<input type="hidden" class="taskDeletionText" name="delete" value="delete">
						<input type="hidden" class="taskDeletionText" name="tID" value="'.$row["ID"].'">
						<input type="submit" class="taskDeletionText" value="Delete Task">
					</form>
				</div>';
						}
					}
				?>
			</div>
		</div>
		
		<!-- Footer. -->
		<footer>
			<p id="footerText">
				Created by: Emmanuils Borovikovs<br>
				Contact: eb18847@essex.ac.uk
			</p>
		</footer>
		
	</body>
</html>
