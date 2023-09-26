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
    
    $peoplesuccess = False;
    $vehiclesuccess = False;
    
    if (isset($_POST['addowner'])){
        header('Location: newowner.php');
        exit();
    }
    
    if (isset($_POST['addperson'])){
        
        $newfname = $_REQUEST['newfname'];
        $newlname = $_REQUEST['newlname'];
        $newlicense = $_REQUEST['newlicense'];
        $newaddress = $_REQUEST['newaddress'];
            
        $query = "INSERT INTO people (people_fname, people_lname, people_license, people_address) VALUES 
        ('$newfname', '$newlname', '$newlicense', '$newaddress')";
	
        mysqli_query($conn, $query);
	    $newpersonresult=mysqli_insert_id($conn);
	
        if ($newpersonresult) {
	    $peoplesuccess=True;
	    
	    $tocut = array(" ", "(", ")", "'");
	    $trimmedquery=str_replace($tocut, "_", $query);
	    
	    $logquery="
	    INSERT INTO user_logs (username, user_action) VALUES
	    ('$username', '$trimmedquery')
	    ";
	    mysqli_query($conn, $logquery);
	    
	    $query = " 
		SELECT * 
		FROM people 
		WHERE people_id='$newpersonresult'
		";
		
		$peopleinsertreport=mysqli_query($conn, $query);
		$rows = mysqli_num_rows($peopleinsertreport);
	    
            }
            
        else {
            echo 'Unsuccessful';
            }
            
        }
        
    if (isset($_POST['addvehicle'])){
        
        $newplate = $_REQUEST['newplate'];
        $newmake = $_REQUEST['newmake'];
        $newmodel = $_REQUEST['newmodel'];
        $newcolour = $_REQUEST['newcolour'];
                
        $query = "
        INSERT INTO vehicle (vehicle_plate) VALUE
        ('$newplate');";
                
        mysqli_query($conn, $query);

        $newvehicleresult=mysqli_insert_id($conn);
        $_SESSION['ownervehicle']=$newvehicleresult;
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
            
            
        if ($newvehicleresult){
            
            $query = "
                INSERT INTO ownership (vehicle_id) VALUE
                ('$newvehicleresult')";
                
            mysqli_query($conn, $query);
            
            $tocut = array(" ", "(", ")", "'");
            $trimmedquery=str_replace($tocut, "_", $query);
            $logquery="INSERT INTO user_logs (username, user_action) VALUES
            ('$username', '$trimmedquery')";
            mysqli_query($conn, $logquery);
            
            $query = "
                INSERT INTO model (vehicle_id, make, model, colour) VALUES
                ('$newvehicleresult', '$newmake', '$newmodel', '$newcolour');";
            
            $newmodelresult=mysqli_query($conn, $query);
            
            $tocut = array(" ", "(", ")", "'");
            $trimmedquery=str_replace($tocut, "_", $query);
            $logquery="INSERT INTO user_logs (username, user_action) VALUES
            ('$username', '$trimmedquery')";
            mysqli_query($conn, $logquery);
            
            if($newmodelresult) {
                echo ' Model insert successful';
                $vehiclesuccess = True;
                
                $query = " 
                SELECT * 
                FROM vehicle 
                JOIN (model) 
                    USING (vehicle_id)
                JOIN (ownership)
                    USING (vehicle_id)
                WHERE vehicle_id='$newvehicleresult'
                ";
                
                $vehicleinsertreport=mysqli_query($conn, $query);
                $rows = mysqli_num_rows($vehicleinsertreport);
                
                }
            
            else {
                echo ' Model insert unsuccessful';
                }
                
            }
        }
?>

