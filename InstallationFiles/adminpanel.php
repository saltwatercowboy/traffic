<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    $username=$_SESSION['username'];
    require("db.php");
    
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
    
    if ($_SESSION['username']!='daniels') {
        header('Location: index.php');
        exit();
    }
    
    if (isset($_POST['add_officer'])){
        
        $newusername = $_REQUEST['username'];
        $newpassword = $_REQUEST['pass'];
            
        $query = "INSERT INTO officer (username, pass) VALUES 
        ('$newusername', '$newpassword')";
        
        $newofficerresult=mysqli_query($conn, $query);
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
        
    }
    
    if (isset($_POST['add_fine'])){
        
        $incidentid = $_REQUEST['incidentid'];
        $fineamount = $_REQUEST['fineamount'];
        $finepoints = $_REQUEST['finepoints'];
            
        $query = "INSERT INTO fine (incident_id, fine_amount, fine_points) VALUES 
        ('$incidentid', '$fineamount', '$finepoints')";
        
        $newfineresult=mysqli_query($conn, $query);
        
        $tocut = array(" ", "(", ")", "'");
        $trimmedquery=str_replace($tocut, "_", $query);
        $logquery="INSERT INTO user_logs (username, user_action) VALUES
        ('$username', '$trimmedquery')";
        mysqli_query($conn, $logquery);
    }
?>

<html>
<head>
<title>Admin Control Panel</title>
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
    <div>
        <div class="admin_boxes">
            <center>
                <br>
                <label id="adminlabel">Add Officer Account</label><br><br><br>
                <form action="" method="post">
                    <label for="username"><b>Officer Username<b></b></label>
                    <input type="text" placeholder="Enter new username" name="username" required>
                    </br></br>
                    <label for="pass"><b>Officer Password<b></b></label>
                    <input type="text" placeholder="Enter new password" name="pass" required><br><br>
                    <button id="confirmbutton" type="post" name="add_officer">Add New Officer</button>
                </form>
                <?php
                if(isset($newofficerresult)){
                    echo '<br><br><b>Officer account '.$newusername.' successfully added</b>';
                }
            ?>
            </center>
        </div>
        <div class='row'>
            <div class='column_left'>
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
            <div class='column_right'>
                <center>
                    <br>
                    <label id="adminlabel">Add Fine</label><br><br><br>
                    <form action="" method="post">
                        <label for="username"><b>Incident ID<b></b></label>
                        <input type="text" placeholder="Enter incident ID" name="incidentid" required>
                        </br></br></br>
                        <label for="pass"><b>Fine Amount<b></b></label>
                        <input type="text" placeholder="Enter amount incurred" name="fineamount" required><br><br></br>
                        <label for="pass"><b>Fine Points<b></b></label>
                        <input type="text" placeholder="Enter points incurred" name="finepoints" required><br><br></br>
                        <button id="confirmbutton" type="post" name="add_fine">Add Fine</button></br></br>
                    <?php
                if(isset($newfineresult)){
                     echo '<br><br><b>Fine successfully added to incident '.$incidentid.'</b><br><br><br>';
                }
            ?>
                </center>
            </div>
        </div>
    </div>
<div id="versionno">v1.0<div> 
</body>

</html>
