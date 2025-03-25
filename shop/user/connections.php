<?php

$connections = mysqli_connect("localhost",username: "root",password: "",database: "mydb_rivera");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_errno();
    }

?>