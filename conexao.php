<?php

$host = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "projetodevweb3"; 


$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}


?>