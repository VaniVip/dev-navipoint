
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список услуг компании</title>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        h2 {
            font-size: 18px;
            font-weight: normal;
            margin: 10px 0;
            padding: 10px;
            color: #000;
        }
        
        .btn-fill-invoice {
            margin-top: 15px;
            padding: 8px 12px;
            background-color: #bbed21;
            color: #525c69;
            border: none;
            border-radius: 3px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: bold;
        }
        
        .btn-fill-invoice:hover {
            background-color: #d2f95f;
        }
        
        .checkbox-column {
            width: 30px;
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #eef2f4;
            color: #525c69;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: normal;
            color: #6a737f;
            border-top: 1px solid #eef2f4;
            position: relative;
        }
        
        tr:hover {
            background-color: #f6f8f9;
        }
        
        .filter-panel {
            margin-bottom: 15px;
            padding: 12px;
            background-color: #fff;
            border: 1px solid #eef2f4;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #525c69;
        }
        
        .checkbox-label input {
            margin-right: 8px;
        }
        
        .error-message {
            padding: 12px;
            margin: 10px 0;
            background-color: #ffe8e8;
            border-radius: 3px;
            color: #d0021b;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #6a737f;
        }
        
        .editable-field {
            width: 100%;
            box-sizing: border-box;
            padding: 8px 10px;
            border: 1px solid #c6cdd3;
            border-radius: 3px;
            min-height: 36px;
            color: #525c69;
            font-size: 14px;
            transition: border 0.2s;
        }
        
        .editable-field:focus {
            outline: none;
            border-color: #bbed21;
        }
        
        a {
            color: #2067b0;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
            color: #1d5da8;
        }
        
        /* Настройка ширины столбцов */
        .col-process-name {
            width: 20%;
        }
        
        .col-responsible {
            width: 15%;
        }
        
        .col-sum {
            width: 10%;
        }
        
        .col-service-name {
            width: 55%;
        }
        
        /* Стили для чекбоксов в стиле Bitrix24 */
        input[type="checkbox"] {
            position: relative;
            width: 16px;
            height: 16px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #fff;
            border: 1px solid #c6cdd3;
            border-radius: 2px;
            cursor: pointer;
            outline: none;
        }
        
        input[type="checkbox"]:checked {
            background-color: #bbed21;
            border-color: #bbed21;
        }
        
        input[type="checkbox"]:checked:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
    </style>
