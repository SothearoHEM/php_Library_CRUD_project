<?php
    $host = 'sql105.infinityfree.com';
    $username = 'if0_41335020';
    $password = 'crud60857123'; 
    $database = 'if0_41335020_library_db';
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
 ?>