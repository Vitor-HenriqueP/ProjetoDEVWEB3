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

    document.querySelector(".form-container.sign-in form").addEventListener("submit", function (e) {
        e.preventDefault();
        var login = document.querySelector("#login").value;
        var senha = document.querySelector("#senha").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText.trim() === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    document.querySelector("#mensagem").innerHTML = "<p style='color:red;'>Login ou senha incorretos</p>";
                }
            }
        };
        xhr.send("login=" + login + "&senha=" + senha);
    });
});

