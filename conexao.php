<?php
// Informações de conexão com o banco de dados
$host = "localhost"; // host do banco de dados
$username = "root"; // nome de usuário do banco de dados
$password = ""; // senha do banco de dados
$dbname = "projetodevweb3"; // nome do banco de dados

// Conexão com o banco de dados
$conn = new mysqli($host, $username, $password, $dbname);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Não feche a conexão aqui
?>