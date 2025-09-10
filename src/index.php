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
  <map name="graph-map">
  </map>
  <img src="" usemap="#graph-map" id="graph">

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
    const graphImg = document.getElementById('graph');
    // data:image/png;base64

    const searchParams = new URLSearchParams(window.location.search);

    fetch('api/getGraph.php?' + searchParams.toString())
      .then(response => response.json())
      .then(data => {
        graphImg.src = `data:image/png;base64,${data.image}`;
        const map = document.querySelector('map[name="graph-map"]');
        data.points.forEach(point => {
          const area = document.createElement('area');
          area.shape = 'circle';
          area.coords = `${point.x},${point.y},5`;
          area.dataset.day = point.day_of_month;
          area.dataset.temperature = point.temperature_c;
          area.dataset.status = point.status;
          map.appendChild(area);
        });
      })
      .catch(error => {
        console.error('Error fetching graph or point data:', error);
      })
      .finally(() => {
        attachAreaListeners();
      });

    const dialog = document.getElementById('editDialog');
    const form = document.getElementById('editForm');
    const cancel = document.getElementById('cancel');
    const submit = document.getElementById('submit');

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
        if (!data.success) {
          console.error('Error updating measurement: ' + data.message);
          return;
        }
        reloadGraph(); // iso 88559-2
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error updating measurement');
      });

      dialog.close();
    });

    function attachAreaListeners() {
      const areas = document.querySelectorAll('area');
      areas.forEach(area => {
        area.addEventListener('click', event => {
          const day = area.dataset.day;
          const temperature = area.dataset.temperature;
          const status = area.dataset.status;

          document.getElementById('day').value = day;
          document.getElementById('dayDisplay').textContent = day;
          document.getElementById('temperature').value = temperature !== 'n/a' ? temperature : '';
          document.getElementById('status').value = status;

          dialog.showModal();
        });
      });
    }

    function reloadGraph() {
      fetch('api/getGraph.php?' + searchParams.toString() + "&" + new Date().getTime())
        .then(response => response.json())
        .then(data => {
          graphImg.src = `data:image/png;base64,${data.image}`;
          const map = document.querySelector('map[name="graph-map"]');
          map.innerHTML = '';
          data.points.forEach(point => {
            const area = document.createElement('area');
            area.shape = 'circle';
            area.coords = `${point.x},${point.y},5`;
            area.dataset.day = point.day_of_month;
            area.dataset.temperature = point.temperature_c;
            area.dataset.status = point.status;
            map.appendChild(area);
          });
        })
        .catch(error => {
          console.error('Error fetching graph or point data:', error);
        })
        .finally(() => {
          attachAreaListeners();
        });
    }

  </script>
</body>
</html>
