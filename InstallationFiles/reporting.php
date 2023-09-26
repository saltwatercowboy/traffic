<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    require("db.php");
    $username=$_SESSION['username'];
    date_default_timezone_set('UTC');
    
    $successfullyedited=null;
    $successfullycreated=null;
    
    
    $offencequery="
        SELECT * FROM offence
        ";
        
    $offencetable=mysqli_query($conn, $offencequery);
    $rows = mysqli_num_rows($offencetable);
    
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
    

    if (isset($_POST['incidentsearch']) or isset($_POST['offendersearch'])){
        $incident = $_REQUEST['incidentsearch'];
        $_SESSION['incident'] = $incident;
        $offender = $_REQUEST['offendersearch'];
        $_SESSION['offender'] = $offender;
        
        $query = "SELECT * FROM incident WHERE incident_id='$incident' OR people_id='$offender'";
        $incidentresult = mysqli_query($conn, $query) or die(mysql_connect_error());
        $rows = mysqli_num_rows($incidentresult);
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
        
    }
    
    if (isset($_POST['incident_to_edit'])) {
        $currentreport = $_REQUEST['incident_edit_id'];
        $_SESSION['currentreport']=$currentreport;
        
        $query="
        SELECT * FROM incident
        WHERE incident_id='$currentreport'
        ";
        
        $activereport = mysqli_query($conn, $query) or die(mysql_connect_error());
        $rows = mysqli_num_rows($activereport);
    }
    
    if (isset($_POST['edited_incident'])) {
        $currentreport=$_SESSION['currentreport'];
        $newoffender = $_REQUEST['newoffender'];
        $newvehicle = $_REQUEST['newvehicle'];
        $newoffenceid = $_REQUEST['newoffenceid'];
        $newdate = date("Y-m-d", strtotime($_REQUEST['newdate']));
        $newreport = $_REQUEST['newreport'];
        
        $query="
        UPDATE incident
            SET people_id='$newoffender', vehicle_id='$newvehicle', incident_date='$newdate', incident_report='$newreport', offence_id='$newoffenceid'
        WHERE incident_id='$currentreport'
        ";
        
        $editedreport = mysqli_query($conn, $query);
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
        
        if($editedreport) {
            $successfullyedited='Y';
            }   
        else {
            $successfullyedited='N';
        }
    }
    
    if (isset($_POST['makereport'])) {
        $createoffender = $_REQUEST['addoffender'];
        $createvehicle = $_REQUEST['addvehicle'];
        $createoffenceid = $_REQUEST['addoffenceid'];
        $createdate = date("Y-m-d", strtotime($_REQUEST['addnewdate']));
        $createreport = $_REQUEST['adddescription'];
        
        $query="
        INSERT INTO incident (people_id, vehicle_id, incident_date, incident_report, offence_id) VALUES
        ('$createoffender', '$createvehicle', '$createdate', '$createreport', '$createoffenceid')";
        
        $createdreport = mysqli_query($conn, $query);
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
        
        if($createdreport) {
            $successfullycreated='Y';
            }   
        else {
            $successfullycreated='N';
        }
    }
?>

