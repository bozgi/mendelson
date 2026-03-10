<?php
require_once 'TCPDF/tcpdf.php';

session_start();
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    http_response_code(401);
    exit;
}

class GraphPDF extends TCPDF {
    public function Header() {

        $this->SetFont('dejavusans', '', 9);

        date_default_timezone_set('Europe/Warsaw');
        $data = date('Y-m-d H:i:s');

        $this->Cell(0, 10, 'Raport wykresu wygenerowany: ' . $data, 0, false, 'R');
    }
}

require_once '../db.php';
$stmt = $conn->prepare("SELECT id FROM graphs WHERE user_id = ? AND id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $_SESSION['id'], $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "No graph found for this user"
    ]);
    exit;
}

require '../chart.php';
$chart = new Chart(1200, 500);
$chart->setXTitle("Data");
$chart->setYTitle("Temperatura [°C]");
$chart->drawGraph($_GET['id']);
$im = $chart->output();
$points = $chart->getPointData();

$user = $_SESSION['email'];

// ====== TCPDF ======

$pdf = new GraphPDF('P', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator($user);
$pdf->SetAuthor($user);
$pdf->SetTitle("Wykres użytkownika $user");
$pdf->SetSubject('Chart export');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);



$pdf->Image('@' . $im, 15, 15, 180, 80, 'PNG', '/dashboard.php?id=' . $_GET["id"]);
$pdf->Ln(90);


$pdf->Text(30,96,"LEGENDA");

$pdf->SetFillColor(255,0,0);
$pdf->Circle(25,105,2,0,360,'F');
$pdf->Text(30,104,'- choroba');

$pdf->SetFillColor(0,0,255);
$pdf->Circle(25,112,2,0,360,'F');
$pdf->Text(30,111,'- pomiar');

$pdf->SetFillColor(100,100,100);
$pdf->Circle(25,119,2,0,360,'F');
$pdf->Text(30,118,'- brak pomiaru');


$pdf->Ln(20);

$html = '
<style>
table {
    width:50%;
    font-size:9px;
}
th {
    background-color:#eeeeee;
    font-weight:bold;
}
td, th {
    padding:4px;
}
</style>



<table border="1" cellpadding="3">';


$html .= '<tr><th>dzień</th><th>temperatura</th><th>data</th></tr>';

foreach ($points as $index => $point) {
    $index = $index + 1;

    if ($point['status'] === 'healthy') {
        $value = $point['temperature_c'];
    } else {
        $value = $point['status'];
    }

    $html .= "<tr>
        <td>{$index}</td>
        <td>{$value}</td>
        <td>{$point['date']}</td>
    </tr>";
}

$html .= '</table>';

$pdf->writeHTML($html);
$pdf->Output("raport.pdf", "I");