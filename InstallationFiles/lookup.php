<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    require("db.php");
    $username=$_SESSION['username'];
    
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
    
    if (isset($_POST['fnamesearch']) or isset($_POST['lnamesearch'])){
        $fname = $_REQUEST['fnamesearch'];
        $_SESSION['fname'] = $fname;
        $lname = $_REQUEST['lnamesearch'];
        $_SESSION['lname'] = $lname;
        
        $query = "SELECT * FROM people WHERE people_fname='$fname' OR people_lname='$lname'";
        $peopleresult = mysqli_query($conn, $query) or die(mysql_connect_error());
        $rows = mysqli_num_rows($peopleresult);
    }
    
    if (isset($_POST['licensesearch'])){
        $license = $_REQUEST['licensesearch'];
        $_SESSION['license'] = $license;
        
        $query = "SELECT * FROM people WHERE people_license='$license'";
        $peopleresult = mysqli_query($conn, $query);
        $rows = mysqli_num_rows($peopleresult);
    }
    
    if (isset($_POST['platesearch'])){
        $plate = $_REQUEST['platesearch'];
        $_SESSION['plate'] = $plate;
        
        $query = " 
		SELECT * 
		FROM vehicle 
		JOIN (model) 
		    USING (vehicle_id)
		JOIN (ownership)
		    USING (vehicle_id)
		WHERE vehicle_plate='$plate'
		";

        $vehicleresult = mysqli_query($conn, $query);
        $rows = mysqli_num_rows($vehicleresult);
    }
    
    if (isset($_POST['makesearch']) or isset($_POST['modelsearch']) or isset($_POST['coloursearch'])) {
        $params = array();
        
        $model = $_REQUEST['modelsearch'];
        $_SESSION['model'] = $model;
        if($model!=null){
            $modelstructor = "model='$model'";
            $params[] = $modelstructor;
        }
        $make = $_REQUEST['makesearch'];
        $_SESSION['make'] = $make;
        if($make!=null){
            $makestructor = "make='$make'";
            $params[] = $makestructor;
        }
        $colour = $_REQUEST['coloursearch'];
        $_SESSION['colour'] = $colour;
        if($colour!=null){
            $colourstructor = "colour='$colour'";
            $params[] = $colourstructor;
        }
        
        if(count($params)==1){
            $querybuild = "
			SELECT * 
			FROM vehicle 
			JOIN (model) 
			    USING (vehicle_id)
			JOIN (ownership)
			    USING (vehicle_id)
			WHERE %s
			";

            $query = sprintf($querybuild, $params[0]);
        }
        
        elseif(count($params)==2){
            $querybuild = "
			SELECT * 
			FROM vehicle 
			JOIN (model) 
			    USING (vehicle_id)
			JOIN (ownership)
			    USING (vehicle_id)
			WHERE %s AND %s
			";

            $query = sprintf($querybuild, $params[0], $params[1]);
        }
        
        elseif(count($params)==3){
            $querybuild = "
			SELECT * 
			FROM vehicle 
			JOIN (model) 
			    USING (vehicle_id)
			JOIN (ownership)
			    USING (vehicle_id)
			WHERE %s AND %s AND %s
			";
			
            $query = sprintf($querybuild, $params[0], $params[1], $params[2]);
        }
        
        if($query){
            $vehicleresult = mysqli_query($conn, $query);
            $rows = mysqli_num_rows($vehicleresult);
        }
        else {
            header("Location: lookup.php");
        }
    }
    
    if (isset($_POST['newperson'])){
        $_SESSION['personfill']=True;
        header('Location: newentry.php');
    }
    if (isset($_POST['newvehicle'])){
        $_SESSION['vehiclefill']=True;
        header('Location: newentry.php');
    }
    if (isset($_POST['confirmperson'])){
        $_SESSION['addingowner'] = null;
        $_SESSION['ownerchosen'] = $_REQUEST['owner_id'];
        $_SESSION['ownerchosenfordb'] = $_REQUEST['owner_id'];
        header('Location: newowner.php');
    }
    if (isset($_POST['confirmvehicle'])){
        $_SESSION['addingvehicle'] = null;
        $_SESSION['vehicle'] = $_REQUEST['vehicle_id'];
        $_SESSION['vehiclefordb'] = $_REQUEST['vehicle_id'];
        header('Location: newowner.php');
    }
