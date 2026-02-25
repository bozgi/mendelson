const loginButton = document.querySelector('.login-button');
const registerButton = document.querySelector('.register-button');
const loginBox = document.querySelector('.login-box');
const registerBox = document.querySelector('.register-box');

const registerForm = document.querySelector('.register-form');
const registerFormEmail = document.querySelector('.register-form input[type=text]');
const registerFormPassword = document.querySelector('.register-form input[type=password]');
const registerFormButton = document.querySelector(".register-form button")

loginButton.addEventListener('click', () => {
    loginButton.classList.add('selected');
    registerButton.classList.remove('selected');
    loginBox.classList.add('active');
    registerBox.classList.remove('active');
});

registerButton.addEventListener('click', () => {
    registerButton.classList.add('selected');
    loginButton.classList.remove('selected');
    registerBox.classList.add('active');
    loginBox.classList.remove('active');
});

registerFormButton.addEventListener("click", (e) => {
    e.preventDefault()
    console.log(registerFormEmail.value, registerFormPassword.value);
    
    fetch("api/register.php", 
        {
            method: "post",
            body: JSON.stringify({
                email: registerFormEmail.value,
                password: registerFormPassword.value
            })
        }
    ).then(res => res.json())
    .then(json => {
        if (!json["success"]) {
            const dialog = document.querySelector("dialog");
            const dialogInfo = document.querySelector(".dialog-info");
            dialogInfo.innerText = `Nie udało się utowrzyć konta, hasło nie spełnia niektórych warunków: ${json["message"].join(", ")}`
            dialog.showModal()
        }
    })
})