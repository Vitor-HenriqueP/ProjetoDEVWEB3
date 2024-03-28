

function submitForm() {
    var form = document.getElementById("formProduto");
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            showSuccessMessage();
        }+3
        
    };
    xhr.send(formData);

    return false;
}
function previewImage(input) {
    var preview = document.getElementById('imagem-preview');
    var file = input.files[0];
    var reader = new FileReader();

    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block'; // Exibe a imagem
    }

    if (file) {
        reader.readAsDataURL(file); // Lê o arquivo como URL de dados
    } else {
        preview.src = '';
        preview.style.display = 'none'; // Oculta a imagem se não houver arquivo selecionado
    }
}
function showPopup(message) {
    var popup = document.getElementById("popup");
    var popupMessage = document.getElementById("popup-message");
    popupMessage.textContent = message;
    popup.style.display = "block";
}