</head>
<body>
    <h2>Список услуг компании</h2>
    
    <div class="filter-panel">
        <label class="checkbox-label">
            <input type="checkbox" id="activeOnly" checked onchange="refreshData()">
            <span>Вывести только активные работы</span>
        </label>
    </div>
    
    <div id="result" class="loading">Загрузка данных...</div>

    <script src="https://api.bitrix24.com/api/v1/"></script>
    <script>
        // Массивы справочников
        const docTypesEnum = {
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

        const tagsEnum = {
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

        // Словарь названий стадий
        const stageNamesMap = {
            'DT171_9_NEW': 'Новая',
            'DT171_9_PREPARATION': 'В работе',
            'DT171_9_CLIENT_INFORMATION': 'Ожидание клиента',
            'DT171_9_UC_4EH25B': 'Ожидание оплаты',
            'DT171_9_UC_TPAEUH': 'Получение товара',
            'DT171_9_UC_RDOTWL': 'Оформление документов',
            'DT171_9_FAIL': 'Отказ',
            'DT171_9_SUCCESS': 'Успешно реализовано',
            'PREPARATION': 'В работе',
            'CLIENT_INFORMATION': 'Ожидание клиента',
            'FAIL': 'Отказ',
            'SUCCESS': 'Успешно реализовано'
        };

        // Глобальные переменные
        let selectedItems = []; // Выбранные элементы
        let companyId = null;
        let smartInvoiceId = null;
        let stageNames = {}; // Для хранения названий стадий
        let productNames = {}; // Для хранения редактируемых названий услуг

        // Функция для обработки нажатия на кнопку "Заполнить счет"
        function fillInvoice() {
            if (selectedItems.length === 0) {
                alert("Выберите хотя бы один элемент!");
                return;
            }

            console.log("Выбранные элементы:", selectedItems);
            
            // Получаем все выбранные элементы
            const promises = selectedItems.map(itemId => {
                return new Promise((resolve, reject) => {
                    // Получаем значение из редактируемого поля
                    const inputField = document.getElementById(`product-name-${itemId}`);
                    if (inputField) {
                        const productName = inputField.value.trim();
                        // Получаем цену из данных
                        BX24.callMethod('crm.item.get', {
                            entityTypeId: 171,
                            id: itemId,
                            select: ['opportunity']
                        }, function(result) {
                            if (result.error()) {
                                console.error(`Ошибка получения данных элемента ${itemId}:`, result.error());
                                reject(result.error());
                                return;
                            }
                            
                            const item = result.data().item;
                            const opportunity = item.opportunity || 0;
                            
                            resolve({
                                name: productName,
                                price: opportunity,
                                quantity: 1
                            });
                        });
                    } else {
                        reject(new Error(`Не найдено поле для редактирования названия услуги элемента ${itemId}`));
                    }
                });
            });
            
            // Дожидаемся получения всех данных
            Promise.all(promises)
                .then(products => {
                    // Счетчик успешно добавленных товаров
                    let addedCount = 0;
                    let errors = [];
                    
                    // Для каждого товара вызываем метод добавления
                    const addProductPromises = products.map(product => {
                        return new Promise((resolve) => {
                            BX24.callMethod('crm.item.productrow.add', {
                                fields: {
                                    ownerId: smartInvoiceId,
                                    ownerType: 'SI',
                                    productId: 0,
                                    productName: product.name,
                                    price: product.price,
                                    quantity: product.quantity
                                }
                            }, function(result) {
                                if (result.error()) {
                                    console.error("Ошибка добавления товара:", result.error());
                                    errors.push(result.error());
                                    resolve(false);
                                } else {
                                    console.log("Товар успешно добавлен:", result.data());
                                    addedCount++;
                                    resolve(true);
                                }
                            });
                        });
                    });
                    
                    // Дожидаемся завершения всех запросов
                    Promise.all(addProductPromises)
                        .then(() => {
                            if (errors.length > 0) {
                                alert(`Добавлено ${addedCount} из ${products.length} товаров. Были ошибки, см. консоль.`);
                            } else {
                                alert(`Успешно добавлено ${addedCount} товаров в счет!`);
                            }
                            BX24.close();
                        });
                })
                .catch(error => {
                    console.error("Ошибка при обработке элементов:", error);
                    alert("Произошла ошибка при обработке выбранных элементов!");
                });
        }

        // Функция для сохранения измененного названия услуги
        function updateProductName(itemId, value) {
            productNames[itemId] = value;
            console.log(`Обновлено название услуги для элемента ${itemId}:`, value);
        }

        // Функция для обработки изменения состояния главного чекбокса
        function toggleSelectAll(checked) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="item-checkbox-"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
                const itemId = parseInt(checkbox.id.replace('item-checkbox-', ''), 10);
                
                if (checked) {
                    // Добавляем ID в массив выбранных, если его еще нет
                    if (!selectedItems.includes(itemId)) {
                        selectedItems.push(itemId);
                    }
                } else {
                    // Удаляем ID из массива выбранных
                    selectedItems = selectedItems.filter(id => id !== itemId);
                }
            });
            
            console.log("Выбранные элементы после переключения:", selectedItems);
        }

        // Функция для обработки изменения состояния чекбокса выбора элемента
        function toggleItemSelection(itemId, checked) {
            if (checked) {
                if (!selectedItems.includes(itemId)) {
                    selectedItems.push(itemId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== itemId);
                
                // Снимаем отметку с главного чекбокса, если хоть один элемент не выбран
                document.getElementById('select-all-checkbox').checked = false;
            }
            
            // Проверяем, все ли чекбоксы отмечены
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="item-checkbox-"]');
            const allChecked = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
            
            // Если все отмечены, отмечаем и главный чекбокс
            if (allChecked && allCheckboxes.length > 0) {
                document.getElementById('select-all-checkbox').checked = true;
            }
            
            console.log("Текущие выбранные элементы:", selectedItems);
        }

        // Функция для получения понятного названия стадии
        function getReadableStageName(stageId) {
            if (!stageId) return '—';
            
            // Проверяем в нашем словаре
            if (stageNamesMap[stageId]) {
                return stageNamesMap[stageId];
            }
            
            // Проверяем в динамически полученных названиях
            if (stageNames[stageId]) {
                return stageNames[stageId];
            }
            
            // Пытаемся сделать название более читабельным
            if (stageId.includes('_')) {
                const parts = stageId.split('_');
                // Берем последнюю часть
                const lastPart = parts[parts.length - 1];
                
                // Формируем понятное название по разным паттернам
                if (lastPart === 'NEW') return 'Новая';
                if (lastPart === 'PREPARATION') return 'В работе';
                if (lastPart === 'CLIENT_INFORMATION') return 'Ожидание клиента';
                if (lastPart === 'FAIL') return 'Отказ';
                if (lastPart === 'SUCCESS') return 'Успешно реализовано';
            }
            
            // Если ничего не подошло, возвращаем исходный ID
            return stageId;
        }

        // Функция для обновления данных в таблице
        function refreshData() {
            selectedItems = []; // Сбрасываем выбранные элементы при обновлении
            document.getElementById('result').innerHTML = "<div class='loading'>Загрузка данных...</div>";
            
            const activeOnly = document.getElementById('activeOnly').checked;
            console.log(`Фильтр по активным работам: ${activeOnly}`);
            
            // Сначала пытаемся загрузить названия стадий
            loadStageNames().then(() => {
                // Затем загружаем элементы
                loadItems(companyId, activeOnly);
            }).catch(() => {
                // В случае ошибки загружаем элементы без названий стадий
                loadItems(companyId, activeOnly);
            });
        }

        // Функция для загрузки названий стадий
        function loadStageNames() {
            return new Promise((resolve, reject) => {
                // Пытаемся получить стадии для основной сущности
                BX24.callMethod('crm.status.list', {
                    filter: { 'ENTITY_ID': 'DYNAMIC_171_STAGE' }
                }, function(result) {
                    if (result.error()) {
                        console.error("Ошибка получения стадий:", result.error());
                        reject(result.error());
                        return;
                    }
                    
                    const stages = result.data();
                    console.log("Получены стадии:", stages);
                    
                    // Сохраняем названия стадий
                    stages.forEach(stage => {
                        stageNames[stage.STATUS_ID] = stage.NAME;
                    });
                    
                    resolve();
                });
            });
        }

        // Простой метод проверки завершенной стадии по ID
        function isFinalStage(stageId) {
            if (!stageId) return false;
            
            // Определяем завершенные стадии по части ID (более простой подход)
            return stageId.includes('FAIL') || stageId.includes('SUCCESS') || stageId.includes('FINAL');
        }

        // Функция для загрузки элементов
        function loadItems(companyId, activeOnly) {
            // Запрашиваем все элементы по companyId
            BX24.callMethod('crm.item.list', {
                entityTypeId: 171,
                filter: { 'companyId': companyId },
                select: ['*', 'UF_*']  // Запрашиваем все поля и все пользовательские поля
            }, function (itemsResult) {
                if (itemsResult.error()) {
                    console.error("Ошибка при получении списка смартов:", itemsResult.error());
                    document.getElementById('result').innerHTML = "<div class='error-message'>Ошибка при получении списка смартов!</div>";
                    return;
                }

                let items = itemsResult.data().items;
                console.log(`Найдено элементов всего: ${items.length}`);

                // Если включен фильтр "только активные", фильтруем элементы
                if (activeOnly) {
                    const filteredItems = items.filter(item => !isFinalStage(item.stageId));
                    console.log(`После фильтрации осталось элементов: ${filteredItems.length}`);
                    items = filteredItems;
                }

                if (items.length === 0) {
                    document.getElementById('result').innerHTML = "<div class='error-message'>Нет элементов смарт-процесса по этой компании.</div>";
                    return;
                }

                let html = `<p>Найдено элементов: ${items.length}</p>`;
                html += `<table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <th class="checkbox-column">
                            <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this.checked)">
                        </th>
                        <th class="col-process-name">Название смарт-процесса</th>
                        <th class="col-responsible">Ответственный</th>
                        <th class="col-sum">Сумма</th>
                        <th class="col-service-name">Название услуги для счета</th>
                    </tr>`;

                let itemsProcessed = 0;

                items.forEach(item => {
                    const title = `<a href="https://gksmart.bitrix24.ru/crm/type/171/details/${item.id}/" target="_blank">${item.id} ${item.title}</a>`;
                    const opportunity = item.opportunity ? `${item.opportunity} ₽` : '—';
                    const stageId = item.stageId || '—';
                    
                    // Универсальный способ получения значений пользовательских полей
                    function findFieldValue(item, fieldNamePart) {
                        const fieldKey = Object.keys(item).find(key => 
                            key.toLowerCase().includes(fieldNamePart.toLowerCase())
                        );
                        return fieldKey ? item[fieldKey] : undefined;
                    }
                    
                    // Получаем значения полей
                    const docTypesField = findFieldValue(item, '1677764118150');
                    const tagsField = findFieldValue(item, '1679033320990');
                    const protocolField = findFieldValue(item, '1678277147353');

                    // Вид документа
                    const docTypesArray = docTypesField || [];
                    const docTypes = docTypesArray.length > 0
                        ? docTypesArray.map(id => docTypesEnum[id] || id).join(", ")
                        : '—';

                    // Теги
                    const tagsArray = tagsField || [];
                    const tags = tagsArray.length > 0
                        ? tagsArray.map(id => tagsEnum[id] || id).join(", ")
                        : '—';

                    // № Протокола
                    const protocol = protocolField || '—';
                    
                    // Формируем название услуги для счета
                    const serviceName = [docTypes, tags, protocol].filter(value => value !== '—').join(', ');

                    // Получаем имя ответственного
                    BX24.callMethod('user.get', {
                        ID: item.assignedById
                    }, function (userResult) {
                        let assignedName = '—';
                        
                        // Если есть данные пользователя, получаем имя
                        if (!userResult.error()) {
                            const user = userResult.data()[0];
                            if (user) {
                                assignedName = `${user.NAME} ${user.LAST_NAME}`;
                            }
                        }

                        // Создаем уникальный ID для чекбокса
                        const checkboxId = `item-checkbox-${item.id}`;
                        
                        html += `<tr>
                            <td class="checkbox-column">
                                <input type="checkbox" id="${checkboxId}" onchange="toggleItemSelection(${item.id}, this.checked)">
                            </td>
                            <td class="col-process-name">${title}</td>
                            <td class="col-responsible">${assignedName}</td>
                            <td class="col-sum">${opportunity}</td>
                            <td class="col-service-name">
                                <input type="text" id="product-name-${item.id}" 
                                    class="editable-field" 
                                    value="${serviceName}" 
                                    onchange="updateProductName(${item.id}, this.value)">
                            </td>
                        </tr>`;

                        itemsProcessed++;

                        if (itemsProcessed === items.length) {
                            html += `</table>`;
                            html += `<button class="btn-fill-invoice" onclick="fillInvoice()">Заполнить счет</button>`;
                            document.getElementById('result').innerHTML = html;
                        }
                    });
                });
            });
        }

        // Ждем загрузки BX24
        BX24.ready(function () {
            BX24.init(function () {
                console.log("BX24 готов!");

                const placementInfo = BX24.placement.info();
                smartInvoiceId = placementInfo.options.ID;

                console.log(`ID смарт-счёта: ${smartInvoiceId}`);

                BX24.callMethod('crm.item.get', {
                    entityTypeId: 31,
                    id: smartInvoiceId
                }, function (result) {
                    if (result.error()) {
                        console.error("Ошибка получения данных смарт-счёта:", result.error());
                        document.getElementById('result').innerHTML = "<div class='error-message'>Ошибка загрузки данных смарт-счёта!</div>";
                        return;
                    }

                    const smartInvoice = result.data();
                    companyId = smartInvoice.item.companyId;

                    if (!companyId) {
                        document.getElementById('result').innerHTML = "<div class='error-message'>У этого счёта не найдено поле companyId!</div>";
                        return;
                    }

                    console.log(`Компания ID: ${companyId}`);

                    // Загружаем названия стадий и элементы
                    refreshData();
                });
            });
        });
    </script>
</body>
</html>
