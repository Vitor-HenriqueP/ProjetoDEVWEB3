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

?>
<!DOCTYPE html>
<html>

<head>
    <title>Consulta de Endereço por CEP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>Consulta de Endereço por CEP</h1>

    <button id="cadastrarNovoEndereco">Cadastrar Novo Endereço</button>
    <button id="usarEnderecoExistente">Usar Endereço Existente</button>

    <form id="enderecoForm" method="post" style="display: none;">
        <h2>Endereço</h2>
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" maxlength="8"><br>
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

    <div id="enderecoSelecionado"></div>

    <table id="listaEnderecos" border="1" style="display: none;">
        <tr>
            <th>CEP</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Rua</th>
            <th>Bairro</th>
            <th>Número</th>
            <th>Selecionar</th>
        </tr>
        <?php
        function listarEnderecos($conn, $usuario_id)
        {
            $stmt = $conn->prepare("SELECT * FROM enderecos WHERE usuario_id = ?");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['cep'] . "</td>";
                    echo "<td>" . $row['cidade'] . "</td>";
                    echo "<td>" . $row['estado'] . "</td>";
                    echo "<td>" . $row['rua'] . "</td>";
                    echo "<td>" . $row['bairro'] . "</td>";
                    echo "<td>" . $row['numero'] . "</td>";
                    echo "<td><button class='selecionarEndereco' data-cep='" . $row['cep'] . "' data-cidade='" . $row['cidade'] . "' data-estado='" . $row['estado'] . "' data-rua='" . $row['rua'] . "' data-bairro='" . $row['bairro'] . "' data-numero='" . $row['numero'] . "'>Selecionar</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum endereço encontrado.</td></tr>";
            }
        }

        listarEnderecos($conn, $_SESSION['id']);
        ?>
    </table>

    <script>
        $(document).ready(function() {
            // Função para atualizar a lista de endereços
            function atualizarListaEnderecos() {
                $.ajax({
                    url: 'listar_enderecos.php', // Arquivo PHP para listar os endereços
                    success: function(data) {
                        $('#listaEnderecos').html(data);
                    }
                });
            }

            // Ao clicar no botão "Cadastrar Novo Endereço"
            $('#cadastrarNovoEndereco').click(function() {
                $('#enderecoForm').show();
                $('#listaEnderecos, #enderecoSelecionado').hide();
                $('#cep, #cidade, #estado, #rua, #bairro, #numero').val('');
                $('#mensagem').text('');
            });

            // Ao clicar no botão "Usar Endereço Existente"
            $('#usarEnderecoExistente').click(function() {
                $('#enderecoForm').hide();
                $('#listaEnderecos').show();
                $('#enderecoSelecionado').text('');
                $('#mensagem').text('');
            });

            // Ao clicar em um botão "Selecionar" da tabela de endereços
            $(document).on('click', '.selecionarEndereco', function() {
                var cep = $(this).data('cep');
                var cidade = $(this).data('cidade');
                var estado = $(this).data('estado');
                var rua = $(this).data('rua');
                var bairro = $(this).data('bairro');
                var numero = $(this).data('numero');

                // Preenche os campos do formulário com os dados do endereço selecionado
                $('#cep').val(cep);
                $('#cidade').val(cidade);
                $('#estado').val(estado);
                $('#rua').val(rua);
                $('#bairro').val(bairro);
                $('#numero').val(numero);

                // Exibe uma mensagem indicando o endereço selecionado
                var enderecoSelecionado = "Endereço selecionado: " + rua + ", " + numero + " - " + bairro + " - " + cidade + "/" + estado + " - CEP: " + cep;
                $('#enderecoSelecionado').text(enderecoSelecionado);
            });

            // Ao enviar o formulário de cadastro de endereço
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
                        atualizarListaEnderecos(); // Atualiza a lista de endereços após salvar
                    },
                    error: function() {
                        $('#mensagem').text('Erro ao salvar endereço');
                    }
                });
            });

            // Ao digitar o CEP
            $('#cep').keyup(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $('#mensagem').html(""); // Limpa a mensagem de erro
                    $('#cidade, #estado, #rua, #bairro').val(""); // Limpa os campos do endereço
                    $('#numero').val(""); // Limpa o campo do número

                    // Desabilita o botão de "Salvar Endereço"
                    $('.button').prop('disabled', true);

                    // Consulta o CEP na API viacep
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