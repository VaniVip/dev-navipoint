
== Инструкция по установке Smart Sheet CRM на Бегет ==

1. Заливаешь архив в папку /public_html/smart-sheet-crm/

2. Скачиваешь с Google Cloud JSON-файл сервисного аккаунта:
   google-credentials.json

3. Кладёшь его в ту же папку (рядом с index.php и api.php)

4. Указываешь ID своей Google Таблицы в файле api.php:
   Вместо 'PASTE_YOUR_SPREADSHEET_ID_HERE' вставляешь ID таблицы

5. На сервере запускаешь установку composer-пакетов:
   composer require google/apiclient:^2.0

   (если composer недоступен, напиши, дам готовую vendor-папку)

6. Открываешь в браузере:
   https://yourdomain.ru/smart-sheet-crm/

7. Готово! Можно загружать и редактировать данные из Google Sheets
