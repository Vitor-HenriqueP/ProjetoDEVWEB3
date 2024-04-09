<?php

session_start();
include '../../conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = $_POST['cep'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    $numero = $_POST['numero'];
    $usuario_id = $_SESSION['id'];
    if (empty($cep) || empty($cidade) || empty($estado) || empty($rua) || empty($bairro) || empty($numero) || empty($usuario_id)) {
        echo "Todos os campos são obrigatórios.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO enderecos (cep, cidade, estado, rua, bairro, numero, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $cep, $cidade, $estado, $rua, $bairro, $numero, $usuario_id);
    if ($stmt->execute()) {
        echo "Endereço salvo com sucesso.";
    } else {
        echo "Erro ao salvar endereço.";
    }
}
$usuario_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/stylelistarendereco.css">
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
        echo "<section>";
        echo "<h2>Seus Endereços</h2>";
        echo "<table >";
        echo "<tr><th>CEP</th><th>Cidade</th><th>Estado</th><th>Rua</th><th>Bairro</th><th>Número</th><th>Selecionar</th><th>Editar</th><th>Excluir</th></tr>";
        echo "</section>";
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
            $('.editAddress').click(function() {
                $('.editAddressFormRow').hide();

                var id = $(this).data('id');
                var editFormRow = $(this).closest('tr').next('.editAddressFormRow');
                editFormRow.show();
                $('.editAddressForm').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: 'edit_address.php',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response === "success") {
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
            function atualizarListaEnderecos() {
                $.ajax({
                    url: 'listar_enderecos.php',
                    type: 'GET',
                    success: function(response) {
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