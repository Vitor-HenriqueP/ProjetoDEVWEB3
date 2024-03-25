<?php
session_start();

// Verifica se o usuário está logado (ou faz a verificação de segurança necessária)

// Verifica se o ID do produto e o comentário foram enviados via POST
if (isset($_POST['id_produto'], $_POST['comentario'])) {
    $idProduto = $_POST['id_produto'];
    $comentario = $_POST['comentario'];

    // Aqui você deve inserir o comentário no banco de dados, associando-o ao produto

    // Exemplo de inserção no banco de dados (substitua pelos seus próprios dados)
    // $pdo = new PDO('mysql:host=localhost;dbname=seu_banco_de_dados', 'seu_usuario', 'sua_senha');
    // $stmt = $pdo->prepare('INSERT INTO comentarios (id_produto, comentario) VALUES (?, ?)');
    // $stmt->execute([$idProduto, $comentario]);

    echo 'Comentário enviado com sucesso'; // Resposta de sucesso para o AJAX
} else {
    echo 'Erro ao enviar o comentário'; // Resposta de erro para o AJAX
}
?>
