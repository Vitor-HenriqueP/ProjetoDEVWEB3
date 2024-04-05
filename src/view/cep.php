<?php
session_start();
include '../../conexao.php'; // Assumindo que este arquivo inclui a conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Insira os dados do endereço no banco de dados
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
        <div id="mensagem"></div> <!-- Div para exibir mensagem de sucesso ou erro -->
    </form>
    <a href="listar_enderecos.php">Listar Endereços</a>

    <script>
        $(document).ready(function() {
            $('#enderecoForm').submit(function(e) {
                e.preventDefault(); // Evita que o formulário seja enviado normalmente
                var formData = new FormData(this); // Cria um objeto FormData com os dados do formulário
                $.ajax({
                    type: 'POST',
                    url: 'cep.php', // Seu arquivo PHP para salvar o endereço
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
                    $('#mensagem').html(""); // Limpa a mensagem de erro
                    $('#cidade, #estado, #rua, #bairro').val(""); // Limpa os campos do endereço
                    $('#numero').val(""); // Limpa o campo do número

                    // Desabilita o botão de "Salvar Endereço"
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
                                $('#numero').focus(); // Coloca o foco no campo do número
                            }
                        },
                        complete: function() {
                            // Habilita o botão de "Salvar Endereço" após a requisição do CEP ser concluída
                            $('.button').prop('disabled', false);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>