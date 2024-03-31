<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Se o formulário foi submetido, processa o salvamento do endereço
    $cep = $_POST['cep'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    $numero = $_POST['numero'];
    $usuario_id = $_SESSION['id'];

    // Verifica se todos os campos estão preenchidos
    if (empty($cep) || empty($cidade) || empty($estado) || empty($rua) || empty($bairro) || empty($numero) || empty($usuario_id)) {
        echo "Todos os campos são obrigatórios.";
        exit;
    }

    // Insere os dados do endereço no banco de dados
    $stmt = $conn->prepare("INSERT INTO enderecos (cep, cidade, estado, rua, bairro, numero, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $cep, $cidade, $estado, $rua, $bairro, $numero, $usuario_id);

    if ($stmt->execute()) {
        echo "Endereço salvo com sucesso.";
    } else {
        echo "Erro ao salvar endereço.";
    }
}

// Consulta os endereços do usuário logado
$usuario_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Exibe a página com o formulário e a lista de endereços
?>
<!DOCTYPE html>
<html>

<head>
    <title>Consulta de Endereço por CEP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="selectedAddress"></div> <!-- Div para exibir o endereço selecionado -->

    <form method="post" action="">
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required><br><br>
        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required><br><br>
        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" required><br><br>
        <label for="rua">Rua:</label>
        <input type="text" id="rua" name="rua" required><br><br>
        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" required><br><br>
        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" required><br><br>
        <input type="submit" value="Salvar">
    </form>

    <?php
    if ($result->num_rows > 0) {
        echo "<h2>Seus Endereços</h2>";
        echo "<table border='1'>";
        echo "<tr><th>CEP</th><th>Cidade</th><th>Estado</th><th>Rua</th><th>Bairro</th><th>Número</th><th>Selecionar</th><th>Editar</th><th>Excluir</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['cep'] . "</td>";
            echo "<td>" . $row['cidade'] . "</td>";
            echo "<td>" . $row['estado'] . "</td>";
            echo "<td>" . $row['rua'] . "</td>";
            echo "<td>" . $row['bairro'] . "</td>";
            echo "<td>" . $row['numero'] . "</td>";
            echo "<td><button class='selectAddress' data-cep='" . $row['cep'] . "' data-cidade='" . $row['cidade'] . "' data-estado='" . $row['estado'] . "' data-rua='" . $row['rua'] . "' data-bairro='" . $row['bairro'] . "' data-numero='" . $row['numero'] . "'>Selecionar</button></td>";
            echo "<td><button class='editAddress' data-id='" . $row['id'] . "'>Editar</button></td>";
            echo "<td><button class='deleteAddress' data-id='" . $row['id'] . "'>Excluir</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum endereço encontrado.";
    }
    ?>


    <script>
        $(document).ready(function() {
            // Captura o evento de click nos botões de "Selecionar Endereço"
            $('.selectAddress').click(function() {
                var cep = $(this).data('cep');
                var cidade = $(this).data('cidade');
                var estado = $(this).data('estado');
                var rua = $(this).data('rua');
                var bairro = $(this).data('bairro');
                var numero = $(this).data('numero');

                var addressText = "Endereço selecionado: " + rua + ", " + numero + " - " + bairro + " - " + cidade + " - " + estado + " - CEP: " + cep;
                $('#selectedAddress').text(addressText);
            });

            // Captura o evento de click nos botões de "Excluir"

                $('.deleteAddress').click(function() {
                var id = $(this).data('id');

                $.ajax({
                    url: 'delete_address.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response === "success") {
                            // Atualiza a lista de endereços e mantém a tabela aberta
                            atualizarListaEnderecos();
                        } else {
                            $('#message').text("Erro ao excluir endereço.");
                        }
                    },
                    error: function() {
                        $('#message').text("Erro ao excluir endereço.");
                    }
                });
            });



            // Captura o evento de click nos botões de "Editar"
            $('.editAddress').click(function() {
                var id = $(this).data('id');

                // Redireciona para a página de edição com o ID do endereço
                window.location.href = 'edit_address.php?id=' + id;
            });
        });
    </script>
</body>

</html>