<html>
<head>
<title>New Entry</title>
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
            <label id="searchlabel">Add New Person</label></br></br>
            <form action="" method="post">
                <?php
                if (isset($_SESSION['personfill'])){
                    if (isset($_SESSION['fname'])){
			            echo '<input type="text" placeholder="First name" name="newfname" value="'.$_SESSION['fname'].'" required></input>';
			            $_SESSION['fname']=null;
		    }
                    elseif (!isset($_SESSION['fname'])){
                        echo '<input type="text" placeholder="First name" name="newfname" required></input>';
                    }
                    if (isset($_SESSION['lname'])){
                        echo '<input type="text" placeholder="Surname" name="newlname" value="'.$_SESSION['lname'].'" required></input>';
			            $_SESSION['lname']=null;
                    }
                    elseif (!isset($_SESSION['lname'])){
                        echo '<input type="text" placeholder="Surname" name="newlname" required></input>';
                    }
                    if (isset($_SESSION['license'])){
                        echo '<input type="text" placeholder="License number" name="newlicense" value="'.$_SESSION['license'].'" required></input>';
			            $_SESSION['license']=null;
                    }
                    elseif (!isset($_SESSION['license'])){
                        echo '<input type="text" placeholder="License number" name="newlicense" required></input>';
                    }
                    echo '<input type="text" placeholder="Address" name="newaddress" required></input></br></br>';
                    echo '<button id="searchbutton" type="submit" name="addperson">Add person</button>';
                    echo '</form>';
                    unset($_SESSION['personfill']);
                }
                else {
                ?>  <form action="" method="post">
                        <input type="text" placeholder="First name" name="newfname" required></input></input>
                        <input type="text" placeholder="Surname" name="newlname" required></input></input>
                        <input type="text" placeholder="License number" name="newlicense" required></input>
                        <input type="text" placeholder="Address" name="newaddress" required></input></br></br>
                        <button id="searchbutton" type="submit" name="addperson">Add person</button>
                    </form>
            <?php } ?>
            </form>
	    
	    <?php
            if($peoplesuccess==True) {
            ?>  <h2>New person added!</h2>
                <h3>New Person Details</h3>
                <table border="1">
                    <tr>
                    <th>Person ID</th>
		            <th>First Name</th>
                    <th>Surname</th>
                    <th>License Num.</th>
		            <th>Address</th>
                    </tr>
                <?php
                if ($peopleinsertreport->num_rows > 0){
                    while($row = $peopleinsertreport->fetch_assoc()){
                    ?>
                        <tr>
                        <td><?php echo $row['people_id']; ?></td>
                        <td><?php echo $row['people_fname']; ?></td>
                        <td><?php echo $row['people_lname']; ?></td>
                        <td><?php echo $row['people_license']; ?></td>
                        <td><?php echo $row['people_address']; ?></td>
                        </tr>
                    </table>
                    <?php
                    }
                }
                    
            } ?>
	    
	    
            </br></br>
        </div>
        <div class="column_right">
            <label id="searchlabel"><b>Add New Vehicle<b></b></label></br></br>
            <form action="" method="post">
                <?php
                if ($_SESSION['vehiclefill']=True){
                    if (isset($_SESSION['plate'])){
			            echo '<input type="text" placeholder="Vehicle plate" name="newplate" value="'.$_SESSION['plate'].'" required></input>';
			            $_SESSION['plate']=null;
                    }
                    elseif (!isset($_SESSION['plate'])){
                        echo '<input type="text" placeholder="Vehicle plate" name="newplate" required></input>';
                    }
                    if (isset($_SESSION['make'])){
                        echo '<input type="text" placeholder="Vehicle make" name="newmake" value="'.$_SESSION['make'].'" required></input>';
			            $_SESSION['make']=null;
                    }
                    elseif (!isset($_SESSION['make'])){
                        echo '<input type="text" placeholder="Vehicle make" name="newmake" required></input>';
                    }
                    if (isset($_SESSION['model'])){
                        echo '<input type="text" placeholder="Model" name="newmodel" value="'.$_SESSION['model'].'" required></input>';
			            $_SESSION['model']=null;
                    }
                    elseif (!isset($_SESSION['model'])){
                        echo '<input type="text" placeholder="Model" name="newmodel" required></input>';
                    }
                    if (isset($_SESSION['colour'])){
                        echo '<input type="text" placeholder="Colour" name="newcolour" value="'.$_SESSION['colour'].'" required></input>';
			            $_SESSION['colour']=null;
                    }
                    elseif (!isset($_SESSION['colour'])){
                        echo '<input type="text" placeholder="Colour" name="newcolour" required></input>';
                    }
                    echo '</br></br>';
		            echo '<button id="searchbutton" type="submit" name="addvehicle">Add vehicle</button>';
		            echo '</form>';
                    $_SESSION['vehiclefill']=False;
                }
                else {
                ?>  <form action="" method="post">
                        <input type="text" placeholder="Vehicle plate" name="newplate" required></input></input>
                        <input type="text" placeholder="Make" name="newmake" required></input></input>
                        <input type="text" placeholder="Model" name="newmodel" required></input>
                        <input type="text" placeholder="Colour" name="newcolour" required></input></br></br>
                        <button id="searchbutton" type="submit" name="addvehicle">Add vehicle</button>
                    </form>
                    
            <?php } ?>
            </form>
            <?php
            if($vehiclesuccess==True) {
            ?>  <h2>New vehicle added!</h2>
                <h3>New Vehicle Details</h3>
                <table border="1">
                    <tr>
                    <th>Vehicle ID</th>
		            <th>Vehicle plate</th>
                    <th>Vehicle Model ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Colour</th>
		            <th>Owner ID</th>
                    </tr>
                <?php
                if ($vehicleinsertreport->num_rows > 0){
                    while($row = $vehicleinsertreport->fetch_assoc()){
                    ?>
                        <tr>
                        <td><?php echo $row['vehicle_id']; ?></td>
                        <td><?php echo $row['vehicle_plate']; ?></td>
                        <td><?php echo $row['vehicle_model_id']; ?></td>
                        <td><?php echo $row['make']; ?></td>
                        <td><?php echo $row['model']; ?></td>
                        <td><?php echo $row['colour']; ?></td>
                        <td><?php echo $row['people_id']; ?></td>
                        <form action="" method="post">
                        <td><button id="searchbutton" type="post" name="addowner">Add owner</button></td>
                        </form>
                        </tr>
                </table>
                    <?php
                    }
                    }
		    
		} ?>
            </br></br>
        </div>
        </div>
    </div>

<div id="versionno">v1.0<div> 
</body>

</html>
