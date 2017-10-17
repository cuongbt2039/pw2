<?php

function connector() {  
    $user = 'sql10198999';
    $password = 'yJq1qfEEjQ';
    $db = 'sql10198999';
    $host = 'sql10.freemysqlhosting.net';
    $port = 3306;

    $conn = new mysqli($host, $user, $password, $db, $port);

    return $conn;
}
