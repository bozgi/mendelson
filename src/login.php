<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit;
}

$login_error = NULL;

if (isset($_POST['email']) && isset($_POST['password'])) {
    require 'db.php';
    $email = $_POST['email'];
    $password = $_POST['password'];
    if ($stmt = $conn->prepare("SELECT id, password, active FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $login_error = "Nieprawidłowy email lub hasło";
        } else {
            $user = $result->fetch_assoc();
            if (!password_verify($password, $user['password'])) {
                $login_error = "Nieprawidłowy email lub hasło";
            } else if ($user['active'] == 0) {
                $login_error = "Konto nieaktywne. Sprawdź swoją skrzynkę pocztową.";
            } else {
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $email;
                header("Location: dashboard.php");
                exit;
            }
        }
    } else {
        $login_error = "Błąd bazy danych: " . $conn->error;
    }
}

$body = "";
$operation_type = "";
$operation_hash = "";

function handleOperation() {
    require 'db.php';
    global $body;
    global $operation_type;
    global $operation_hash;
    $stmt = $conn->prepare("SELECT operation_type FROM operations WHERE hash = ?");
    $stmt->bind_param("s", $operation_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $body = "Invalid operation";
        return;
    }
    $operation = $result->fetch_assoc();
    $operation_type = $operation["operation_type"];

    if ($operation_type == "REGISTER") {
        if ($stmt = $conn->prepare("SELECT * FROM operations WHERE hash = ? AND operation_type = 'REGISTER'")) {
            $stmt->bind_param("s", $operation_hash);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $body = "Invalid operation";
                return;
            }
            $operation = $result->fetch_assoc();
            $user_id = $operation["account_id"];
            if ($stmt = $conn->prepare("UPDATE users SET active = 1 WHERE id = ?")) {
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $body = "Konto aktywowane pomyślnie! Zostaniesz przekierowany do strony logowania.";
                    $stmt = $conn->prepare("DELETE FROM operations WHERE hash = ?");
                    $stmt->bind_param("s", $operation_hash);
                    $stmt->execute();
                    return;
                } else {
                    $body = "Error activating account: " . $stmt->error;
                    return;
                }
            } else {
                $body = "Error preparing statement: " . $conn->error;
                return;
            }
        } else {
            echo "Error preparing statement: " . $conn->error;
            return;
        }
    } else if ($operation_type == "RESTORE_PASSWORD") {
        if ($stmt = $conn->prepare("SELECT * FROM operations WHERE hash = ? AND operation_type = 'RESTORE_PASSWORD'")) {
            $stmt->bind_param("s", $operation_hash);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $body = "Invalid operation";
                return;
            }
            $operation = $result->fetch_assoc();
            $user_id = $operation["account_id"];
            $body = "Podaj nowe hasło";
            return;
        } else {
            echo "Error preparing statement: " . $conn->error;
            return;
        }
    }
}

if (isset($_GET["operation"])) {
    $operation_hash = $_GET["operation"];
    handleOperation();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="static/script.js" defer></script>
    <script src="https://kit.fontawesome.com/7cfa22db6e.js" crossorigin="anonymous"></script>
    <?php 
        if ($body != "") {
            echo "<script>
                window.onload = function() {
                    const dialog = document.getElementById('editDialog');
                    dialog.showModal();
                }
            </script>";
        }
        if ($login_error) {
            echo "<script>
                window.onload = function() {
                    const dialog = document.getElementById('editDialog');
                    const info = document.querySelector('.dialog-info');
                    info.textContent = '$login_error';
                    dialog.showModal();
                }
            </script>";
        }
    ?>
</head>
<body>
    <div class="container">
        <dialog id="editDialog">
            <form method="dialog" id="editForm">
                <h3>Info</h3>
                <p class="dialog-info"><?php echo $body; ?></p>
                <input type="email" name="email" placeholder="E-mail" class="restore-email">
                <input type="password" name="password" placeholder="Nowe hasło" class="restore-password <?php if ($operation_type == "RESTORE_PASSWORD") { echo "active"; } ?>">
                <input type="password" name="confirm_password" placeholder="Potwierdź hasło" class="restore-confirm-password <?php if ($operation_type == "RESTORE_PASSWORD") { echo "active"; } ?>">
                <input type="hidden" name="operation_type" value="<?php echo $operation_type; ?>">
                <input type="hidden" name="operation_hash" value="<?php echo $operation_hash; ?>">
                <div>
                    <button id="submit">Ok</button>
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
                        <a href="" class="restore-password-link">Zapomniałeś hasła?</a>
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