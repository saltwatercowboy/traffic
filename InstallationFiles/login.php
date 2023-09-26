<?php
    session_start();
    $_SESSION['loggedin']=False;
    require("db.php");
    $userpasswrong = False;
    $result = $conn -> query("SELECT * FROM people");
    $_SESSION['numpeople']=$result -> num_rows;
    $result = $conn -> query("SELECT * FROM vehicle");
    $_SESSION['numvehicles']=$result -> num_rows;
    
    if (isset($_POST['username'])){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['pass'];
        
        $query = "SELECT * FROM officer WHERE username='$username' AND pass='$password'";
        $result = mysqli_query($conn, $query) or die(mysql_error());
        $rows = mysqli_num_rows($result);
        
        if ($rows==1){
            $_SESSION['username']=$username;
            $_SESSION['loggedin']=True;
            header('Location: index.php');
        }
        
        elseif ($rows!=1){
            $userpasswrong = True;
        }
    }
?>

<head>
<title>Log in</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=0.1">
<link rel ="stylesheet" href="db_style.css">

</head>
<body>
<div>
    <div class="navbar">
        <a class="nava" href="login.php">Nottinghamshire </br> Traffic Infractions </a>
        <div class="policeimg"></div>
        
    </div>
    <center>

        <form method="post">
            </br>
            <img src="image/notts_crest.png"> </img>
            <p>Please log in to continue.</p>
            <label for="username"><b>Username<b></b></label>
            <input type="text" placeholder="Enter username" name="username" required>
            </br>
            <label for="pass"><b>Password<b></b></label>
            <input type="password" placeholder="Enter password" name="pass" required>
            </br></br>
            <button type="submit">Log in</button>
            </br></br>
            <?php if ($userpasswrong==True){echo 'Username or password incorrect';}?>

        </form>
    </center>
</div>
<div id="versionno">v1.0<div> 
</body>
</html>