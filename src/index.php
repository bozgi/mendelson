<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    area {
      cursor: pointer;
    }
    dialog {
      border: none;
      padding: 1rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    dialog::backdrop {
      background: rgba(0,0,0,0.4);
    }
  </style>
</head>
<body>
  <?php
  require 'chart.php';
  if (!isset($_GET["width"]) || !isset($_GET["height"])) {
      $_GET["width"] = 500;
      $_GET["height"] = 500;
  }
  $chart = new Chart($_GET["width"], $_GET["height"]);
  $chart->setXTitle("Dzień miesiąca");
  $chart->setYTitle("Temperatura [°C]");
  $chart->drawGraph();
  $im = $chart->output();
  ?>
  <map name="graph">
    <?php
      $chart->getPointData();
      foreach ($chart->getPointData() as $point) {
          echo '<area shape="circle" coords="'.$point['x'].','.$point['y'].',5"'
              .' alt="'.$point['status'].'"'
              .' title="'.$point['status'].'"'
              .' data-day="'.$point['day_of_month'].'"'
              .' data-temperature="'.$point['temperature_c'].'"'
              .' data-status="'.$point['status'].'">';
      }
    ?>
  </map>
  <img src="data:image/x-icon;base64,<?php echo base64_encode($im); ?>" usemap="#graph">

  <dialog id="editDialog">
    <form method="dialog" id="editForm">
      <h3>Edit Measurement</h3>
      <input type="hidden" name="day" id="day">
      <p>Day: <span id="dayDisplay"></span></p>
      <p>
        <label>
          Temperature:
          <input type="number" step="0.01" name="temperature" id="temperature">
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
      <menu>
        <button type="button" id="cancel">Cancel</button>
        <button type="submit" id="submit">Submit</button>
      </menu>
    </form>
  </dialog>

  <script>
    const dialog = document.getElementById('editDialog');
    const form = document.getElementById('editForm');
    const cancel = document.getElementById('cancel');
    const submit = document.getElementById('submit');

    document.querySelectorAll('area').forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        document.getElementById('day').value = item.dataset.day;
        document.getElementById('dayDisplay').textContent = item.dataset.day;
        document.getElementById('temperature').value = item.dataset.temperature || '';
        document.getElementById('status').value = item.dataset.status;
        dialog.showModal();
      });
    });

    cancel.addEventListener('click', () => dialog.close());

    form.addEventListener('submit', event => {
      event.preventDefault();
      const day = document.getElementById('day').value;
      const temperature = document.getElementById('temperature').value;
      const status = document.getElementById('status').value;

      fetch('api/updateGraph.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ day, temperature, status })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error updating measurement: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error updating measurement');
      });

      dialog.close();
    });

  </script>
</body>
</html>
