<?php

    $dbHost = "localhost";
    $dbUsername = "root";
    $dbPassword ="";
    $dbName = "school";

    $conn = mysqli_connect($dbHost,$dbUsername,$dbPassword,$dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

?>