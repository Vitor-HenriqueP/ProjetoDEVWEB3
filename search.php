<?php
$host = "localhost"; // host do banco de dados
$username = "root"; // nome de usuário do banco de dados
$password = ""; // senha do banco de dados
$dbname = "ProjetoDEVWEB3"; // nome do banco de dados

// Conexão com o banco de dados
$conn = new mysqli($host, $username, $password, $dbname);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Verifica se o parâmetro 'q' foi passado na URL (query string)
if (isset($_GET['q'])) {
    $searchQuery = "%" . $_GET['q'] . "%";

    // Consulta SQL com prepared statement para buscar produtos com base na pesquisa
    $sql = "SELECT id, nome, descricao, preco, imagem FROM produto WHERE nome LIKE ? OR descricao LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($row["imagem"]) . "'>";
            echo "<div class='card-content'>";
            echo "<h3>" . $row["nome"] . "</h3>";
            echo "<p>" . $row["descricao"] . "</p>";
            echo "<p class='price'>R$" . number_format($row["preco"], 2, ',', '.') . "</p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "Nenhum produto encontrado.";
    }
    $stmt->close();
} else {
    echo "Nenhum termo de pesquisa especificado.";
}

$conn->close();
?>
