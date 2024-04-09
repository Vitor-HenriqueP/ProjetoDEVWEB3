const container = document.getElementById('container');
const signInBtn = document.getElementById('signInBtn');
const signUpBtn = document.getElementById('signUpBtn');

signInBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

signUpBtn.addEventListener('click', () => {
    container.classList.add("active");
});
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#cadastroForm").addEventListener("submit", function (e) {
        e.preventDefault();
        var nome = document.querySelector("#nome").value;
        var login = document.querySelector("#login").value;
        var senha = document.querySelector("#senha").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
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

document.getElementById('formLogin').addEventListener('submit', function (event) {
    event.preventDefault();

    var form = this;
    var formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('message').innerHTML = "<div  class='success'>" + data.message + "</div>";
                setTimeout(function () {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                document.getElementById('message').innerHTML = "<div class='error'>" + data.message + "</div>";
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
});


function validarEmail() {
    var email = document.getElementById("login").value;
    if (email.includes("@estacaodigital")) {
        document.getElementById("login").setCustomValidity("");
    } else {
        document.getElementById("login").setCustomValidity("O e-mail deve conter '@estacaodigital'");
    }
}
