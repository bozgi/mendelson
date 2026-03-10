<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php" . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel wykresów</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="https://kit.fontawesome.com/7cfa22db6e.js" crossorigin="anonymous"></script>
    <script>
        let OPEN_GRAPH_ID = null;
        let OPEN_GRAPH_BUTTON = null;
    </script>
    <?php 
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        echo "<script>
                OPEN_GRAPH_ID = $id;
            </script>";
    }
    ?>
    <script src="static/dashboard_script.js" defer></script>
</head>
<body>
    <div class="container">
        <dialog id="editDialog">
            <form method="dialog" id="editForm">
                <h3>Info</h3>
                <div class="dialog-options">
                    <label>
                        Początek zakresu:
                        <input type="date" name="startDate" class="start-date">
                    </label>
                    <label>
                        Koniec zakresu:
                        <input type="date" name="endDate" class="end-date">
                    </label>
                    <p class="dialog-info"></p>
                </div>
                <button type="button" id="ok">Ok</button>
                <button type="button" id="cancel">Anuluj</button>
            </form>

        </dialog>
        <dialog class="graphDialog">
            <map name="graph-map">
            </map>
            <img src="#" usemap="#graph-map" id="graph" alt="Graph">

            <dialog id="editGraphDialog">
                <form method="dialog" id="editDialogForm">
                <h3>Edit Measurement</h3>
                <input type="hidden" name="graphId" id="graphId">
                <input type="hidden" name="date" id="date">
                <p>Date: <span id="dateDisplay"></span></p>
                <p>
                    <label>
                    Temperature:
                    <input type="text" name="temperature" id="temperature" pattern="^\d*\.?\d*$">
                    </label>
                </p>
                <p>
                    <label>
                    Status:
                    <select name="status" id="status">
                        <option value="healthy">healthy</option>
                        <option value="sick">sick</option>
                        <option value="n/a">n/a</option>
                    </select>
                    </label>
                </p>
                <div>
                    <button type="button" id="cancelGraph">Cancel</button>
                    <button type="submit" id="submitGraph">Submit</button>
                </div>
                </form>
            </dialog>
        </dialog>
        <header>
            <div class="header-background">
                <div class="login-bar">
                    <div><a class="logout" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><?php echo $_SESSION['email']; ?></a></div>
                </div>
                <div class="logo-bar"><strong>Wykresiki</strong></div>
                <nav>
                </nav>
            </div>
        </header>
        <main>
            <button class="create-graph-button">Utwórz wykres</button>
            <div class="graphs-container">
                
            </div>
        </main>
        <footer>
            <p>&copy; 2026 Oskar Biedroń</p>
        </footer>
    </div>
</body>
</html>