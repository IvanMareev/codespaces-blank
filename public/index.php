<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея изображений</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
</head>
<body>
    <div class="page-wrapper">
        <header class="page-header">
            <h1>Галерея изображений</h1>
            <div class="top-actions">
                <button id="sortAscBtn" onclick="sortImages('asc')" disabled class="btn btn-secondary">По возрастанию</button>
                <button id="sortDescBtn" onclick="sortImages('desc')" class="btn btn-secondary">По убыванию</button>
            </div>
        </header>

        <section class="card upload-form">
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="file" id="imageInput" name="image" accept="image/*" required>
                <button type="submit" class="btn">Загрузить</button>
            </form>
            <div id="progress">
                <progress value="0" max="100"></progress>
                <span id="progressText">0%</span>
            </div>
        </section>

        <section class="gallery" id="gallery"></section>

        <div class="pagination">
            <button id="prevBtn" class="btn btn-secondary" disabled>Назад</button>
            <span id="pageInfo">Страница 1</span>
            <button id="nextBtn" class="btn btn-secondary">Далее</button>
        </div>
    </div>

</body>
</html>