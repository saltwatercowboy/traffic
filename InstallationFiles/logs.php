<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    require("db.php");
    
    if ($_SESSION['username']!='daniels') {
        header('Location: index.php');
        exit();
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
    
    $userquery="
        SELECT username FROM officer
        ";
    
    $usertable=mysqli_query($conn, $userquery);
    $rows = mysqli_num_rows($usertable);
    
    if (isset($_POST['user_select'])){
        
        $userid = $_REQUEST['user_search'];
        
        $query="
        SELECT * FROM user_logs
        WHERE username='$userid'
        ";
        
        $logtable=mysqli_query($conn, $query);
        $rows = mysqli_num_rows($logtable);
    }
    
?>

<html>
<head>
<title>Logs</title>
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
        <div class="users_table">
                <?php
               if(isset($usertable)) {
               ?>  
                <center>
                   <h3>User list</h3>
                   <table border="1">
                       <tr>
                       <th>Username</th>
                       </tr>
                   <?php
                   if ($usertable->num_rows > 0){
                           while($row = $usertable->fetch_assoc()){
                           ?>
                               <tr>
                               <td><?php echo $row['username']; ?></td>
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
        <div class='log_search'>
            <b>Choose a user to audit</b><br><br>
            <form action="" method="post">
                <input type="text" placeholder="Username" name="user_search"><br><br>
                <button id="ownerbutton" type="post" name="user_select">Select user</button>
            </form>
            <?php
            if (isset($_POST['user_select'])){
            ?>
                <br><br><h3>User Log</h3>
                <table style="table-layout: fixed; width: 100%" id ="users_table" border="1">
                    <tr>
                    <th>Action ID</th>
                    <th>Username</th>
                    <th>User Action</th>
                    <th>Timestamp</th>
                    </tr>

                <?php
                if ($logtable->num_rows > 0){
                    while($row = $logtable->fetch_assoc()){
                ?>
                    <tr>
                    <td><?php echo $row['action_id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td style="word-wrap: break-word"><?php echo $row['user_action']; ?></td>
                    <td><?php echo $row['action_time']; ?></td>
            <?php
                    }
                }
            }
            ?>
        </div>
    </div>
<div id="versionno">v1.0<div> 
</body>

</html>