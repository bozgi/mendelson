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
  <img src="#" usemap="#graph-map" id="graph" alt="Graph">

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
      <div>
        <button type="button" id="cancel">Cancel</button>
        <button type="submit" id="submit">Submit</button>
      </div>
    </form>
  </dialog>

  <script>


  </script>
</body>
</html>