<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>CSV to PDF Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0972b2 0%, #0972b2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5em;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }

        .step {
            margin-bottom: 40px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 15px;
            /*0972b2*/
            /*border-left: 5px solid #0972b2;*/
        }

        .step-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-number {
            background: #0972b2;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="file"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #0972b2;
            border-radius: 10px;
            background: white;
            font-size: 1em;
            transition: all 0.3s;
        }

        input[type="file"] {
            border: 2px dashed #0972b2;
            cursor: pointer;
        }

        input[type="file"]:hover {
            border-color: #764ba2;
            background: #f8f9fa;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            background: #0972b2;
            /*background: linear-gradient(135deg, #0972b2 0%, #764ba2 100%);*/
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .mapping-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .mapping-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .mapping-item select {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .export-settings {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .export-setting-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .export-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .export-info h4 {
            color: #0066cc;
            margin-bottom: 10px;
        }

        .status {
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status.show {
            display: block;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0972b2;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📄 CSV to PDF Generator</h1>
    <p class="subtitle">Загрузите CSV файл и PDF шаблон для генерации объединенного PDF</p>

    <!-- Шаг 1: Загрузка файлов -->
    <div class="step" id="step1">
        <div class="step-title">
            <span class="step-number">1</span>
            <span>Загрузка файлов</span>
        </div>
        <form id="uploadForm">
            <div class="form-group">
                <label for="template_select">Выберите PDF шаблон</label>
                <select id="template_select" name="template" required style="width: 100%; padding: 12px; border: 2px solid #0972b2; border-radius: 10px; background: white; font-size: 1em; cursor: pointer;">
                    <option value="">-- Выберите шаблон --</option>
                    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($template['filename']); ?>"><?php echo e($template['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div id="templateFieldsPreview" style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; display: none;">
                    <strong>Поля в шаблоне:</strong>
                    <div id="templateFieldsList" style="margin-top: 5px; color: #666;"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="csv_file">CSV файл с данными</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required>
            </div>
            <button type="submit" class="btn">Загрузить CSV и выбрать шаблон</button>
        </form>
        <div class="status" id="uploadStatus"></div>
        <div class="loading" id="uploadLoading">
            <div class="spinner"></div>
            <p>Обработка файлов...</p>
        </div>
    </div>

    <!-- Шаг 2: Сопоставление полей -->
    <div class="step hidden" id="step2">
        <div class="step-title">
            <span class="step-number">2</span>
            <span>Сопоставление полей</span>
        </div>
        <div id="manualFieldsContainer" style="margin-bottom: 20px; display: none;">
            <label style="display: block; margin-bottom: 10px; font-weight: 500;">Если поля не были найдены автоматически, введите их вручную (через запятую):</label>
            <input type="text" id="manualFieldsInput" placeholder="name, email, date, amount, description, company" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">
            <button type="button" class="btn" id="addManualFieldsBtn" style="background: #28a745; margin-bottom: 20px;">Добавить поля</button>
        </div>
        <div id="mappingContainer"></div>
        <button type="button" class="btn" id="saveMappingBtn" style="margin-top: 20px;">Сохранить сопоставление</button>
        <div class="status" id="mappingStatus"></div>
    </div>

    <!-- Шаг 3: Настройки экспорта -->
    <div class="step hidden" id="step3">
        <div class="step-title">
            <span class="step-number">3</span>
            <span>Настройки экспорта</span>
        </div>

        <div class="export-settings">
            <div class="export-setting-item">
                <div class="form-group">
                    <label for="start_row">Начальная строка</label>
                    <input type="number" id="start_row" name="start_row" min="1" value="1" required>
                    <small style="color: #666; font-size: 0.9em;">С какой строки CSV начинать экспорт (первая строка = 1)</small>
                </div>
            </div>

            <div class="export-setting-item">
                <div class="form-group">
                    <label for="row_count">Количество строк</label>
                    <input type="number" id="row_count" name="row_count" min="1" max="500" value="250" required>
                    <small style="color: #666; font-size: 0.9em;">Максимальное количество строк для экспорта (1-500)</small>
                </div>
            </div>
        </div>

        <div class="export-info">
            <h4>💡 Информация о данных</h4>
            <p id="exportInfoText">Всего строк в CSV: <span id="totalRows">0</span></p>
            <p>Будет экспортировано: <span id="exportRange">строки 1-250</span></p>
        </div>

        <button type="button" class="btn" id="saveExportSettingsBtn" style="margin-top: 20px;">Сохранить настройки экспорта</button>
        <div class="status" id="exportSettingsStatus"></div>
    </div>

    <!-- Шаг 4: Генерация PDF -->
    <div class="step hidden" id="step4">
        <div class="step-title">
            <span class="step-number">4</span>
            <span>Генерация PDF</span>
        </div>
        <div class="export-info">
            <h4>📋 Параметры экспорта</h4>
            <p>Начальная строка: <span id="finalStartRow">1</span></p>
            <p>Количество строк: <span id="finalRowCount">250</span></p>
            <p>Всего страниц: ~<span id="estimatedPages">250</span></p>
        </div>
        <button type="button" class="btn" id="generateBtn" disabled>Сгенерировать PDF</button>
        <div class="status" id="generateStatus"></div>
        <div class="loading" id="generateLoading">
            <div class="spinner"></div>
            <p>Генерация PDF файла...</p>
        </div>
    </div>
</div>

<script>
    let csvHeaders = [];
    let pdfFields = [];
    let totalCsvRows = 0;

    // Загрузка полей шаблона при выборе
    document.getElementById('template_select').addEventListener('change', async function() {
        const template = this.value;
        const preview = document.getElementById('templateFieldsPreview');
        const fieldsList = document.getElementById('templateFieldsList');

        if (!template) {
            preview.style.display = 'none';
            return;
        }

        try {
            const response = await fetch('/get-template-fields', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ template: template })
            });

            const data = await response.json();

            if (data.success && data.fields.length > 0) {
                fieldsList.textContent = data.fields.join(', ');
                preview.style.display = 'block';
            } else {
                fieldsList.textContent = 'Поля не найдены автоматически';
                preview.style.display = 'block';
            }
        } catch (error) {
            fieldsList.textContent = 'Ошибка при загрузке полей';
            preview.style.display = 'block';
        }
    });

    // Загрузка файлов
    document.getElementById('uploadForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('csv_file', document.getElementById('csv_file').files[0]);
        formData.append('template', document.getElementById('template_select').value);

        const loading = document.getElementById('uploadLoading');
        const status = document.getElementById('uploadStatus');

        loading.classList.add('show');
        status.classList.remove('show');

        try {
            const response = await fetch('/upload-csv', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();

            if (data.success) {
                csvHeaders = data.csv_headers || [];
                pdfFields = data.pdf_fields || [];
                totalCsvRows = data.csv_rows_count || 0;

                status.textContent = `Успешно! Найдено ${totalCsvRows} записей в CSV и ${pdfFields.length} полей в PDF`;
                status.className = 'status success show';

                // Обновляем информацию о данных
                document.getElementById('totalRows').textContent = totalCsvRows;
                updateExportRange();

                // Показываем шаг 2
                document.getElementById('step2').classList.remove('hidden');

                // Если поля не найдены, показываем возможность ручного ввода
                if (pdfFields.length === 0) {
                    document.getElementById('manualFieldsContainer').style.display = 'block';
                    status.textContent += '. Поля не найдены автоматически - введите их вручную.';
                    status.className = 'status error show';
                }

                buildMappingInterface();
            } else {
                status.textContent = data.message || 'Ошибка при загрузке файлов';
                status.className = 'status error show';
            }
        } catch (error) {
            status.textContent = 'Ошибка: ' + error.message;
            status.className = 'status error show';
        } finally {
            loading.classList.remove('show');
        }
    });

    // Обновление диапазона экспорта
    function updateExportRange() {
        const startRow = parseInt(document.getElementById('start_row').value) || 1;
        const rowCount = parseInt(document.getElementById('row_count').value) || 250;
        const endRow = Math.min(startRow + rowCount - 1, totalCsvRows);

        document.getElementById('exportRange').textContent = `строки ${startRow}-${endRow}`;
    }

    // Слушатели изменений для полей экспорта
    document.getElementById('start_row').addEventListener('change', updateExportRange);
    document.getElementById('row_count').addEventListener('change', updateExportRange);

    // Построение интерфейса сопоставления
    function buildMappingInterface() {
        const container = document.getElementById('mappingContainer');
        container.innerHTML = '';

        if (pdfFields.length === 0) {
            container.innerHTML = '<p style="color: #666; padding: 20px; text-align: center;">Поля не найдены. Введите их вручную выше.</p>';
            return;
        }

        pdfFields.forEach(pdfField => {
            const mappingItem = document.createElement('div');
            mappingItem.className = 'mapping-item';

            const label = document.createElement('label');
            label.textContent = pdfField;
            label.style.minWidth = '150px';

            const select = document.createElement('select');
            select.name = `mapping[${pdfField}]`;
            select.id = `mapping_${pdfField}`;

            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '-- Выберите поле --';
            select.appendChild(emptyOption);

            csvHeaders.forEach(header => {
                const option = document.createElement('option');
                option.value = header;
                option.textContent = header;
                select.appendChild(option);
            });

            mappingItem.appendChild(label);
            mappingItem.appendChild(document.createTextNode(' → '));
            mappingItem.appendChild(select);

            container.appendChild(mappingItem);
        });
    }

    // Добавление полей вручную
    document.getElementById('addManualFieldsBtn').addEventListener('click', () => {
        const input = document.getElementById('manualFieldsInput');
        const fieldsText = input.value.trim();

        if (!fieldsText) {
            alert('Пожалуйста, введите поля');
            return;
        }

        // Разбиваем по запятой и очищаем от пробелов
        const newFields = fieldsText.split(',').map(f => f.trim()).filter(f => f.length > 0);

        // Добавляем новые поля к существующим
        newFields.forEach(field => {
            if (!pdfFields.includes(field)) {
                pdfFields.push(field);
            }
        });

        // Скрываем контейнер ручного ввода
        document.getElementById('manualFieldsContainer').style.display = 'none';

        // Перестраиваем интерфейс
        buildMappingInterface();

        // Обновляем статус
        document.getElementById('uploadStatus').textContent = `Успешно! Найдено ${csvHeaders.length} заголовков в CSV и ${pdfFields.length} полей в PDF`;
        document.getElementById('uploadStatus').className = 'status success show';
    });

    // Сохранение сопоставления
    document.getElementById('saveMappingBtn').addEventListener('click', async () => {
        const mapping = {};
        pdfFields.forEach(pdfField => {
            const select = document.getElementById(`mapping_${pdfField}`);
            if (select.value) {
                mapping[pdfField] = select.value;
            }
        });

        if (Object.keys(mapping).length === 0) {
            document.getElementById('mappingStatus').textContent = 'Пожалуйста, выберите хотя бы одно поле для сопоставления';
            document.getElementById('mappingStatus').className = 'status error show';
            return;
        }

        try {
            const response = await fetch('/map-fields', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ field_mapping: mapping })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('mappingStatus').textContent = 'Сопоставление сохранено! Теперь настройте параметры экспорта';
                document.getElementById('mappingStatus').className = 'status success show';

                // Показываем шаг 3
                document.getElementById('step3').classList.remove('hidden');
            } else {
                document.getElementById('mappingStatus').textContent = data.message || 'Ошибка при сохранении';
                document.getElementById('mappingStatus').className = 'status error show';
            }
        } catch (error) {
            document.getElementById('mappingStatus').textContent = 'Ошибка: ' + error.message;
            document.getElementById('mappingStatus').className = 'status error show';
        }
    });

    // Сохранение настроек экспорта
    document.getElementById('saveExportSettingsBtn').addEventListener('click', async () => {
        const startRow = parseInt(document.getElementById('start_row').value) || 1;
        const rowCount = parseInt(document.getElementById('row_count').value) || 250;

        if (startRow < 1) {
            document.getElementById('exportSettingsStatus').textContent = 'Начальная строка должна быть не менее 1';
            document.getElementById('exportSettingsStatus').className = 'status error show';
            return;
        }

        if (rowCount < 1 || rowCount > 500) {
            document.getElementById('exportSettingsStatus').textContent = 'Количество строк должно быть от 1 до 500';
            document.getElementById('exportSettingsStatus').className = 'status error show';
            return;
        }

        if (startRow > totalCsvRows) {
            document.getElementById('exportSettingsStatus').textContent = `Начальная строка не может быть больше общего количества строк (${totalCsvRows})`;
            document.getElementById('exportSettingsStatus').className = 'status error show';
            return;
        }

        try {
            const response = await fetch('/save-export-settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    start_row: startRow,
                    row_count: rowCount
                })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('exportSettingsStatus').textContent = 'Настройки экспорта сохранены! Теперь можно генерировать PDF';
                document.getElementById('exportSettingsStatus').className = 'status success show';

                // Обновляем финальную информацию
                document.getElementById('finalStartRow').textContent = startRow;
                document.getElementById('finalRowCount').textContent = rowCount;
                document.getElementById('estimatedPages').textContent = rowCount;

                // Показываем шаг 4
                document.getElementById('step4').classList.remove('hidden');
                document.getElementById('generateBtn').disabled = false;
            } else {
                document.getElementById('exportSettingsStatus').textContent = data.message || 'Ошибка при сохранении настроек';
                document.getElementById('exportSettingsStatus').className = 'status error show';
            }
        } catch (error) {
            document.getElementById('exportSettingsStatus').textContent = 'Ошибка: ' + error.message;
            document.getElementById('exportSettingsStatus').className = 'status error show';
        }
    });

    // Генерация PDF
    document.getElementById('generateBtn').addEventListener('click', async () => {
        const loading = document.getElementById('generateLoading');
        const status = document.getElementById('generateStatus');

        loading.classList.add('show');
        status.classList.remove('show');

        try {
            const response = await fetch('/generate-pdf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();

            if (data.success) {
                status.innerHTML = `PDF успешно сгенерирован! <a href="/download-pdf/${data.filename}" style="color: #155724; text-decoration: underline;">Скачать файл</a>`;
                status.className = 'status success show';
            } else {
                status.textContent = data.message || 'Ошибка при генерации PDF';
                status.className = 'status error show';
            }
        } catch (error) {
            status.textContent = 'Ошибка: ' + error.message;
            status.className = 'status error show';
        } finally {
            loading.classList.remove('show');
        }
    });
</script>
</body>
</html>
<?php /**PATH /Users/max_pups/Desktop/pht_backup_20251120_1/resources/views/index.blade.php ENDPATH**/ ?>