?> 
<html>
<head>
<title>Lookup</title>
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
            <form method="post">
                <label id="searchlabel">Person Lookup</label></br></br>
                <input type="text" placeholder="First name" name="fnamesearch">
                <input type="text" placeholder="Surname" name="lnamesearch">
                <button id="searchbutton" type="submit">Search by name</button>
                </br></br>
            </form>
            <form method="post">
                <label id="searchlabel">License Lookup</label></br></br>
                <input type="text" placeholder="License number" name="licensesearch">
                <button id="searchbutton" type="submit">Search by license number</button></br></br>
            </form>
            <?php
            if (isset($_POST['fnamesearch']) or isset($_POST['lnamesearch']) or isset($_POST['licensesearch'])){
            ?>
	    <h3>People Details</h3>
		<table border="1">
		    <tr>
		    <th>Person ID</th>
		    <th>First Name</th>
		    <th>Last Name</th>
		    <th>License</th>
		    <th>Address</th>
		    </tr>

		<?php
		if ($peopleresult->num_rows > 0){
		    while($row = $peopleresult->fetch_assoc()){
		?>
		    <tr>
		    <td><?php echo $row['people_id']; ?></td>
		    <td><?php echo $row['people_fname']; ?></td>
		    <td><?php echo $row['people_lname']; ?></td>
		    <td><?php echo $row['people_license']; ?></td>
		    <td><?php echo $row['people_address']; ?></td>
			
		    <?php
		    if(isset($_SESSION['addingowner'])){ 
		    
		    ?>

			<form method="post">
			    <?php $owner_id=$row['people_id']; ?>
			    <input type='hidden' name='owner_id' value=<?php echo $owner_id; ?></input>
			    <td><button id="searchbutton" type="submit" name="confirmperson">Select as owner</button></td>
			</form>
			
		<?php
		    }
		?>
		    </tr>
		    
		    <?php
		    }
		    
		}
		else {
		    ?>
		    <tr>
		    <th colspan="5">No person found</th>
		    </tr>
		    
		<form method="post">
		    <button id="searchbutton" type="submit" name="newperson">Add new person</button>
		</form>
            <?php        
		    }
		?>
		</table>
		<?php
		}
		 ?>

        </div>
	
        <div class="column_right">
            <form method="post">
                <label id="searchlabel">Vehicle plate Lookup</label></br></br>
                <input type="text" placeholder="Plate number" name="platesearch"></input>
                <button id="searchbutton" type="submit">Search by number plate</button>
                </br></br>
            </form>
            <form method="post">
                <label id="searchlabel">Vehicle description Lookup</label></br></br>
                <input type="text" placeholder="Make" name="makesearch">
                <input type="text" placeholder="Model" name="modelsearch"></input>
                <input type="text" placeholder="Colour" name="coloursearch"></input>
                <button id="searchbutton" type="submit">Search by description</button></br></br>
            </form>
            
            <?php
            if (isset($_POST['platesearch']) or isset($_POST['makesearch']) or isset($_POST['modelsearch']) or isset($_POST['coloursearch'])){
            ?>
                    <h3>Vehicle Details</h3>
                    <table border="1">
                        <tr>
                        <th>Vehicle ID</th>
                        <th>Plate number</th>
                        <th>Model ID</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Colour</th>
                        <th>Owner Person ID</th>
                        </tr>
                    <?php
                        if ($vehicleresult->num_rows > 0){
                            while($row = $vehicleresult->fetch_assoc()){
                            ?>
                                <tr>
                                <td><?php echo $row['vehicle_id']; ?></td>
                                <td><?php echo $row['vehicle_plate']; ?></td>
                                <td><?php echo $row['vehicle_model_id']; ?></td>
                                <td><?php echo $row['make']; ?></td>
                                <td><?php echo $row['model']; ?></td>
                                <td><?php echo $row['colour']; ?></td>
                                <td><?php echo $row['people_id'];?></td>                                
                                </tr>
                            
				<?php
				if(isset($_SESSION['addingvehicle'])){ 
		    
				?>
	    
				    <form method="post">
					<?php $vehicle_id=$row['vehicle_id']; ?>
					<input type='hidden' name='vehicle_id' value=<?php echo $vehicle_id; ?></input>
					<td><button id="searchbutton" type="submit" name="confirmvehicle">Select as vehicle</button></td>
				    </form>
				    
			    <?php
				}
			    ?>
                            
                            
                            
                            <?php
                            }
                        }
                        else {
                            ?>
                            <tr>
                            <th colspan="9">No vehicle found</th>
                            </tr> 
                            
			    <form method="post">
				<button id="searchbutton" type="submit" name="newperson">Add new vehicle</button>
			    </form>
                        <?php
                        }
			?>
			</table>
			<?php
		    }
                        ?>
        </div>
    </div>
</div>
<div id="versionno">v1.0<div> 
</body>

</html>