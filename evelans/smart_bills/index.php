<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список услуг компании1</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .btn-settings {
            padding: 8px 16px;
            background-color: #bbed21;
            color: #525c69;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        
        .btn-settings:hover {
            background-color: #d2f95f;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <h2>Список услуг компании1</h2>
        <button class="btn-settings" onclick="window.location.href='settings.php'">Настройки</button>
    </div>
    
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