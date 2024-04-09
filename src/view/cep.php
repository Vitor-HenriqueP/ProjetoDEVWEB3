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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Estação Digital | Consulta de Endereço por CEP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="src/view/assets/css/stylecarrinho.css">
</head>

<body>
    <h1>Consulta de Endereço por CEP</h1>
    <form id="enderecoForm" method="post">
        <h2>Endereço</h2>
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" maxlength="9"><br>
        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" readonly><br>
        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" readonly><br>
        <label for="rua">Rua:</label>
        <input type="text" id="rua" name="rua"><br>
        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro"><br>
        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero"><br>
        <input type="submit" value="Salvar Endereço" class="button">
        <div id="mensagem"></div> 
    </form>
    <a href="listar_enderecos.php">Listar Endereços</a>

    <script>
        $(document).ready(function() {
            $('#enderecoForm').submit(function(e) {
                e.preventDefault(); 
                var formData = new FormData(this); 
                $.ajax({
                    type: 'POST',
                    url: 'cep.php', 
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#mensagem').text('Endereço salvo com sucesso');

                    },
                    error: function() {
                        $('#mensagem').text('Erro ao salvar endereço');
                    }
                });
            });

            $('#cep').keyup(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 9) {
                    $('#mensagem').html("");
                    $('#cidade, #estado, #rua, #bairro').val("");
                    $('#numero').val("");
                    $('.button').prop('disabled', true);

                    $.ajax({
                        url: 'https://viacep.com.br/ws/' + cep + '/json/',
                        dataType: 'json',
                        success: function(data) {
                            if (!data.erro) {
                                $('#cidade').val(data.localidade);
                                $('#estado').val(data.uf);
                                $('#rua').val(data.logradouro);
                                $('#bairro').val(data.bairro);
                                $('#numero').focus();
                            }
                        },
                        complete: function() {
                            $('.button').prop('disabled', false);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>