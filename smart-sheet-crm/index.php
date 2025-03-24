
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Google Таблица по сделке</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background-color: #f2f2f2; }
    input[type="text"] { width: 100%; box-sizing: border-box; }
    button { margin-top: 10px; padding: 10px 15px; }
  </style>
</head>
<body>
  <h2>Google Таблица по сделке</h2>

  <button onclick="loadData()">Загрузить данные</button>
  <table id="sheetTable"></table>
  <button onclick="saveData()">Сохранить данные</button>

  <script>
    async function loadData() {
      const res = await fetch('api.php?action=get');
      const data = await res.json();

      const table = document.getElementById('sheetTable');
      table.innerHTML = '';

      data.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');
        row.forEach((cell, cellIndex) => {
          const td = document.createElement('td');
          const input = document.createElement('input');
          input.type = 'text';
          input.value = cell;
          input.dataset.row = rowIndex;
          input.dataset.col = cellIndex;
          td.appendChild(input);
          tr.appendChild(td);
        });
        table.appendChild(tr);
      });
    }

    async function saveData() {
      const inputs = document.querySelectorAll('table input');
      const rows = [];

      inputs.forEach(input => {
        const row = parseInt(input.dataset.row);
        const col = parseInt(input.dataset.col);
        if (!rows[row]) rows[row] = [];
        rows[row][col] = input.value;
      });

      const res = await fetch('api.php?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(rows)
      });

      const result = await res.json();
      alert(result.message);
    }
  </script>
</body>
</html>
