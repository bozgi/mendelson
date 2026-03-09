const loginButton = document.querySelector('.login-button');
const registerButton = document.querySelector('.register-button');
const loginBox = document.querySelector('.login-box');
const registerBox = document.querySelector('.register-box');

const registerForm = document.querySelector('.register-form');
const registerFormEmail = document.querySelector('.register-form input[type=text]');
const registerFormPassword = document.querySelector('.register-form input[type=password]');
const registerFormButton = document.querySelector(".register-form button")

const dialog = document.querySelector("dialog");
const dialogInfo = document.querySelector(".dialog-info");

const restorePasswordLink = document.querySelector('.restore-password-link');

dialog.addEventListener('close', () => {
    const emailInput = document.querySelector('.restore-email');
    const passwordInput = document.querySelector('.restore-password');
    const confirmPasswordInput = document.querySelector('.restore-confirm-password');
    emailInput.value = "";
    passwordInput.value = "";
    confirmPasswordInput.value = "";
    emailInput.classList.remove('active');
    passwordInput.classList.remove('active');
    confirmPasswordInput.classList.remove('active');
})

submit.addEventListener('click', (e) => {
    const emailInput = document.querySelector('.restore-email');
    const passwordInput = document.querySelector('.restore-password');
    const confirmPasswordInput = document.querySelector('.restore-confirm-password');
    const operationType = document.querySelector('input[name="operation_type"]');
    const operationHash = document.querySelector('input[name="operation_hash"]');
    if (emailInput.value != "" && emailInput.classList.contains('active')) {
        e.preventDefault();
        fetch("api/reset_password.php",
            {
                method: "post",
                body: JSON.stringify({ email: emailInput.value })
            }
        ).then(res => res.json())
        .then(json => {
            if (json["success"] == true) {
                dialogInfo.innerText = json["message"];
            } else {
                dialogInfo.innerText = `Nie udało się zresetować hasła: ${json["message"]}`;
            }
            
        })
    }
    if (passwordInput.value != "" && confirmPasswordInput.value != "" && operationType.value == "RESTORE_PASSWORD") {
        e.preventDefault();
        if (passwordInput.value != confirmPasswordInput.value) {
            dialogInfo.innerText = "Hasła nie są takie same!";
            return;
        }
        fetch("api/confirm_reset_password.php",
            {
                method: "post",
                body: JSON.stringify({
                    password: passwordInput.value,
                    operation: operationHash.value
                })
            }
        ).then(res => res.json())
        .then(json => {
            if (json["success"] == true) {
                dialogInfo.innerText = json["message"];
            } else {
                dialogInfo.innerText = `Nie udało się zresetować hasła: ${json["message"]}`;
            }
            operationType.value = "";
        })
    }
    dialogInfo.innerText = "";
    emailInput.classList.remove('active');
    passwordInput.classList.remove('active');
    confirmPasswordInput.classList.remove('active');
})

restorePasswordLink.addEventListener('click', (e) => {
    e.preventDefault();
    const emailInput = document.querySelector('.restore-email');
    emailInput.classList.add('active');
    dialog.showModal();
})

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
        if (json["success"] == false) {
            dialogInfo.innerText = `Nie udało się utowrzyć konta, hasło nie spełnia niektórych warunków: ${Array.isArray(json["message"]) ? json["message"].join(", ") : json["message"]}`;
            dialog.showModal()
        } else if (json["success"] == true) {
            dialogInfo.innerText = json["message"]
            dialog.showModal()
        }
    })
})