<html>
<head>
<title>Reports</title>
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
        <div class="reporting_column_left">
            <form method="post">
                <label id="searchlabel">Report Lookup</label></br></br>
                <input type="text" placeholder="Incident ID" name="incidentsearch">
                <input type="text" placeholder="Offender (Person ID)" name="offendersearch">
                </br></br><button id="searchbutton" type="submit">Search Reports</button>
                </br></br>
            </form>
            <?php
            if (isset($_POST['incidentsearch']) or isset($_POST['offendersearch'])){
            ?>
                    <h3>Incident details</h3>
                    <table border="1">
                        <tr>
                        <th>Incident ID</th>
                        <th>Offender (Person ID)</th>
                        <th>Vehicle ID</th>
                        <th>Offence ID</th>
                        <th>Incident Date</th>
                        <th>Incident Report</th>
                        </tr>
                    <?php
                        if ($incidentresult->num_rows > 0){
                            while($row = $incidentresult->fetch_assoc()){
                            ?>
                                <tr>
                                <td><?php echo $row['incident_id']; ?></td>
                                <td><?php echo $row['people_id']; ?></td>
                                <td><?php echo $row['vehicle_id']; ?></td>
                                <td><?php echo $row['offence_id']; ?></td>
                                <td><?php echo $row['incident_date']; ?></td>
                                <td><?php echo $row['incident_report']; ?></td>                            
                                </tr>
                            
                                <form method="post">
                                    <input type='hidden' name='incident_edit_id' value=<?php echo $row['incident_id']; ?>></input>
                                    <td><button id="searchbutton" type="submit" name="incident_to_edit">Edit this report</button></td>
                                </form>
                                    
                            <?php
                                }
                            }
                        
                        else {
                            ?>
                            <tr>
                            <th colspan="6">No incident found</th>
                            </tr>
                        <?php
                        }
                        ?>
                        </table>
                        <?php
                    }
                        ?>
            
            
        </div>
        <div class="reporting_column_right">
            <label id="searchlabel">Edit Report</label></br>
            <?php
            
            if($successfullyedited=='Y') {
                     echo '</br><b>Report '.$currentreport.' successfully amended</b>';
                     $successfullyedited=null;
                 }
                 
                 if($successfullyedited=='N') {
                     echo '</br><b>Failed - check that the vehicle or person is already in the database elsewhere and that your date is in YYYY-MM-DD format</b>';
                 }
            
            if (!isset($_POST['incident_to_edit'])){
            ?>
                <p> Select report in search </p>
                <?php
                if($successfullyedited=='Y') {
                     echo '<b>Report '.$currentreport.' successfully amended</b>';
                     $successfullyedited=null;
                 }
                 
                if($successfullyedited=='N') {
                     echo '<b>Failed - check that the vehicle or person is already in the database elsewhere and that your date is in YYYY-MM-DD format</b>';
                 }

                if($successfullycreated=='Y'){
                    echo '<p> Report successfully added </p>';
                }
            
            }
            
            if (isset($_POST['incident_to_edit'])){
            ?>

                <h3>Report <?php echo $currentreport; ?> details</h3>
                    <hr></hr>
                    <table border="1">
                        <tr>
                        <th>Incident ID</th>
                        <th>Offender (Person ID)</th>
                        <th>Vehicle ID</th>
                        <th>Offence ID</th>
                        <th>Incident Date</th>
                        <th>Incident Report</th>
                        </tr>
                    <?php
                        if ($activereport->num_rows > 0){
                            while($row = $activereport->fetch_assoc()){
                            ?>
                                <tr>
                                <form method="post">
                                    <td><?php echo $currentreport; ?></td>
                                    <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="newoffender" value= <?php echo $row['people_id']?>></input></td>
                                    <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="newvehicle" value= <?php echo $row['vehicle_id']?>></input></td>
                                    <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="newoffenceid" value= <?php echo $row['offence_id']?>></input></td>
                                    <td><input type="text" placeholder="Must be YYYY-MM-DD" name="newdate" value= <?php echo $row['incident_date']?>></input></td>
                                    <td><textarea id="descriptionform" type="text" name="newreport"><?php echo $row['incident_report']?></textarea></td>
                                    <td><button id="searchbutton" type="submit" name="edited_incident">Amend Report</button></td>
                                </form>
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
        <?php
        if(isset($_POST['addreport'])) {
            ?>  
            <div class="reporting_new_report_column">
                <h3>New report details</h3>
                    <table border="1">
                        <tr>
                        <th>Offender (Person ID)</th>
                        <th>Vehicle ID</th>
                        <th>Offence ID</th>
                        <th>Incident Date</th>
                        <th>Incident Report</th>
                        </tr>
                        <tr>
                        <form method="post">
                            <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="addoffender" required></input></td>
                            <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="addvehicle" required></input></td>
                            <td id="smallinputform"><input id="smallinputform" type="text" placeholder="ID" name="addoffenceid"] required></input></td>
                            <td><input type="text" placeholder="Must be YYYY-MM-DD" name="addnewdate" required></input></td>
                            <td><textarea id="descriptionform" type="text" placeholder="Incident details" name="adddescription" required></textarea></td>
                            <td><button id="searchbutton" type="submit" name="makereport">Add Report</button></td>
                        </form>
                        </tr>
                    </table>
                    <?php

                if(isset($offencetable)) {
                ?>  
                <center>
                   <h3>Offences</h3>
                   <table border="1">
                       <tr>
                       <th>Offence ID</th>
                       <th>Offence Type</th>
                       <th>Max Fine</th>
                       <th>Max Points</th>
                       </tr>
                   <?php
                   if ($offencetable->num_rows > 0){
                           while($row = $offencetable->fetch_assoc()){
                           ?>
                               <tr>
                               <td><?php echo $row['offence_id']; ?></td>
                               <td><?php echo $row['offence_description']; ?></td>
                               <td><?php echo $row['offence_maxfine']; ?></td>
                               <td><?php echo $row['offence_maxpoints']; ?></td>
                               </tr>
                       
                           <?php
                           }
                       }
                   } 
                   ?>
                   </table>
                   <br>
               </center>
            </div>
            <?php
            }
            
        elseif(!isset($_POST['addreport'])) {
        ?>
        <div>
            <form method="post">
            <br>
               <button id="ownerbutton" name="addreport" type="submit">Create New Report</button>
            <form>
        </div>
        <?php
        }
        ?>
    </div>
<div id="versionno">v1.0<div> 
</body>

</html>
