<?php
    header("Content-Type: text/html; charset = utf-8");
    require_once 'connection.php';
    $link = mysqli_connect($host, $user, $password, $db) or die("Ошибка ".mysqli_error($link));
    $query = mysqli_query($link, "SELECT *, INET_NTOA(users_ip) AS users_ip FROM Users WHERE users_id = '".intval($_COOKIE['users_id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);
    $countrow = mysqli_num_rows($query);
    if(isset($_COOKIE['users_id']) and isset($_COOKIE['users_hash']) and ($countrow >= 1))
    {
        $queryforadmin = mysqli_query($link, "SELECT users_is_admin FROM Users WHERE users_id = ".$userdata['users_id']."");
        $amass = mysqli_fetch_assoc($queryforadmin);
        if($amass['users_is_admin'] == 1)
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a>Таблицы</a></p><br>";
            echo "<a href = 'books.php'><input type = 'button' value = 'Книги'></a><a> </a>";
            echo "<a href = 'readers.php'><input type = 'button' value = 'Читатели'></a><a> </a>";
            echo "<a href = 'librarycards.php'><input type = 'button' value = 'Читательские билеты'></a><a> </a>";
            echo "<a href = 'subscriptions.php'><input type = 'button' value = 'Абонементы читателей'></a><a> </a>";
            echo "<a href = 'workers.php'><input type = 'button' value = 'Работники библиотеки'></a><a> </a>";
            echo "<a href = 'providers.php'><input type = 'button' value = 'Поставщики книг'></a>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a>Таблицы</a></p><br>";
            echo "<a href = 'books.php'><input type = 'button' value = 'Книги'></a><a> </a>";
            echo "<a href = 'readers.php'><input type = 'button' value = 'Читатели'></a><a> </a>";
            echo "<a href = 'subscriptions.php'><input type = 'button' value = 'Мои абонементы'></a>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>