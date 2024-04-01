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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

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

            // Adicione um formulário oculto para edição
            echo "<tr class='editAddressFormRow' style='display: none;'>";
            echo "<td colspan='6'>";
            echo "<form class='editAddressForm'>";
            echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
            echo "<label for='cep_edit'>CEP:</label>";
            echo "<input type='text' id='cep_edit' name='cep_edit' value='" . $row['cep'] . "' required><br><br>";
            echo "<label for='cidade_edit'>Cidade:</label>";
            echo "<input type='text' id='cidade_edit' name='cidade_edit' value='" . $row['cidade'] . "' required><br><br>";
            echo "<label for='estado_edit'>Estado:</label>";
            echo "<input type='text' id='estado_edit' name='estado_edit' value='" . $row['estado'] . "' required><br><br>";
            echo "<label for='rua_edit'>Rua:</label>";
            echo "<input type='text' id='rua_edit' name='rua_edit' value='" . $row['rua'] . "' required><br><br>";
            echo "<label for='bairro_edit'>Bairro:</label>";
            echo "<input type='text' id='bairro_edit' name='bairro_edit' value='" . $row['bairro'] . "' required><br><br>";
            echo "<label for='numero_edit'>Número:</label>";
            echo "<input type='text' id='numero_edit' name='numero_edit' value='" . $row['numero'] . "' required><br><br>";
            echo "<input type='submit' value='Salvar Edição'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
            echo '<div id="selectedAddress"></div>';
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
            // Captura o evento de click nos botões de "Editar"
            $('.editAddress').click(function() {
                // Oculta todos os formulários de edição que possam estar visíveis
                $('.editAddressFormRow').hide();

                var id = $(this).data('id');
                var editFormRow = $(this).closest('tr').next('.editAddressFormRow');
                editFormRow.show();

                // Adiciona um listener para o formulário de edição
                $('.editAddressForm').submit(function(event) {
                    event.preventDefault();

                    // Obtém os dados do formulário de edição
                    var formData = $(this).serialize();

                    // Envia os dados via AJAX para salvar a edição
                    $.ajax({
                        url: 'edit_address.php',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response === "success") {
                                // Atualiza a lista de endereços e mantém a tabela aberta
                                atualizarListaEnderecos();
                            } else {
                                $('#message').text("Erro ao salvar edição.");
                            }
                        },
                        error: function() {
                            $('#message').text("Erro ao salvar edição.");
                        }
                    });
                });
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

            // Função para atualizar a lista de endereços sem recarregar a página
            function atualizarListaEnderecos() {
                $.ajax({
                    url: 'listar_enderecos.php',
                    type: 'GET',
                    success: function(response) {
                        // Atualiza a tabela de endereços com os novos dados
                        $('#listaEnderecos').html(response);
                    },
                    error: function() {
                        $('#message').text("Erro ao atualizar lista de endereços.");
                    }
                });
            }
        });
    </script>
</body>

</html>