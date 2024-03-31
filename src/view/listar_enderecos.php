<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if (isset($_SESSION['id'])) {
    $usuario_id = $_SESSION['id'];
    
    // Consulta os endereços do usuário logado
    $stmt = $conn->prepare("SELECT * FROM enderecos WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Exibe os endereços em uma tabela
        echo "<h2>Seus Endereços</h2>";
        echo "<table border='1'>";
        echo "<tr><th>CEP</th><th>Cidade</th><th>Estado</th><th>Rua</th><th>Bairro</th><th>Número</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['cep'] . "</td>";
            echo "<td>" . $row['cidade'] . "</td>";
            echo "<td>" . $row['estado'] . "</td>";
            echo "<td>" . $row['rua'] . "</td>";
            echo "<td>" . $row['bairro'] . "</td>";
            echo "<td>" . $row['numero'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum endereço encontrado.";
    }
} else {
    echo "Usuário não logado.";
}
?>
