/**
 * JavaScript для страницы настроек приложения
 */

// Объект приложения настроек
const settingsApp = {
    // Исходные данные
    docTypesEnum: {},
    tagsEnum: {},
    
    // Замены для типов документов (новое поле)
    docTypeReplacements: {},
    
    /**
     * Инициализация приложения настроек
     */
    init: function() {
        console.log("Инициализация приложения настроек...");
        
        // Инициализация интерфейса
        this.initUI();
        
        // Загрузка данных
        BX24.ready(() => {
            BX24.init(() => {
                console.log("BX24 готов!");
                this.loadSettings();
            });
        });
    },
    
    /**
     * Инициализация интерфейса
     */
    initUI: function() {
        // Обработчики для вкладок
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Делаем все вкладки неактивными
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Делаем выбранную вкладку активной
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab') + '-content';
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Кнопка добавления типа документа
        document.getElementById('add-doc-type').addEventListener('click', () => {
            this.addDocTypeRow();
        });
        
/**
 * JavaScript для страницы настроек приложения
 */

// Объект приложения настроек
const settingsApp = {
    // Исходные данные
    docTypesEnum: {},
    tagsEnum: {},
    
    // Замены для типов документов (новое поле)
    docTypeReplacements: {},
    
    /**
     * Инициализация приложения настроек
     */
    init: function() {
        console.log("Инициализация приложения настроек...");
        
        // Инициализация интерфейса
        this.initUI();
        
        // Загрузка данных
        BX24.ready(() => {
            BX24.init(() => {
                console.log("BX24 готов!");
                this.loadSettings();
            });
        });
    },
    
    /**
     * Инициализация интерфейса
     */
    initUI: function() {
        // Обработчики для вкладок
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Делаем все вкладки неактивными
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Делаем выбранную вкладку активной
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab') + '-content';
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Кнопка добавления типа документа
        document.getElementById('add-doc-type').addEventListener('click', () => {
            this.addDocTypeRow();
        });
        
        // Кнопка добавления тега
        document.getElementById('add-tag').addEventListener('click', () => {
            this.addTagRow();
        });
        
        // Кнопка сохранения типов документов
        document.getElementById('save-doc-types').addEventListener('click', () => {
            this.saveDocTypes();
        });
        
        // Кнопка сохранения тегов
        document.getElementById('save-tags').addEventListener('click', () => {
            this.saveTags();
        });
        
        // Кнопка возврата к приложению
        document.getElementById('back-to-app').addEventListener('click', () => {
            window.location.href = 'index.php';
        });
    },
    
    /**
     * Загрузка настроек
     */
    loadSettings: function() {
        console.log("Загрузка настроек...");
        
        // Загружаем настройки из хранилища Bitrix24
        BX24.callMethod('app.option.get', {}, result => {
            if (result.error()) {
                console.error("Ошибка загрузки настроек:", result.error());
                this.showNotification("Ошибка загрузки настроек", "error");
                return;
            }
            
            const options = result.data();
            console.log("Загруженные настройки:", options);
            
            // Загружаем типы документов
            if (options.docTypesEnum) {
                try {
                    this.docTypesEnum = JSON.parse(options.docTypesEnum);
                    console.log("Загруженные типы документов:", this.docTypesEnum);
                } catch (e) {
                    console.error("Ошибка парсинга типов документов:", e);
                    // Используем значения по умолчанию из app.js
                    this.loadDefaultDocTypesEnum();
                }
            } else {
                // Если настройки не найдены, загружаем значения по умолчанию
                this.loadDefaultDocTypesEnum();
            }
            
            // Загружаем замены для типов документов
            if (options.docTypeReplacements) {
                try {
                    this.docTypeReplacements = JSON.parse(options.docTypeReplacements);
                    console.log("Загруженные замены типов документов:", this.docTypeReplacements);
                } catch (e) {
                    console.error("Ошибка парсинга замен типов документов:", e);
                    this.docTypeReplacements = {}; // Пустой объект по умолчанию
                }
            } else {
                // По умолчанию добавляем базовые замены
                this.docTypeReplacements = {
                    "ПИ": "Протокол",
                    "ССТРТС": "Консультационные и сопроводительные услуги в области подтверждения соответствия"
                };
                console.log("Созданы замены типов документов по умолчанию:", this.docTypeReplacements);
            }
            
            // Загружаем теги
            if (options.tagsEnum) {
                try {
                    this.tagsEnum = JSON.parse(options.tagsEnum);
                    console.log("Загруженные теги:", this.tagsEnum);
                } catch (e) {
                    console.error("Ошибка парсинга тегов:", e);
                    // Используем значения по умолчанию из app.js
                    this.loadDefaultTagsEnum();
                }
            } else {
                // Если настройки не найдены, загружаем значения по умолчанию
                this.loadDefaultTagsEnum();
            }
            
            // Обновляем интерфейс
            this.updateDocTypesTable();
            this.updateTagsTable();
        });
    },
    
    /**
     * Загрузка типов документов по умолчанию из константы в app.js
     */
    loadDefaultDocTypesEnum: function() {
        // По умолчанию используем константу из app.js
        this.docTypesEnum = {
            "759": "ССТРТС",
            "761": "ДСТРТС",
            "763": "ОТКОС",
            "765": "СТ 1 / СТ",
            "767": "СГР",
            "769": "ДСС",
            "771": "ДС ГОСТ Р",
            "773": "ПИ",
            "775": "ИК",
            "777": "Сертификат происхождения",
            "779": "СТО",
            "781": "ТУ",
            "783": "ISO",
            "1093": "Сертификат типа",
            "1045": "Экономический паспорт деятельности",
            "1095": "Паспорт",
            "785": "РУ",
            "787": "Тех. док. (МИ)",
            "789": "ПИ (МИ)",
            "791": "ВИРД",
            "1099": "Отрицательное Решение",
            "1109": "Штрих коды",
            "1117": "Переводы (МИ)",
            "1133": "Сроки годности",
            "1137": "Дополнение к техническому протоколу",
            "1141": "ЭЗ",
            "1143": "Паспорт безопасности",
            "1433": "Нотификация",
            "1145": "Инструкция МИ",
            "1149": "Заключение ФСТЭК",
            "1381": "Письмо РЗН",
            "1473": "Международные переводы",
            "1491": "ОБ"
        };
        console.log("Загружены типы документов по умолчанию:", this.docTypesEnum);
    },
    
    /**
     * Загрузка тегов по умолчанию из константы в app.js
     */
    loadDefaultTagsEnum: function() {
        // По умолчанию используем константу из app.js
        this.tagsEnum = {
            "883": "004",
            "885": "020",
            "887": "010",
            "889": "037",
            "1155": "012",
            "891": "005",
            "893": "007",
            "895": "008",
            "897": "009",
            "899": "015",
            "901": "016",
            "971": "017",
            "903": "019",
            "905": "021",
            "907": "023",
            "909": "024",
            "911": "025",
            "913": "030",
            "915": "031",
            "917": "032",
            "919": "033",
            "921": "034",
            "923": "040",
            "925": "044",
            "927": "РУ",
            "929": "2425",
            "933": "Грин Лайн",
            "973": "051",
            "977": "018",
            "1053": "Иное",
            "1349": "СГР",
            "1421": "Добровольная сертификация",
            "1469": "001",
            "1487": "Честный знак"
        };
        console.log("Загружены теги по умолчанию:", this.tagsEnum);
    },
    
    /**
     * Обновление таблицы типов документов
     */
    updateDocTypesTable: function() {
        const tbody = document.querySelector('#doc-types-table tbody');
        tbody.innerHTML = '';
        
        Object.entries(this.docTypesEnum).forEach(([id, name]) => {
            const row = document.createElement('tr');
            row.dataset.id = id;
            
            // Получаем замену из docTypeReplacements или оставляем пустым
            const replacement = this.docTypeReplacements[name] || '';
            
            row.innerHTML = `
                <td><input type="text" class="doc-type-id" value="${id}" readonly></td>
                <td><input type="text" class="doc-type-name" value="${name}"></td>
                <td><input type="text" class="doc-type-replacement" value="${replacement}"></td>
                <td><button class="btn-delete" onclick="settingsApp.deleteDocType('${id}')">Удалить</button></td>
            `;
            
            tbody.appendChild(row);
        });
    },
    
    /**
     * Обновление таблицы тегов
     */
    updateTagsTable: function() {
        const tbody = document.querySelector('#tags-table tbody');
        tbody.innerHTML = '';
        
        Object.entries(this.tagsEnum).forEach(([id, name]) => {
            const row = document.createElement('tr');
            row.dataset.id = id;
            
            row.innerHTML = `
                <td><input type="text" class="tag-id" value="${id}" readonly></td>
                <td><input type="text" class="tag-name" value="${name}"></td>
                <td><button class="btn-delete" onclick="settingsApp.deleteTag('${id}')">Удалить</button></td>
            `;
            
            tbody.appendChild(row);
        });
    },
    
    /**
     * Добавление новой строки типа документа
     */
    addDocTypeRow: function() {
        const tbody = document.querySelector('#doc-types-table tbody');
        const row = document.createElement('tr');
        row.dataset.new = true;
        
        row.innerHTML = `
            <td><input type="text" class="doc-type-id" placeholder="Введите ID"></td>
            <td><input type="text" class="doc-type-name" placeholder="Введите название"></td>
            <td><input type="text" class="doc-type-replacement" placeholder="Введите замену для счета (если нужно)"></td>
            <td><button class="btn-delete" onclick="this.parentNode.parentNode.remove()">Удалить</button></td>
        `;
        
        tbody.appendChild(row);
    },
    
    /**
     * Добавление новой строки тега
     */
    addTagRow: function() {
        const tbody = document.querySelector('#tags-table tbody');
        const row = document.createElement('tr');
        row.dataset.new = true;
        
        row.innerHTML = `
            <td><input type="text" class="tag-id" placeholder="Введите ID"></td>
            <td><input type="text" class="tag-name" placeholder="Введите название"></td>
            <td><button class="btn-delete" onclick="this.parentNode.parentNode.remove()">Удалить</button></td>
        `;
        
        tbody.appendChild(row);
    },
    
    /**
     * Удаление типа документа
     */
    deleteDocType: function(id) {
        if (confirm('Вы уверены, что хотите удалить этот тип документа?')) {
            delete this.docTypesEnum[id];
            this.updateDocTypesTable();
        }
    },
    
    /**
     * Удаление тега
     */
    deleteTag: function(id) {
        if (confirm('Вы уверены, что хотите удалить этот тег?')) {
            delete this.tagsEnum[id];
            this.updateTagsTable();
        }
    },
    
    /**
     * Сохранение типов документов
     */
    saveDocTypes: function() {
        console.log("Начало сохранения типов документов");
        
        // Обновляем объект docTypesEnum из таблицы
        const docTypesEnum = {};
        const docTypeReplacements = {};
        
        document.querySelectorAll('#doc-types-table tbody tr').forEach(row => {
            const idInput = row.querySelector('.doc-type-id');
            const nameInput = row.querySelector('.doc-type-name');
            const replacementInput = row.querySelector('.doc-type-replacement');
            
            if (idInput && nameInput && replacementInput) {
                const id = idInput.value.trim();
                const name = nameInput.value.trim();
                const replacement = replacementInput.value.trim();
                
                if (id && name) {
                    docTypesEnum[id] = name;
                    
                    // Если есть замена, добавляем в словарь замен
                    if (replacement) {
                        docTypeReplacements[name] = replacement;
                        console.log(`Добавлена замена: "${name}" -> "${replacement}"`);
                    }
                }
            }
        });
        
        console.log("Собранные типы документов:", docTypesEnum);
        console.log("Собранные замены типов документов:", docTypeReplacements);
        
        // Сохраняем изменения
        this.docTypesEnum = docTypesEnum;
        this.docTypeReplacements = docTypeReplacements;
        
        // Сохраняем в хранилище Bitrix24
        BX24.callMethod('app.option.set', {
            options: {
                docTypesEnum: JSON.stringify(docTypesEnum),
                docTypeReplacements: JSON.stringify(docTypeReplacements)
            }
        }, result => {
            if (result.error()) {
                console.error("Ошибка сохранения типов документов:", result.error());
                this.showNotification("Ошибка сохранения типов документов", "error");
            } else {
                console.log("Типы документов успешно сохранены");
                console.log("Ответ от сервера:", result.data());
                this.showNotification("Типы документов успешно сохранены", "success");
            }
        });
    },
    
    /**
     * Сохранение тегов
     */
    saveTags: function() {
        console.log("Начало сохранения тегов");
        
        // Обновляем объект tagsEnum из таблицы
        const tagsEnum = {};
        
        document.querySelectorAll('#tags-table tbody tr').forEach(row => {
            const idInput = row.querySelector('.tag-id');
            const nameInput = row.querySelector('.tag-name');
            
            if (idInput && nameInput) {
                const id = idInput.value.trim();
                const name = nameInput.value.trim();
                
                if (id && name) {
                    tagsEnum[id] = name;
                }
            }
        });
        
        console.log("Собранные теги:", tagsEnum);
        
        // Сохраняем изменения
        this.tagsEnum = tagsEnum;
        
        // Сохраняем в хранилище Bitrix24
        BX24.callMethod('app.option.set', {
            options: {
                tagsEnum: JSON.stringify(tagsEnum)
            }
        }, result => {
            if (result.error()) {
                console.error("Ошибка сохранения тегов:", result.error());
                this.showNotification("Ошибка сохранения тегов", "error");
            } else {
                console.log("Теги успешно сохранены");
                console.log("Ответ от сервера:", result.data());
                this.showNotification("Теги успешно сохранены", "success");
            }
        });
    },
    
    /**
     * Показать уведомление
     */
    showNotification: function(message, type = 'info') {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        
        // Скрываем уведомление через 3 секунды
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
};

// Инициализация приложения при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    settingsApp.init();
});