<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="./src/view/assets/css/stylelogin.css">


</head>

<body>
<div id="mensagem"></div>

    <div class="container" id="container">
        <div class="form-container sign-up">
        <form id="cadastroForm" method="post">
                <h1>Cadastro de usuário</h1>
                <input type="text" id="nome" name="nome" required placeholder="Nome">
                <input type="text" id="login" name="login" required placeholder="E-mail">
                <input type="password" id="senha" name="senha" required placeholder="Senha">

                <input type="submit" value="Cadastrar" class="button">

            </form>

        </div>


        <div class="form-container sign-in">
            <form method="post" action="login.php">
                <h1>Entrar</h1>
                <input type="text" id="login" name="login" required placeholder="E-mail"><br><br>
                <input type="password" id="senha" name="senha" required placeholder="Senha"><br><br>
                <a href="#">Esqueceu sua senha ?</a>
                <input type="submit" value="Entrar" class="button">
                <a href="index.php">Voltar para a página inicial</a>
                <br>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bem-vindo novamente!!</h1>
                    <p>Entre em sua conta para utilizar todas as funções de nossa loja!</p>
                    <button class="hidden" id="signInBtn">Entrar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Olá, amigo!</h1>
                    <p>Registre sua própria conta e desfrute de todas as funções de nossa loja!</p>
                    <button class="hidden" id="signUpBtn">Cadastre-se</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("#cadastroForm").addEventListener("submit", function(e) {
                e.preventDefault();
                var nome = document.querySelector("#nome").value;
                var login = document.querySelector("#login").value;
                var senha = document.querySelector("#senha").value;

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "cadastrar.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status == "success") {
                            document.querySelector("#mensagem").innerHTML = "<p style='color:green;'>" + response.mensagem + "</p>";
                        } else {
                            document.querySelector("#mensagem").innerHTML = "<p style='color:red;'>" + response.mensagem + "</p>";
                        }
                    }
                };
                xhr.send("nome=" + nome + "&login=" + login + "&senha=" + senha);
            });
        });
    </script>
    <script src="./src/view/assets/js/scriptlogin.js"></script>

</body>

</html>