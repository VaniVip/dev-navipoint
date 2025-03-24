/**
 * Приложение для заполнения счетов Bitrix24
 */

// Справочники и константы (будут загружены из настроек)
let docTypesEnum = {};
let tagsEnum = {};
let docTypeReplacements = {};

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

// Уникальные идентификаторы пользовательских полей
const FIELD_IDS = {
    DOC_TYPES: '1677764118150',
    TAGS: '1679033320990',
    PROTOCOL: '1678277147353'
};

// Идентификатор сущности для смарт-процесса
const ENTITY_TYPE_ID = 171;

// Основное приложение
const app = {
    selectedItems: [], // Выбранные элементы
    companyId: null,
    smartInvoiceId: null,
    stageNames: {}, // Для хранения названий стадий
    productNames: {}, // Для хранения редактируемых названий услуг
    
    /**
     * Инициализация приложения
     */
    init: function() {
        console.log("Инициализация приложения...");
        
        // Ждем загрузки BX24
        BX24.ready(function () {
            BX24.init(function () {
                console.log("BX24 готов!");
                
                // Загрузка справочников из настроек
                app.loadSettings().then(() => {
                    app.loadInvoiceData();
                });
            });
        });
    },
    
    /**
     * Загрузка настроек из хранилища Bitrix24
     */
    loadSettings: function() {
        return new Promise((resolve, reject) => {
            console.log("Загрузка настроек...");
            
            BX24.callMethod('app.option.get', {}, result => {
                if (result.error()) {
                    console.error("Ошибка загрузки настроек:", result.error());
                    // Используем значения по умолчанию
                    this.loadDefaultSettings();
                    resolve();
                    return;
                }
                
                const options = result.data();
                console.log("Загруженные настройки:", options);
                
                // Загружаем типы документов
                if (options.docTypesEnum) {
                    try {
                        docTypesEnum = JSON.parse(options.docTypesEnum);
                        console.log("Загруженные типы документов:", docTypesEnum);
                    } catch (e) {
                        console.error("Ошибка парсинга типов документов:", e);
                        // Используем значения по умолчанию
                        this.loadDefaultDocTypesEnum();
                    }
                } else {
                    // Если настройки не найдены, загружаем значения по умолчанию
                    this.loadDefaultDocTypesEnum();
                }
                
                // Загружаем замены для типов документов
                if (options.docTypeReplacements) {
                    try {
                        docTypeReplacements = JSON.parse(options.docTypeReplacements);
                        console.log("Загруженные замены типов документов:", docTypeReplacements);
                    } catch (e) {
                        console.error("Ошибка парсинга замен типов документов:", e);
                        // Используем значения по умолчанию
                        this.loadDefaultDocTypeReplacements();
                    }
                } else {
                    // Если настройки не найдены, загружаем значения по умолчанию
                    this.loadDefaultDocTypeReplacements();
                }
                
                // Загружаем теги
                if (options.tagsEnum) {
                    try {
                        tagsEnum = JSON.parse(options.tagsEnum);
                        console.log("Загруженные теги:", tagsEnum);
                    } catch (e) {
                        console.error("Ошибка парсинга тегов:", e);
                        // Используем значения по умолчанию
                        this.loadDefaultTagsEnum();
                    }
                } else {
                    // Если настройки не найдены, загружаем значения по умолчанию
                    this.loadDefaultTagsEnum();
                }
                
                console.log("Итоговые настройки после загрузки:");
                console.log("docTypesEnum:", docTypesEnum);
                console.log("tagsEnum:", tagsEnum);
                console.log("docTypeReplacements:", docTypeReplacements);
                
                resolve();
            });
        });
    },
    
    /**
     * Загрузка значений по умолчанию
     */
    loadDefaultSettings: function() {
        this.loadDefaultDocTypesEnum();
        this.loadDefaultTagsEnum();
        this.loadDefaultDocTypeReplacements();
    },
    
    /**
     * Загрузка типов документов по умолчанию
     */
    loadDefaultDocTypesEnum: function() {
        docTypesEnum = {
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
        console.log("Загружены типы документов по умолчанию:", docTypesEnum);
    },
    
    /**
     * Загрузка тегов по умолчанию
     */
    loadDefaultTagsEnum: function() {
        tagsEnum = {
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
        console.log("Загружены теги по умолчанию:", tagsEnum);
    },
    
    /**
     * Загрузка замен типов документов по умолчанию
     */
    loadDefaultDocTypeReplacements: function() {
        docTypeReplacements = {
            "ПИ": "Протокол",
            "ССТРТС": "Консультационные и сопроводительные услуги в области подтверждения соответствия"
        };
        console.log("Загружены замены типов документов по умолчанию:", docTypeReplacements);
    },
    
    /**
     * Загрузка данных счета
     */
    loadInvoiceData: function() {
        const placementInfo = BX24.placement.info();
        this.smartInvoiceId = placementInfo.options.ID;

        console.log(`ID смарт-счёта: ${this.smartInvoiceId}`);

        BX24.callMethod('crm.item.get', {
            entityTypeId: 31, // Smart Invoice
            id: this.smartInvoiceId
        }, function (result) {
            if (result.error()) {
                console.error("Ошибка получения данных смарт-счёта:", result.error());
                document.getElementById('result').innerHTML = "<div class='error-message'>Ошибка загрузки данных смарт-счёта!</div>";
                return;
            }

            const smartInvoice = result.data();
            app.companyId = smartInvoice.item.companyId;

            if (!app.companyId) {
                document.getElementById('result').innerHTML = "<div class='error-message'>У этого счёта не найдено поле companyId!</div>";
                return;
            }

            console.log(`Компания ID: ${app.companyId}`);

            // Загружаем названия стадий и элементы
            app.refreshData();
        });
    },
    
    /**
     * Обновление данных в таблице
     */
    refreshData: function() {
        this.selectedItems = []; // Сбрасываем выбранные элементы при обновлении
        document.getElementById('result').innerHTML = "<div class='loading'>Загрузка данных...</div>";
        
        const activeOnly = document.getElementById('activeOnly').checked;
        console.log(`Фильтр по активным работам: ${activeOnly}`);
        
        // Сначала пытаемся загрузить названия стадий
        this.loadStageNames().then(() => {
            // Затем загружаем элементы
            this.loadItems(this.companyId, activeOnly);
        }).catch(() => {
            // В случае ошибки загружаем элементы без названий стадий
            this.loadItems(this.companyId, activeOnly);
        });
    },
    
    /**
     * Загрузка названий стадий
     */
    loadStageNames: function() {
        return new Promise((resolve, reject) => {
            // Пытаемся получить стадии для основной сущности
            BX24.callMethod('crm.status.list', {
                filter: { 'ENTITY_ID': `DYNAMIC_${ENTITY_TYPE_ID}_STAGE` }
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
                    app.stageNames[stage.STATUS_ID] = stage.NAME;
                });
                
                resolve();
            });
        });
    },
    
    /**
     * Проверяет, является ли стадия финальной
     */
    isFinalStage: function(stageId) {
        if (!stageId) return false;
        return stageId.includes('FAIL') || stageId.includes('SUCCESS') || stageId.includes('FINAL');
    },
    
    /**
     * Находит значение пользовательского поля по части имени
     */
    findFieldValue: function(item, fieldNamePart) {
        const fieldKey = Object.keys(item).find(key => 
            key.toLowerCase().includes(fieldNamePart.toLowerCase())
        );
        return fieldKey ? item[fieldKey] : undefined;
    },
    
    /**
     * Форматирует массив идентификаторов в строку с помощью справочника
     */
    formatEnumValues: function(idsArray, dictionary) {
        if (!idsArray || idsArray.length === 0) return '—';
        return idsArray.map(id => dictionary[id] || id).join(", ");
    },
    
    /**
     * Создает строку с названием услуги для счета с преобразованием
     */
    createServiceName: function(docTypes, tags, protocol) {
        console.log("=== Создание названия услуги для счета ===");
        console.log("Исходное название типа документа:", docTypes);
        
        // Прямая проверка на ПИ и ССТРТС для гарантированной работы
        if (docTypes === "ПИ") {
            console.log("Точное совпадение с 'ПИ', заменяем на 'Протокол'");
            docTypes = "Протокол";
        } else if (docTypes.includes("ПИ")) {
            console.log("Название содержит 'ПИ', заменяем на 'Протокол'");
            docTypes = "Протокол";
        } else if (docTypes === "ССТРТС") {
            console.log("Точное совпадение с 'ССТРТС', заменяем на полное название услуги");
            docTypes = "Консультационные и сопроводительные услуги в области подтверждения соответствия";
        } else if (docTypes.includes("ССТРТС")) {
            console.log("Название содержит 'ССТРТС', заменяем на полное название услуги");
            docTypes = "Консультационные и сопроводительные услуги в области подтверждения соответствия";
        } else {
            // Пробуем использовать словарь замен
            let replaced = false;
            
            // Проверяем точные совпадения
            if (docTypeReplacements[docTypes]) {
                docTypes = docTypeReplacements[docTypes];
                console.log("Найдено точное соответствие в словаре замен, новое значение:", docTypes);
                replaced = true;
            }
            
            // Если точного совпадения нет, ищем частичные
            if (!replaced) {
                for (const [original, replacement] of Object.entries(docTypeReplacements)) {
                    if (docTypes.includes(original)) {
                        docTypes = replacement;
                        console.log("Найдено частичное соответствие для", original, "в словаре замен, новое значение:", docTypes);
                        replaced = true;
                        break;
                    }
                }
            }
            
            if (!replaced) {
                console.log("Замены не найдены, оставляем исходное значение:", docTypes);
            }
        }
        
        console.log("Итоговое название типа документа после всех проверок:", docTypes);
        
        // Формируем полное название услуги
        const serviceName = [docTypes, tags, protocol].filter(value => value !== '—').join(', ');
        console.log("Окончательное название услуги:", serviceName);
        
        return serviceName;
    },
    
    /**
     * Загрузка элементов смарт-процесса
     */
    loadItems: function(companyId, activeOnly) {
        // Запрашиваем все элементы по companyId
        BX24.callMethod('crm.item.list', {
            entityTypeId: ENTITY_TYPE_ID,
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
                const filteredItems = items.filter(item => !app.isFinalStage(item.stageId));
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
                        <input type="checkbox" id="select-all-checkbox" onchange="app.toggleSelectAll(this.checked)">
                    </th>
                    <th class="col-process-name">Название смарт-процесса</th>
                    <th class="col-responsible">Ответственный</th>
                    <th class="col-sum">Сумма</th>
                    <th class="col-service-name">Название услуги для счета</th>
                </tr>`;

            let itemsProcessed = 0;

            items.forEach(item => {
                const title = `<a href="https://gksmart.bitrix24.ru/crm/type/${ENTITY_TYPE_ID}/details/${item.id}/" target="_blank">${item.id} ${item.title}</a>`;
                const opportunity = item.opportunity ? `${item.opportunity} ₽` : '—';
                
                // Получаем значения полей
                const docTypesField = app.findFieldValue(item, FIELD_IDS.DOC_TYPES);
                const tagsField = app.findFieldValue(item, FIELD_IDS.TAGS);
                const protocolField = app.findFieldValue(item, FIELD_IDS.PROTOCOL);

                // Вид документа
                const docTypes = app.formatEnumValues(docTypesField, docTypesEnum);
                console.log(`Элемент ${item.id}: Вид документа:`, docTypes);

                // Теги
                const tags = app.formatEnumValues(tagsField, tagsEnum);
                console.log(`Элемент ${item.id}: Теги:`, tags);

                // № Протокола
                const protocol = protocolField || '—';
                console.log(`Элемент ${item.id}: Протокол:`, protocol);
                
                // Формируем название услуги для счета
                const serviceName = app.createServiceName(docTypes, tags, protocol);
                console.log(`Элемент ${item.id}: Итоговое название услуги:`, serviceName);

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
                            <input type="checkbox" id="${checkboxId}" onchange="app.toggleItemSelection(${item.id}, this.checked)">
                        </td>
                        <td class="col-process-name">${title}</td>
                        <td class="col-responsible">${assignedName}</td>
                        <td class="col-sum">${opportunity}</td>
                        <td class="col-service-name">
                            <input type="text" id="product-name-${item.id}" 
                                class="editable-field" 
                                value="${serviceName}" 
                                onchange="app.updateProductName(${item.id}, this.value)">
                        </td>
                    </tr>`;

                    itemsProcessed++;

                    if (itemsProcessed === items.length) {
                        html += `</table>`;
                        html += `<button class="btn-fill-invoice" onclick="app.fillInvoice()">Заполнить счет</button>`;
                        document.getElementById('result').innerHTML = html;
                    }
                });
            });
        });
    },
    
    /**
     * Заполняет счет выбранными товарами
     */
    fillInvoice: async function() {
        if (this.selectedItems.length === 0) {
            alert("Выберите хотя бы один элемент!");
            return;
        }

        console.log("Выбранные элементы:", this.selectedItems);
        
        try {
            // Получаем все выбранные элементы
            const products = await Promise.all(this.selectedItems.map(async (itemId) => {
                // Получаем значение из редактируемого поля
                const inputField = document.getElementById(`product-name-${itemId}`);
                if (!inputField) {
                    throw new Error(`Не найдено поле для редактирования названия услуги элемента ${itemId}`);
                }
                
                const productName = inputField.value.trim();
                console.log(`Название услуги для элемента ${itemId}:`, productName);
                
                // Получаем цену из данных
                return new Promise((resolve, reject) => {
                    BX24.callMethod('crm.item.get', {
                        entityTypeId: ENTITY_TYPE_ID,
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
                });
            }));
            
            // Счетчик успешно добавленных товаров
            let addedCount = 0;
            let errors = [];
            
            // Для каждого товара вызываем метод добавления
            const results = await Promise.all(products.map(product => {
                return app.addProductToInvoice(this.smartInvoiceId, product.name, product.price, product.quantity);
            }));
            
            // Подсчитываем результаты
            results.forEach(result => {
                if (result.success) {
                    addedCount++;
                } else {
                    errors.push(result.error);
                }
            });
            
            if (errors.length > 0) {
                alert(`Добавлено ${addedCount} из ${products.length} товаров. Были ошибки, см. консоль.`);
            } else {
                alert(`Успешно добавлено ${addedCount} товаров в счет!`);
            }
            
            BX24.close();
            
        } catch (error) {
            console.error("Ошибка при обработке элементов:", error);
            alert("Произошла ошибка при обработке выбранных элементов!");
        }
    },
    
    /**
     * Добавляет товар в счет
     */
    addProductToInvoice: function(ownerId, productName, price, quantity = 1) {
        return new Promise((resolve) => {
            BX24.callMethod('crm.item.productrow.add', {
                fields: {
                    ownerId: ownerId,
                    ownerType: 'SI',
                    productId: 0,
                    productName: productName,
                    price: price,
                    quantity: quantity
                }
            }, function(result) {
                if (result.error()) {
                    console.error("Ошибка добавления товара:", result.error());
                    resolve({ success: false, error: result.error() });
                } else {
                    console.log("Товар успешно добавлен:", result.data());
                    resolve({ success: true, data: result.data() });
                }
            });
        });
    },
    
    /**
     * Обновляет название услуги
     */
    updateProductName: function(itemId, value) {
        this.productNames[itemId] = value;
        console.log(`Обновлено название услуги для элемента ${itemId}:`, value);
    },
    
    /**
     * Переключает выбор всех элементов
     */
    toggleSelectAll: function(checked) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="item-checkbox-"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const itemId = parseInt(checkbox.id.replace('item-checkbox-', ''), 10);
            
            if (checked) {
                // Добавляем ID в массив выбранных, если его еще нет
                if (!this.selectedItems.includes(itemId)) {
                    this.selectedItems.push(itemId);
                }
            } else {
                // Удаляем ID из массива выбранных
                this.selectedItems = this.selectedItems.filter(id => id !== itemId);
            }
        });
        
        console.log("Выбранные элементы после переключения:", this.selectedItems);
    },
    
    /**
     * Переключает выбор отдельного элемента
     */
    toggleItemSelection: function(itemId, checked) {
        if (checked) {
            if (!this.selectedItems.includes(itemId)) {
                this.selectedItems.push(itemId);
            }
        } else {
            this.selectedItems = this.selectedItems.filter(id => id !== itemId);
            
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
        
        console.log("Текущие выбранные элементы:", this.selectedItems);
    }
};

// Инициализация приложения при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    app.init();
});