
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#formCadastroAdm").addEventListener("submit", function (e) {
        e.preventDefault();
        var nome = document.querySelector("#nome").value;
        var login = document.querySelector("#login").value;
        var senha = document.querySelector("#senha").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "cadastro_adm.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
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