<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список услуг компании</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Список услуг компании</h2>
    
    <div class="filter-panel">
        <label class="checkbox-label">
            <input type="checkbox" id="activeOnly" checked onchange="app.refreshData()">
            <span>Вывести только активные работы</span>
        </label>
    </div>
    
    <div id="result" class="loading">Загрузка данных...</div>

    <script src="https://api.bitrix24.com/api/v1/"></script>
    <script src="app.js"></script>
</body>
</html>
