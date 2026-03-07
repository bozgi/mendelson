<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="static/script.js" defer></script>
    <script src="https://kit.fontawesome.com/7cfa22db6e.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <dialog id="editDialog">
            <form method="dialog" id="editForm">
                <h3>Info</h3>
                <p class="dialog-info"></p>
                <div>
                    <button type="submit" id="submit">Ok</button>
                </div>
            </form>
        </dialog>
        <header>
            <div class="header-background">
                <div class="login-bar">
                    <div><i class="fa-solid fa-arrow-right-to-bracket"></i>Zaloguj się</div>
                </div>
                <div class="logo-bar"><strong>Wiggatronics</strong></div>
                <nav>
                    <div class="option selected login-button">Zaloguj</div>
                    <div class="option register-button">Utwórz konto</div>
                </nav>
            </div>
        </header>
        <main>
            <div class="login-box active">
                <img src="static/gapejak.png" alt="avatar">
                <form method="post">
                    <input type="text" name="email" placeholder="E-mail" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <div class="login-box-options">
                        <button type="submit">Zaloguj</button>
                        <a href="">Zapomniałeś hasła?</a>
                    </div>
                </form>
            </div>     
            <div class="register-box">
                <img src="static/mentaljak.png" alt="avatar">
                <form class="register-form" method="post">
                    <input type="text" name="email" placeholder="E-mail" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <div class="login-box-options">
                        <button type="submit">Zarejestruj</button>
                    </div>
                </form>
            </div>
        </main>
        <footer>
            <p>&copy; 2026 Oskar Biedroń</p>
        </footer>
    </div>
</body>
</html>