<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    require("db.php");
   		$username=$_SESSION['username'];
    
    if (isset($_SESSION['vehicle'])){
		$vehiclechosen=$_SESSION['vehicle'];
    }
    
    if (isset($_SESSION['ownerchosen'])){
		$ownerchosen=$_SESSION['ownerchosen'];
    }
    
    if (isset($_POST['log_out'])){
	
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
			);
		}
	
		session_destroy();
		header('Location: login.php');
		exit();
    }    
    
    if (isset($_SESSION['vehiclefordb'])){
		$vehvar=$_SESSION['vehiclefordb'];
        $query = " 
            SELECT * 
            FROM vehicle 
            JOIN (model) 
                USING (vehicle_id)
            JOIN (ownership)
                USING (vehicle_id)
            WHERE vehicle_id='$vehvar'
            ";
     	$vehicleforowner=mysqli_query($conn, $query);
        $rows=mysqli_num_rows($vehicleforowner);
        
    }
    
    if (isset($_SESSION['ownerchosenfordb'])){
		$pervar=$_SESSION['ownerchosenfordb'];
        $query="
	    SELECT * 
	    FROM people 
	    WHERE people_id='$pervar'
	    ";
	
    	$ownerforvehicle=mysqli_query($conn, $query);
		$rows=mysqli_num_rows($ownerforvehicle);
    }
    
    if (isset($_POST['ownersearch'])){
		$ownersearch=$_REQUEST['ownersearch'];
		
		$query="
		SELECT vehicle_plate from vehicle
		JOIN (ownership) USING (vehicle_id)
		WHERE people_id='$ownersearch'";
		
		$ownervehicles=mysqli_query($conn, $query);
		$veharray = [];
		while ($row = $ownervehicles->fetch_assoc()){
			foreach($row as $value){
				$veharray[] = $value;
			}
		}
		$allvehiclestr=implode(", ", $veharray);
		
		$query="
		SELECT * from people
		WHERE people_id='$ownersearch'";
		
		$ownerdetails=mysqli_query($conn, $query);
		$rows=mysqli_num_rows($ownerdetails);
    }

    if (isset($_POST['change_vehicle'])){
		$_SESSION['addingvehicle']=True;
		header('Location: lookup.php');
		exit();
    }
    
    if (isset($_POST['change_owner'])){
		$_SESSION['addingowner']=True;
		header('Location: lookup.php');
		exit();
    }
    
?>

<html>
<head>
<title>Vehicle Ownership</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=0.1">
<link rel ="stylesheet" href="db_style.css">
</head>

