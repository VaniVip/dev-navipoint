<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список услуг компании</title>
    <style>
        .btn-fill-invoice {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-fill-invoice:hover {
            background-color: #2980b9;
        }
        .checkbox-column {
            width: 30px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Список услуг компании</h2>
    <div id="result"></div>

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

        // Глобальная переменная для хранения выбранных элементов
        let selectedItems = [];

        // Функция для обработки нажатия на кнопку "Заполнить счет"
        // Функция для обработки нажатия на кнопку "Заполнить счет"











//
// Функция для обработки нажатия на кнопку "Заполнить счет"
// Функция для обработки нажатия на кнопку "Заполнить счет"
function fillInvoice() {
    if (selectedItems.length === 0) {
        alert("Выберите хотя бы один элемент!");
        return;
    }

    console.log("Выбранные элементы:", selectedItems);
    
    const placementInfo = BX24.placement.info();
    const smartInvoiceId = placementInfo.options.ID;
    
    // Получаем все выбранные элементы
    const promises = selectedItems.map(itemId => {
        return new Promise((resolve, reject) => {
            BX24.callMethod('crm.item.get', {
                entityTypeId: 171,
                id: itemId,
                select: ['*', 'UF_*']
            }, function(result) {
                if (result.error()) {
                    console.error(`Ошибка получения данных элемента ${itemId}:`, result.error());
                    reject(result.error());
                    return;
                }
                
                const item = result.data().item;
                
                // Получаем значения полей
                function findFieldValue(item, fieldNamePart) {
                    const fieldKey = Object.keys(item).find(key => 
                        key.toLowerCase().includes(fieldNamePart.toLowerCase())
                    );
                    return fieldKey ? item[fieldKey] : undefined;
                }
                
                const docTypesField = findFieldValue(item, '1677764118150') || [];
                const tagsField = findFieldValue(item, '1679033320990') || [];
                const protocolField = findFieldValue(item, '1678277147353') || '—';
                const opportunity = item.opportunity || 0;
                
                // Получаем значения для названия товара
                const docTypes = docTypesField.length > 0
                    ? docTypesField.map(id => docTypesEnum[id] || id).join(", ")
                    : '—';
                
                const tags = tagsField.length > 0
                    ? tagsField.map(id => tagsEnum[id] || id).join(", ")
                    : '—';
                
                // Формируем название товара
                const productName = `${docTypes} / ${tags} / ${protocolField}`.trim();
                
                resolve({
                    name: productName,
                    price: opportunity,
                    quantity: 1
                });
            });
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




        

















        // Функция для обработки изменения состояния чекбокса
        function toggleItemSelection(itemId, checked) {
            if (checked) {
                if (!selectedItems.includes(itemId)) {
                    selectedItems.push(itemId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== itemId);
            }
            console.log("Текущие выбранные элементы:", selectedItems);
        }

        // Ждем загрузки BX24
        BX24.ready(function () {
            BX24.init(function () {
                console.log("BX24 готов!");

                const placementInfo = BX24.placement.info();
                const smartInvoiceId = placementInfo.options.ID;

                console.log(`ID смарт-счёта: ${smartInvoiceId}`);

                BX24.callMethod('crm.item.get', {
                    entityTypeId: 31,
                    id: smartInvoiceId
                }, function (result) {
                    if (result.error()) {
                        console.error("Ошибка получения данных смарт-счёта:", result.error());
                        document.getElementById('result').innerHTML = "Ошибка загрузки данных смарт-счёта!";
                        return;
                    }

                    const smartInvoice = result.data();
                    const companyId = smartInvoice.item.companyId;

                    if (!companyId) {
                        document.getElementById('result').innerHTML = "У этого счёта не найдено поле companyId!";
                        return;
                    }

                    console.log(`Компания ID: ${companyId}`);

                    // Запрашиваем данные с расширенным выбором полей
                    BX24.callMethod('crm.item.list', {
                        entityTypeId: 171,
                        filter: { 'companyId': companyId },
                        select: ['*', 'UF_*']  // Запрашиваем все поля и все пользовательские поля
                    }, function (itemsResult) {
                        if (itemsResult.error()) {
                            console.error("Ошибка при получении списка смартов:", itemsResult.error());
                            document.getElementById('result').innerHTML = "Ошибка при получении списка смартов!";
                            return;
                        }

                        const items = itemsResult.data().items;
                        console.log(`Найдено элементов: ${items.length}`);

                        if (items.length === 0) {
                            document.getElementById('result').innerHTML = "Нет элементов смарт-процесса по этой компании.";
                            return;
                        }

                        // Выводим первый элемент полностью для анализа структуры
                        if (items.length > 0) {
                            console.log("Полная структура первого элемента:", items[0]);
                        }

                        let html = `<p>Найдено элементов: ${items.length}</p>`;
                        html += `<table border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th class="checkbox-column">Выбор</th>
                                <th>Название смарт-процесса</th>
                                <th>Ответственный</th>
                                <th>Сумма</th>
                                <th>Вид документа</th>
                                <th>Теги</th>
                                <th>№ Протокола</th>
                            </tr>`;

                        let itemsProcessed = 0;

                        items.forEach(item => {
                            // Выводим в консоль все ключи объекта для анализа
                            console.log("Ключи элемента:", Object.keys(item));
                            
                            const title = `<a href="https://gksmart.bitrix24.ru/crm/type/171/details/${item.id}/" target="_blank">${item.id} ${item.title}</a>`;
                            const opportunity = item.opportunity ?? '—';
                            
                            // Универсальный способ получения значений пользовательских полей
                            // Ищем нужные поля независимо от регистра
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
                            
                            console.log(`ID элемента: ${item.id}`);
                            console.log(`Поле вида документа:`, docTypesField);
                            console.log(`Поле тегов:`, tagsField);
                            console.log(`Поле протокола:`, protocolField);

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

                            // Получаем имя ответственного
                            BX24.callMethod('user.get', {
                                ID: item.assignedById
                            }, function (userResult) {
                                let assignedName = '—';
                                const user = userResult.data()[0];
                                if (user) {
                                    assignedName = `${user.NAME} ${user.LAST_NAME}`;
                                }

                                // Создаем уникальный ID для чекбокса
                                const checkboxId = `item-checkbox-${item.id}`;
                                
                                html += `<tr>
                                    <td class="checkbox-column">
                                        <input type="checkbox" id="${checkboxId}" onchange="toggleItemSelection(${item.id}, this.checked)">
                                    </td>
                                    <td>${title}</td>
                                    <td>${assignedName}</td>
                                    <td>${opportunity}</td>
                                    <td>${docTypes}</td>
                                    <td>${tags}</td>
                                    <td>${protocol}</td>
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
                });
            });
        });
    </script>
</body>
</html>