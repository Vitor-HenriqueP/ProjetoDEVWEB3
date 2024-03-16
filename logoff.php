<?php
//Codigo para destruir a sessão ao clicar no botão sair.
session_start();
session_destroy();
header("Location: index.php");
?>
