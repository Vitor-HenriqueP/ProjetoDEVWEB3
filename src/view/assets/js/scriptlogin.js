const container = document.getElementById('container');
const signInBtn = document.getElementById('signInBtn');
const signUpBtn = document.getElementById('signUpBtn');

signInBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

signUpBtn.addEventListener('click', () => {
    container.classList.add("active");
});
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.indexOf('cadastro=success') !== -1) {
        var popup = document.getElementById('popup');
        popup.style.display = 'block';
        
        setTimeout(function() {
            popup.style.display = 'none';
        }, 10000);
    }
});

