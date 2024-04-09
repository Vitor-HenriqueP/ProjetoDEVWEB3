<?php
session_start();

include '../../conexao.php';
function logout()
{
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    logout();
}
if (!isset($_SESSION['login'])) {
    echo '<script>alert("Faça login para acessar o carrinho");</script>';
    echo '<script>setTimeout(function(){ window.location.href = "../../login.php"; });</script>';
    exit();
}

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;

if ($tipo_usuario !== null && $tipo_usuario != 2) {
    echo '<script>alert("Você não tem permissão para acessar esta página.");</script>';
    echo '<script>setTimeout(function(){ window.location.href = "../../index.php"; });</script>';
    exit();
}

if (isset($_POST['id_produto']) && isset($_POST['action'])) {
    $id_produto = intval($_POST['id_produto']);
    $id_usuario = intval($_SESSION['id']);

    if ($_POST['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();
        $mensagem = "Produto adicionado ao carrinho.";
        echo "<script>setTimeout(function() { document.getElementById('mensagem').style.display = 'none'; }, 3000);</script>";
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    }
}


if (isset($_POST['slug']) && isset($_POST['action'])) {
    $slug = $_POST['slug'];
    $id_usuario = intval($_SESSION['id']);

    if ($_POST['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto IN (SELECT id FROM produto WHERE slug = ?) LIMIT 1");
        $stmt->bind_param("is", $id_usuario, $slug);
        $stmt->execute();

        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } elseif ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) SELECT ?, id FROM produto WHERE slug = ?");
        $stmt->bind_param("is", $id_usuario, $slug);
        $stmt->execute();

        $mensagem = "Produto adicionado ao carrinho.";
        echo "<script>setTimeout(function() { document.getElementById('mensagem').style.display = 'none'; }, 3000);</script>";

        header("Location: $_SERVER[PHP_SELF]");
        exit();
    }
}

$stmt_quantidade = $conn->prepare("SELECT COUNT(id_produto) as quantidade FROM carrinho WHERE id_usuario = ?");
$stmt_quantidade->bind_param("i", $id_usuario);
$stmt_quantidade->execute();
$result_quantidade = $stmt_quantidade->get_result();
$row_quantidade = $result_quantidade->fetch_assoc();
$_SESSION['quantidade_carrinho'] = $row_quantidade['quantidade'];
$stmt_quantidade->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estação Digital | Carrinho</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecart.css">
    <link rel="shortcut icon" type="image/png" href="assets/imagens/cart2.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function alterarQuantidade(id_produto, action) {
            $.ajax({
                type: "POST",
                url: "alterar_quantidade.php",
                data: {
                    id_produto: id_produto,
                    action: action
                },
                success: function(response) {
                    location.reload(); 
                }
            });
        }
    </script>
</head>

<body>
    <div class="header" id="header">
        <button onclick="toggleSidebar()" class="btn_icon_header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
            </svg>
        </button>
        <div class="logo_header">
            <a href="../../index.php"><img src="./assets/imagens/newlogo.png" alt="Logo Estação Digital" class="img_logo_header"></a>
        </div>
        <div class="navigation_header" id="navigation_header">
            <button onclick="toggleSidebar()" class="btn_icon_header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </button>
            <?php if (!isset($_SESSION['login'])) : ?>
                <a href="../../login.php"><span class="material-symbols-outlined">login</span></a>
            <?php endif; ?>
            <?php if (isset($_SESSION['login'])) : ?>
                <a href="../../user_config.php"><span class="material-symbols-outlined">person</span></a>
            <?php endif; ?>

            <?php if (isset($_SESSION['login'])) : ?>
                <form method="post" action="../../index.php">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="button" style="background: none; border: none; cursor: pointer;">
                        <a><span class="material-symbols-outlined" style="vertical-align: middle;">logout</span></a>
                    </button>
                </form>

            <?php endif; ?>

        </div>
    </div>
    <div class="containerOferta">
        <span class="oferta">Carrinho</span>
    </div>

    <?php
    $id_usuario = intval($_SESSION['id']);
    $stmt = $conn->prepare("SELECT p.slug,c.id_produto, COUNT(c.id_produto) as quantidade, p.nome, p.descricao, p.preco, p.imagem FROM carrinho c INNER JOIN produto p ON c.id_produto = p.id WHERE c.id_usuario = ? GROUP BY c.id_produto");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $total = 0; 

        while ($row = $result->fetch_assoc()) {
            $total += $row['preco'] *
                $row['quantidade']; 
            $nome = $row['nome'];
            $descricao = explode(' ', $row["descricao"]);
            $descricao = array_slice($descricao, 0, 20);
            $descricao = implode(' ', $descricao);

            echo "<div class='product-container'>";
            echo "<div class='product-image-wrapper'>";
            echo "<form method='get' action='produto.php'>";
            echo "<input type='hidden' name='slug' value='" . htmlspecialchars($row["slug"]) . "'>";
            echo "<button type='submit' style='border: none; background: none; padding: 0; text-decoration: none; color: inherit;'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($row['imagem']) . "' alt='" . $row['nome'] . "' class='product-image'>";
            echo "</button>";
            echo "</form>";
            echo "</div>";
            echo "<div class='product-details'>";
            echo "<p class='product-name'>{$nome}</p>";
            echo "<p class='product-description'>{$descricao}</p>";
            echo "<p class='product-price'>R$ {$row['preco']}</p>";
            echo "<div class='product-quantity-wrapper'>";
            echo "<button type='button' onclick='alterarQuantidade({$row['id_produto']}, \"remove\")' class='button'>-</button>";
            echo "<span class='product-quantity'>{$row['quantidade']}</span>";
            echo "<button type='button' onclick='alterarQuantidade({$row['id_produto']}, \"add\")' class='button'>+</button>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        echo "<div class='total-wrapper'>";
        echo "<p>Total: R$ $total</p>";
        echo "<form method='post'>";
        echo "<input type='submit' name='comprar' value='Comprar' class='button'>";
        echo "</form>";
        echo "</div>";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comprar'])) {
            $stmt_delete = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ?");
            $stmt_delete->bind_param("i", $id_usuario);
            if ($stmt_delete->execute()) {
                echo "<script>
                setTimeout(function() {
                    window.location.href = '../../index.php';
                }, 500);
            </script>";
                echo "<div id='compra-realizada' style='background-color: #dff0d8; color: #3c763d; padding: 10px; margin-top: 10px;'>Compra realizada!</div>";
            } else {
                echo "Erro ao realizar a compra: " . $conn->error;
            }
        }
    } else {
        echo "<p>Carrinho vazio</p>";
    }
    ?>
