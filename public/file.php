<?php
require '../config/config.php';

$errors = [];
$messages = [];

// С помощью массива GET имя картинки передано в ссылке
$imageFileName = $_GET['name'];
$commentFilePath = ROOT_DIR . COMMENT_DIR . '/' . $imageFileName . '.txt';// формируем путь до картинки
$link = mysqli_connect(HOST, USER, PASSWORD, DB_NAME); 

if (isset($_COOKIE['id'])) {
    $query = mysqli_query($link, "SELECT user_login FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");

    $userdata = mysqli_fetch_assoc($query);
    $user_name = strval($userdata['user_login']);
}
else {
    $user_name = '';
    $errors[] = 'Для добавления комментария нужна авторизация';
}

// Если коммент был отправлен
if(!empty($_POST['comment'])) {
    // Убираем пробелы в комментариях
    $comment = trim($_POST['comment']);

    // Валидация коммента
    if($comment === '') {
        $errors[] = 'Введите текст комментария';
    }

    // Если нет ошибок записываем коммент (если массив с ошибками пуст)
    if(empty($errors)) {

        // Чистим текст, земеняем переносы строк на <br/>, дописываем дату
        $comment = strip_tags($comment); // защита от ссылок и тегов, ввод только текста
        // записывается как один комментарий, убираются переносы строк
        $comment = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$comment);
        //$comment = date('d.m.Y g:i') . ': ' . $comment;

        // Дописываем текст в файл (будет создан, если еще не существует)
        file_put_contents($commentFilePath,  $comment . "\n", FILE_APPEND);
        

        $sql = "INSERT INTO comments SET img_name='". $imageFileName."', user_name='".$user_name."', comment='".$comment. "', date=now()";
        mysqli_query($link, $sql);

        $messages[] = 'Пользователь добавил коментарий';
        
        header("Location: ". $_SERVER['REQUEST_URI']);
    }
    else {
        if (trim($user_name) === '')         {
            header("Location: ../authorization/login.php");
            exit();
        }
    }
}

// Получаем список комментов
$comments = file_exists($commentFilePath)
   ? file($commentFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    // $sql = "select from comments where img_name = '". $imageFileName. "' order by date";
    
    // $query = mysqli_query($link, $sql );

    // $comments = mysqli_fetch_assoc($query);
    

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Галерея изображений<?php echo $imageFileName; ?></title>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
</head>
<body>

<section class="section all_page">

    <div class="container pt-4 view">

        <h1 class="mb-4 open_img"><a href="<?php echo URL; ?>">Галерея изображений</a></h1>

        <!-- Вывод сообщений об успехе/ошибке -->
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endforeach; ?>

        <?php foreach ($messages as $message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endforeach; ?>

        <h2 class="mb-4">Файл <?php echo $imageFileName; ?></h2>

        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2">

                <img src="<?php echo URL . '/' . UPLOAD_DIR . '/' . $imageFileName ?>" class="img-thumbnail mb-4"
                     alt="<?php echo $imageFileName ?>">

                <h3>Комментарии</h3>
                <?php if(!empty($comments)): ?>
                    <?php foreach ($comments as $key => $comment): ?>
                        <p class="<?php echo (($key % 2) > 0) ? 'bg-light' : ''; ?>"><?php echo $comment; ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Пока ни одного коммантария.</p>
                <?php endif; ?>

                <!-- Форма добавления комментария -->
                <form method="post">
                    <div class="form-group">
                        <label for="comment">Ваш комментарий</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Отправить</button>

                </form>
            </div>

        </div><!-- /.row -->
    </div><!-- /.container -->


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
</body>
</html>
