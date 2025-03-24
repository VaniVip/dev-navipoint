
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Google Таблица по сделке</title>
  <script src="//api.bitrix24.com/api/v1/"></script>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    input { padding: 5px; margin-right: 10px; width: 400px; }
    button { padding: 5px 10px; }
    #iframeContainer { margin-top: 20px; }
    .log { margin-top: 20px; color: red; white-space: pre-wrap; background: #f9f9f9; padding: 10px; border: 1px solid #ddd; }
  </style>
</head>
<body>
  <h2>Google Таблица по сделке</h2>

  <div>
    <label>Google Sheet ID:</label><br>
    <input type="text" id="sheetIdInput" placeholder="Вставь ID таблицы сюда" />
    <button onclick="saveSheetId()">Сохранить ID</button>
  </div>

  <div id="iframeContainer">
    <!-- Здесь появится таблица -->
  </div>

  <div class="log" id="log"></div>

  <script>
    let dealId = null;
    let sheetField = 'UF_CRM_1741888963566';

    function log(message) {
      console.log(message);
      const logDiv = document.getElementById('log');
      logDiv.textContent += message + '\n';
    }

    BX24.init(function() {
      log('BX24 инициализирован');

      try {
        const placementInfo = BX24.placement.info();
log('placementInfo: ' + JSON.stringify(placementInfo, null, 2));

if (!placementInfo || !placementInfo.options || !placementInfo.options.ID) {
  log('Ошибка: не найден ID сделки в placementInfo');
  return;
}

dealId = placementInfo.options.ID;
log('Deal ID: ' + dealId);


        BX24.callMethod('crm.deal.get', { ID: dealId }, function(result) {
          if (result.error()) {
            log('Ошибка получения сделки: ' + result.error());
          } else {
            const data = result.data();
            const sheetId = data[sheetField] || '';
            log('Текущий Google Sheet ID: ' + sheetId);

            document.getElementById('sheetIdInput').value = sheetId;

            if (sheetId) {
              showIframe(sheetId);
            } else {
              log('ID таблицы не найден');
            }
          }
        });
      } catch (error) {
        log('Ошибка инициализации: ' + error.message);
      }
    });

    function showIframe(sheetId) {
      const iframeUrl = `https://docs.google.com/spreadsheets/d/${sheetId}/pubhtml?widget=true&headers=false`;
      const iframeHtml = `<iframe src="${iframeUrl}" width="100%" height="600px" frameborder="0"></iframe>`;
      document.getElementById('iframeContainer').innerHTML = iframeHtml;
      log('Показан iframe с таблицей: ' + iframeUrl);
    }

    function saveSheetId() {
      if (!dealId) {
        log('Ошибка сохранения: dealId не определён!');
        alert('Ошибка! ID сделки не найден.');
        return;
      }

      const newSheetId = document.getElementById('sheetIdInput').value.trim();

      if (!newSheetId) {
        alert('Введите корректный ID таблицы!');
        log('Попытка сохранить пустой ID');
        return;
      }

      const fields = {};
      fields[sheetField] = newSheetId;

      BX24.callMethod('crm.deal.update', { ID: dealId, fields: fields }, function(result) {
        if (result.error()) {
          log('Ошибка сохранения ID: ' + result.error());
          alert('Ошибка сохранения ID!');
        } else {
          log('ID таблицы сохранён: ' + newSheetId);
          alert('ID таблицы сохранён!');
          showIframe(newSheetId);
        }
      });
    }
  </script>
</body>
</html>
