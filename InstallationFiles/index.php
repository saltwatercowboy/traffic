<?php
    session_start();
    if ($_SESSION['loggedin']==False){
        header('Location: login.php');
    }
    require("db.php");
    
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
    
    if (isset($_POST['admin_page'])){
        header('Location: adminpanel.php');
    }
    
    if (isset($_POST['logs'])){
        header('Location: logs.php');
    }
?>

<html>
<head>
<title>Home</title>
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
            <p> Select an option above. </p>
            </br></br>
        </div>
        <div class="column_right">
            <p> <?php echo $_SESSION['username']; ?> logged in. </br></br>
            <?php echo $_SESSION['numpeople'] ?> people in database. <?php echo $_SESSION['numvehicles'] ?> vehicles in database. </p>
        </div>
        <?php
        if ($_SESSION['username']=='daniels') {
        ?>
        <div class="admin_panel">
            <label id="searchlabel">Admin Panel</label><br><br>
            <form action="" method="post">
                <button id="searchbutton" type="post" name="admin_page">Go to admin page</button>
            </form>
            <form action="" method="post">
                <button id="ownerbutton" type="post" name="logs">Go to logs</button>
            </form>
        </div>
        <?php
        }
        ?>
    </div>
<div id="versionno">v1.0<div> 
</body>

</html>