<body>
    <div class="navbar">
        <a class="nava" href="index.php">Nottinghamshire </br> Traffic Infractions </a>
		<a class="navb" href="lookup.php">Person/Vehicle Lookup</a>
		<a class="navb" href="newentry.php">New Entry</a>
		<a class="navb" href="newowner.php">Vehicle Owners</a>
		<a class="navb" href="reporting.php">Reporting</a>
        <div class="policeimg"></div>
	<div class="navc">
	    <form action="" method="post">
		<button id="logoutbutton" type="post" name="log_out">Log out</button>
	    </form>
	</div>
    </div>
    <div class="row">
        <div class="column_left">
            <label id="searchlabel">Select vehicle</label>
			<p>Choose vehicle in vehicle lookup</p>
			<form action="" method="post">
			<button id="searchbutton" type="post" name="change_vehicle">Choose vehicle</button>
			</form>
            <?php
            if(isset($vehicleforowner)) {
            ?>  <h3>Vehicle Details</h3>
                <table border="1">
                    <tr>
                    <th>Vehicle ID</th>
                    <th>Vehicle plate</th>
                    <th>Vehicle Model ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Colour</th>
                    </tr>
                <?php
                if ($vehicleforowner->num_rows > 0){
                        while($row = $vehicleforowner->fetch_assoc()){
                        ?>
                            <tr>
                            <td><?php echo $row['vehicle_id']; ?></td>
                            <td><?php echo $row['vehicle_plate']; ?></td>
                            <td><?php echo $row['vehicle_model_id']; ?></td>
                            <td><?php echo $row['make']; ?></td>
                            <td><?php echo $row['model']; ?></td>
                            <td><?php echo $row['colour']; ?></td>
                            </tr>
		    
                        <?php
                        }
                    }
                } 
		?>
		</table>
            
        </div>
        <div class="column_right">
	    <label id="searchlabel">Select owner</label>
	    <p>Choose owner to assign in person lookup</p>
	    <form action="" method="post">
		<button id="searchbutton" type="post" name="change_owner">Choose person</button>
	    </form>
	    
	    <?php
	    if(isset($ownerforvehicle)) {
	    ?>  <h3>Owner Details</h3>
			<table border="1">
				<tr>
				<th>Person ID</th>
				<th>First name</th>
				<th>Surname</th>
				<th>License</th>
				<th>Address</th>
				</tr>
			<?php
			if ($ownerforvehicle->num_rows > 0){
				while($row = $ownerforvehicle->fetch_assoc()){
				?>
					<tr>
					<td><?php echo $row['people_id']; ?></td>
					<td><?php echo $row['people_fname']; ?></td>
					<td><?php echo $row['people_lname']; ?></td>
					<td><?php echo $row['people_license']; ?></td>
					<td><?php echo $row['people_address']; ?></td>
					</tr>
				
				<?php
				}
				
			}
			?>
		</table>
		<?php
		}
		?>
		
        </div>
    
    
    <div class='column_right'>
	<form method="post">
	    <label id="searchlabel">Vehicle Owner Lookup</label></br></br>
	    <input type="text" placeholder="Person ID" name="ownersearch"></br>
	    <button id="searchbutton" type="submit">Search vehicles by Person ID</button>
	</form>
	<br>
	<?php
	if(isset($_POST['ownersearch'])){
	?>
	    <h3>Owner Details</h3>
		<table border="1">
		    <tr>
		    <th>Person ID</th>
		    <th>First name</th>
		    <th>Surname</th>
		    <th>Vehicles owned by plate</th>
		    
		<?php
		if ($ownerdetails->num_rows > 0){
		    while($row = $ownerdetails->fetch_assoc()){
		?>
		    <tr>
		    <td><?php echo $row['people_id']; ?></td>
		    <td><?php echo $row['people_fname']; ?></td>
		    <td><?php echo $row['people_lname']; ?></td>
		    <td><?php echo $allvehiclestr; ?></td>
			
		    <?php	    
		    }
		}	
	}
	
    
	
	?>
    </div>	
    <?php

    if(isset($vehiclechosen)){ 
		?>
		</div>
		<center>
			<h2>Confirm owner assigmnent</h3>
			<h3>Ownership Details</h3>
			</br>
			<form action="" method="post">
				<input type="text" placeholder="Confirm Vehicle ID" name='vehid' required>
				<input type="text" placeholder="Confirm Person ID" name='perid' required>
				<button id="confirmbutton" type="post" name="assign_owner">Assign owner</button>
			</form>
		</center>
		</div>
		<?php
	}
	
    if (isset($_POST['assign_owner'])){
		$pertoupdate=$_REQUEST['perid'];
		$vehtoupdate=$_REQUEST['vehid'];
		$query="
		UPDATE ownership
			SET people_id='$pertoupdate'
		WHERE vehicle_id='$vehtoupdate'
		";
		
		$assignmentresult=mysqli_query($conn, $query);
		
		$tocut = array(" ", "(", ")", "'");
		$trimmedquery=str_replace($tocut, "_", $query);
		$logquery="INSERT INTO user_logs (username, user_action) VALUES
		('$username', '$trimmedquery')";
		mysqli_query($conn, $logquery);
	
	    if ($assignmentresult) {
		echo '<center><p>Updated succesfully</p></center>';
		}
		    
	    else {
		echo '<center><p>Unsuccessful</p></center>';
		}
		    
	}
	?>
<div id="versionno">v1.0<div> 
</body>

</html>