

function submitForm() {
    var form = document.getElementById("formProduto");
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            showSuccessMessage();
        } +3

    };
    xhr.send(formData);

    return false;
}

document.getElementById("formProduto").addEventListener("submit", function (event) {
    event.preventDefault();
    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "cadastrar_produto.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("mensagem").innerHTML = "<p>Produto cadastrado com sucesso!</p>";
            document.getElementById("formProduto").reset();
            document.getElementById("imagem").value = "";
            document.getElementById("imagem-preview").style.display = "none";
        }
    };
    xhr.send(formData);
});

function previewImage(input) {
    var preview = document.getElementById('imagem-preview');
    var file = input.files[0];
    var reader = new FileReader();

    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
function showPopup(message) {
    var popup = document.getElementById("popup");
    var popupMessage = document.getElementById("popup-message");
    popupMessage.textContent = message;
    popup.style.display = "block";
}
function validarFormulario() {
    var login = document.getElementById('login').value;
    if (login.indexOf('@') === -1 || login.indexOf('.') === -1) {
        alert('O login deve conter o caractere "@" e pelo menos um ponto ".".');
        return false;
    }
    return true;
}