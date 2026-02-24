<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="login-bar"></div>
            <div class="logo-bar"><strong>Wiggatronics</strong></div>
            <nav>
                <div>Zaloguj</div>
                <div>Utwórz konto</div>
            </nav>
        </header>
        <main>
            <div class="login-box">
                <img src="static/mentaljak.png" alt="avatar">
                <form action="api/login.php" method="post">
                    <input type="text" id="username" name="username" placeholder="E-mail" required>
                    <input type="password" id="password" name="password" placeholder="Hasło" required>
                    <div class="login-box-options">
                        <button type="submit">Zaloguj</button>
                        <a href="">Zapomniałeś hasła?</a>
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