</body>

</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {



    if (empty($cep) || empty($cidade) || empty($estado) || empty($rua) || empty($bairro) || empty($numero) || empty($usuario_id)) {
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
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/stylecart.css">
</head>

<body>
    <div>
        <button id="cadastrarNovoEndereco" class="button">Cadastrar Novo Endereço</button>
        <button id="usarEnderecoExistente" class="button">Usar Endereço Existente</button>
        <form id="enderecoForm" method="post" style="display: none;">
            <h2>Endereço</h2>
            <input type="text" id="cep" name="cep" placeholder="CEP" maxlength="9" required><br>
            <input type="text" id="cidade" name="cidade" placeholder="Cidade" required><br>
            <input type="text" id="estado" name="estado" placeholder="Estado" required><br>
            <input type="text" id="rua" name="rua" placeholder="Rua" required><br>
            <input type="text" id="bairro" name="bairro" placeholder="Bairro" required><br>
            <input type="number" text-decoration="none" placeholder="Número" id="numero" name="numero" required><br> <input type="submit" value="Salvar Endereço" class="button">
            <div id="mensagem"></div> 
        </form>
    </div>
    <div id="enderecoSelecionado"></div>

    <table id="listaEnderecos" style="display: none;">
        <tr>
            <th>CEP</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Rua</th>
            <th>Bairro</th>
            <th>Número</th>
            <th>Selecionar</th>
        </tr>
    </table>
    </div>
    <div class="container">
        <a href="../../index.php">Voltar para a página inicial</a>
    </div>
    <script>
        const cepInput = document.getElementById('cep');
        cepInput.addEventListener('input', function(event) {
            let cep = event.target.value;
            cep = cep.replace(/\D/g, ''); 
            if (cep.length > 5) {
                cep = cep.replace(/^(\d{5})(\d{1,3})/, '$1-$2'); 
            }
            event.target.value = cep;
        });
        function atualizarListaEnderecos() {
            $.ajax({
                url: 'listar_enderecos.php', 
                success: function(data) {
                    $('#listaEnderecos').html(data);
                    $('.selecionarEndereco').show();
                }
            });
        }
        $(document).ready(function() {
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
                atualizarListaEnderecos();
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
                        var cep = $('#cep').val();
                        var cidade = $('#cidade').val();
                        var estado = $('#estado').val();
                        var rua = $('#rua').val();
                        var bairro = $('#bairro').val();
                        var numero = $('#numero').val();
                        var enderecoSelecionado = "Endereço selecionado: " + rua + ", " + numero + " - " + bairro + " - " + cidade + "/" + estado + " - CEP: " + cep;
                        $('#mensagem').text(enderecoSelecionado);
                        atualizarListaEnderecos();
                        $('#cep, #cidade, #estado, #rua, #bairro, #numero').val('');
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
                                $('#numero');
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