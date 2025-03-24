<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки приложения</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="settings.css">
</head>
<body>
    <div class="settings-container">
        <h2>Настройки приложения</h2>
        
        <div class="tabs">
            <div class="tab active" data-tab="doc-types">Типы документов</div>
            <div class="tab" data-tab="tags">Теги</div>
        </div>
        
        <div class="tab-content active" id="doc-types-content">
            <h3>Редактирование типов документов</h3>
            <p>Здесь вы можете изменить названия типов документов и их замены в счетах.</p>
            
            <div class="settings-actions">
                <button id="add-doc-type" class="btn-action">Добавить тип документа</button>
                <button id="save-doc-types" class="btn-save">Сохранить изменения</button>
            </div>
            
            <div class="table-container">
                <table id="doc-types-table">
                    <thead>
                        <tr>
                            <th width="15%">ID</th>
                            <th width="30%">Оригинальное название</th>
                            <th width="45%">Замена в счете</th>
                            <th width="10%">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные будут загружены динамически -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="tab-content" id="tags-content">
            <h3>Редактирование тегов</h3>
            <p>Здесь вы можете изменить названия тегов.</p>
            
            <div class="settings-actions">
                <button id="add-tag" class="btn-action">Добавить тег</button>
                <button id="save-tags" class="btn-save">Сохранить изменения</button>
            </div>
            
            <div class="table-container">
                <table id="tags-table">
                    <thead>
                        <tr>
                            <th width="20%">ID</th>
                            <th width="70%">Название</th>
                            <th width="10%">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные будут загружены динамически -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="settings-footer">
            <button id="back-to-app" class="btn-back">Вернуться к приложению</button>
        </div>
    </div>
    
    <div id="notification" class="notification"></div>

    <script src="https://api.bitrix24.com/api/v1/"></script>
    <script src="settings.js"></script>
</body>
</html>