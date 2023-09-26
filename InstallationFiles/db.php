<?php

$conn = mysqli_connect("mysql.cs.nott.ac.uk", "psxev3_traffic", "trafficdb", "psxev3_traffic");

if(mysqli_connect_errno()){
    echo "Failed to connect to
    MySQL:".mysqli_connect_error();
    die();
}

?>