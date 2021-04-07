<?php

require './config/config.php';

$errors = [];
$messages = [];

// Если файл был отправлен
if (!empty($_FILES)) {

    // Проходим в цикле по файлам
    for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

        $fileName = $_FILES['files']['name'][$i];

        // Проверяем размер
        if ($_FILES['files']['size'][$i] > UPLOAD_MAX_SIZE) {
            $errors[] = 'Недопустимый размер файла ' . $fileName;
            continue;
        }

        // Проверяем формат
        if (!in_array($_FILES['files']['type'][$i], ALLOWED_TYPES)) {
            $errors[] = 'Недопустимый формат файла ' . $fileName;
            continue;
        }
        // Системный путь загрузки
        $filePath = UPLOAD_DIR . '/' . basename($fileName);

        // Пытаемся загрузить файл
        if (!move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
            $errors[] = 'Ошибка загрузки файла ' . $fileName;
            continue;
        }
    }
    // Если нет ошибок, то файлы загружены
    if (empty($errors)) {
        $messages[] = 'Файлы загружены';
    }

}

// Если файл был удален
// Если массив не пуст, происходит отправка форм и формируем путь до файлов
if (!empty($_POST['name'])) {

    $filePath = UPLOAD_DIR . '/' . $_POST['name'];
    $commentPath = COMMENT_DIR . '/' . $_POST['name'] . '.txt';

    // Удаляем изображение
    unlink($filePath);

    // Удаляем файл комментариев, если он существует
    if(file_exists($commentPath)) {
        unlink($commentPath);
    }

    $messages[] = 'Файл удален';
}

// Получаем список файлов, исключаем системные
$files = scandir(UPLOAD_DIR);
$files = array_filter($files, function ($file) {
    return !in_array($file, ['.', '..', '.gitkeep']);
});

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Загрузка файлов в галерею</title>
    <link rel="stylesheet" href="public/css/style.css" type="text/css"/>
</head>

<body>
    <section class="section all_page">

        <section class="section header">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn log btn-info" type="button" onclick="location.href= './authorization/login.php'">Вход</button>
                <button class="btn registration btn-info" type="button" onclick="location.href= './authorization/register.php'">Регистрация</button>
            </div>
        </section>   

        <section class="section gallery">
            <div class="container pt-4 main">

                <h1 class="mb-4 text_h1"><a href="<?php echo URL; ?>">Галерея изображений</a></h1>


                <!-- Вывод сообщений об успехе/ошибке -->
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endforeach; ?>

                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endforeach; ?>


                <!-- Вывод изображений -->
                <div class="mb-4">
                    <?php if (!empty($files)): ?>
                        <div class="row">
                            <?php foreach ($files as $file): ?>

                                <div class="col-12 col-sm-3 mb-4">
                                    <form method="post">
                                        <input type="hidden" name="name" value="<?php echo $file; ?>">
                                        <button type="submit" class="close" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </form>
                                    <a href="<?php echo URL . '/public/file.php?name=' . $file; ?>" title="Просмотр полного изображения">
                                        <img src="<?php echo URL . '/' . UPLOAD_DIR . '/' . $file ?>" class="img-thumbnail"
                                            alt="<?php echo $file; ?>">
                                    </a>
                                </div>
            
                            <?php endforeach; ?>
                        </div><!-- /.row -->
                    <?php else: ?>
                        <div class="alert alert-secondary">Нет загруженных файлов</div>
                    <?php endif; ?>
                </div>


                <!-- Форма загрузки файлов -->
                <h3 class="mb-4">Вы можете загрузить сюда свои файлы &#11015;</h3>
                <form method="post" enctype="multipart/form-data">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="files[]" id="customFile" multiple required>
                        <label class="custom-file-label" for="customFile" data-browse="Выбрать">Выберите файл...</label>
                        <small class="form-text text-muted">
                            Максимальный размер файла: <?php echo UPLOAD_MAX_SIZE / 1000000; ?> Мб.
                            Допустимые форматы: <?php echo implode(', ', ALLOWED_TYPES) ?>.
                        </small>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </form>
            </div><!-- /.container -->
        </section>

        <section class="section footer">
            <div class="container-fluid info_footer">
                <p>(с) 2021. Все права защищены.</p>
            </div>
        </section>   

    </section>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>
<script>
    $(() => {
        bsCustomFileInput.init();
    });
</script>
</body>
</html>
