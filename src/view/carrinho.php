<?php
session_start(); // Inicie a sessão no início do arquivo

include '../../conexao.php'; // Inclua o arquivo de conexão com o banco de dados

// Verifique se o usuário está logado
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

// Verifique se o ID do produto foi enviado por POST
if (isset($_POST['id_produto']) && isset($_POST['action'])) {
    $id_produto = intval($_POST['id_produto']);
    $id_usuario = intval($_SESSION['id']); // Obter o ID do usuário da sessão

    if ($_POST['action'] === 'remove') {
        // Remove um item do carrinho
        $stmt = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ? AND id_produto = ? LIMIT 1");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        // Redireciona o usuário para a mesma página usando GET
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } elseif ($_POST['action'] === 'add') {
        // Adiciona um item ao carrinho
        $stmt = $conn->prepare("INSERT INTO carrinho (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_produto);
        $stmt->execute();

        // Definir uma mensagem de sucesso para exibir
        $mensagem = "Produto adicionado ao carrinho.";
        // Exibir a mensagem de produto adicionado ao carrinho por 3 segundos
        echo "<script>setTimeout(function() { document.getElementById('mensagem').style.display = 'none'; }, 3000);</script>";

        // Redireciona o usuário para a mesma página usando GET
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    }
}

// Atualizar a quantidade de itens no carrinho na sessão
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
    <title>Carrinho</title>
    <link rel="stylesheet" type="text/css" href="/config/stylecarrinho.css">
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
                    location.reload(); // Recarrega a página após a atualização
                }
            });
        }

        $(document).ready(function() {
                    $('#cep').on('input', function() {
                        var cep = $(this).val();
                        if (cep.length === 5) {
                            $(this).val(cep + '-');
                        }
                        if (cep.length > 9) {
                            $(this).val(cep.substring(0, 9));
                        }
                    });

                    $('#calcular-frete').click(function() {
                        var cep = $('#cep').val().replace('-', '');

                        $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                            if (!("erro" in data)) {
                                if (data.localidade) {
                                    $('#cidade').val(data.localidade);
                                } else {
                                    $('#cidade').removeAttr('readonly').val('');
                                }

                                if (data.logradouro) {
                                    $('#rua').val(data.logradouro);
                                } else {
                                    $('#rua').removeAttr('readonly').val('');
                                }

                                if (data.uf) {
                                    $('#estado').val(data.uf);
                                } else {
                                    $('#estado').removeAttr('readonly').val('');
                                }

                                if (data.bairro) {
                                    $('#bairro').val(data.bairro);
                                } else {
                                    $('#bairro').removeAttr('readonly').val('');
                                }

                                // Exibe o valor do frete
                                var total = <?php echo $total; ?>;
                                var frete = 10.00; // Valor fixo do frete
                                var totalComFrete = total + frete;
                                $('#frete').val('R$ ' + frete.toFixed(2).replace('.', ','));
                                $('#total-com-frete').val('R$ ' + totalComFrete.toFixed(2).replace('.', ','));

                                // Exibe o campo do frete
                                $('#frete').show();
                            } else {
                                alert("CEP não encontrado.");
                            }
                        });
                    });
    </script>
</head>

<body>
    <h1>Carrinho</h1>

    <?php
    // Consulta SQL para obter os produtos no carrinho do usuário logado
    $id_usuario = intval($_SESSION['id']);
    $stmt = $conn->prepare("SELECT c.id_produto, COUNT(c.id_produto) as quantidade, p.nome, p.descricao, p.preco, p.imagem FROM carrinho c INNER JOIN produto p ON c.id_produto = p.id WHERE c.id_usuario = ? GROUP BY c.id_produto");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $total = 0; // Variável para armazenar o total dos produtos no carrinho

        while ($row = $result->fetch_assoc()) {
            $total += $row['preco'] * $row['quantidade']; // Adiciona o preço do produto ao total
            $descricao = explode(' ', $row["descricao"]);
            $descricao = array_slice($descricao, 0, 20);
            $descricao = implode(' ', $descricao);

            echo "<form method='post' action='../../src/view/produto.php'>";
            echo "<input type='hidden' name='id' value='" . $row["id_produto"] . "'>";
            echo "<button type='submit' style='background: none; border: none; padding: 0; margin: 0;' class='product-image-button'>";

            echo "<img src='data:image/jpeg;base64," . base64_encode($row['imagem']) . "' alt='" . $row['nome'] . "' class='product-image'>";
            echo "</button>";
            echo "</form>";

            echo "<p>{$descricao} - R$ {$row['preco']} - Quantidade: {$row['quantidade']}</p>";

            echo "<button type='button' onclick='alterarQuantidade({$row['id_produto']}, \"remove\")' class='button'>-</button>";

            echo "<button type='button' onclick='alterarQuantidade({$row['id_produto']}, \"add\")' class='button'>+</button>";
        }

        // Exibe o valor total
        echo "<p><strong>Total: R$ $total</strong></p>";

        // Botão Comprar
        echo "<form method='post'>";
        echo "<input type='submit' name='comprar' value='Comprar' class='button'>";
        echo "</form>";

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comprar'])) {
            // Limpa todos os itens do carrinho no banco de dados
            $stmt_delete = $conn->prepare("DELETE FROM carrinho WHERE id_usuario = ?");
            $stmt_delete->bind_param("i", $id_usuario);
            if ($stmt_delete->execute()) {
                // Recarrega a página após 2 segundos
                echo "<script>setTimeout(function(){ location.reload(); }, 500);</script>";
                echo "<div id='compra-realizada' style='background-color: #dff0d8; color: #3c763d; padding: 10px; margin-top: 10px;'>Compra realizada!</div>";
            } else {
                echo "Erro ao realizar a compra: " . $conn->error;
            }
        }
    } else {
        echo "<p>Carrinho vazio</p>";
    }
    ?>

    <?php if ($result->num_rows > 0) : ?>
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" maxlength="9">
        <button id="calcular-frete">Calcular Frete</button><br><br>

        <!-- Campos de endereço -->
        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" readonly><br>
        <label for="rua">Rua:</label>
        <input type="text" id="rua" name="rua" readonly><br>
        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" readonly><br>
        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" readonly><br>
        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero"><br>

        <!-- Div para exibir o total com frete -->
        <label for="frete">Frete:</label>
<input type="text" id="frete" name="frete" readonly><br>

<label for="frete-total">Frete Total:</label>
<input type="text" id="frete-total" name="frete-total" readonly><br>


    <?php endif; ?>

    <a href="../../index.php">Voltar para a página inicial</a>

    <div class="card-mensagem" id="mensagem"><?php echo isset($mensagem) ? $mensagem : ''; ?></div>
    <label for="frete">Frete:</label>
    <input type="text" id="frete" name="frete" readonly><br>

</body>

</html>

<?php
$frete = 10.00; // Valor fixo do frete
$total += $frete; // Adiciona o frete ao total

// Exibe o valor total incluindo o frete
echo "<p><strong>Total: R$ " . number_format($total, 2, ',', '.') . "</strong></p>";
$stmt->close();
$conn->close();
?>