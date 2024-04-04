<?php
session_start();
include '../../conexao.php';

if (!isset($_SESSION['login'])) {
    echo "Faça login para acessar o carrinho";
    exit();
}

if (isset($_POST['id_produto']) && isset($_POST['action'])) {
    $id_produto = intval($_POST['id_produto']);
    $id_usuario = intval($_SESSION['id']);

    if ($_POST['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();
    }
}
