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
    <title>Estação Digital | Consulta de Endereço por CEP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecart.css">
</head>

<body>
    <h1>Consulta de Endereço por CEP</h1>

    <button id="cadastrarNovoEndereco">Cadastrar Novo Endereço</button>
    <button id="usarEnderecoExistente">Usar Endereço Existente</button>
    <div class="form-table">
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
            <div id="mensagem"></div> 
        </form>
    </div>
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
            function atualizarListaEnderecos() {
                $.ajax({
                    url: 'listar_enderecos.php',
                    success: function(data) {
                        $('#listaEnderecos').html(data);
                    }
                });
            }
            $('#cadastrarNovoEndereco').click(function() {
                $('#enderecoForm').show();
                $('#listaEnderecos, #enderecoSelecionado').hide();
                $('#cep, #cidade, #estado, #rua, #bairro, #numero').val('');
                $('#mensagem').text('');
            });
            $('#usarEnderecoExistente').click(function() {
                $('#enderecoForm').hide();
                $('#listaEnderecos').show();
                $('#enderecoSelecionado').text('');
                $('#mensagem').text('');
            });
            $(document).on('click', '.selecionarEndereco', function() {
                var cep = $(this).data('cep');
                var cidade = $(this).data('cidade');
                var estado = $(this).data('estado');
                var rua = $(this).data('rua');
                var bairro = $(this).data('bairro');
                var numero = $(this).data('numero');
                $('#cep').val(cep);
                $('#cidade').val(cidade);
                $('#estado').val(estado);
                $('#rua').val(rua);
                $('#bairro').val(bairro);
                $('#numero').val(numero);

                var enderecoSelecionado = "Endereço selecionado: " + rua + ", " + numero + " - " + bairro + " - " + cidade + "/" + estado + " - CEP: " + cep;
                $('#enderecoSelecionado').text(enderecoSelecionado);
            });

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
                        atualizarListaEnderecos(); 
                    },
                    error: function() {
                        $('#mensagem').text('Erro ao salvar endereço');
                    }
                });
            });

            $('#cep').keyup(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length === 8) {
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