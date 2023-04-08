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
        if((($userdata['users_hash'] !== $_COOKIE['users_hash']) or ($userdata['users_id'] !== $_COOKIE['users_id']) or ($userdata['users_ip'] !== $_SERVER['REMOTE_ADDR']) and ($userdata['users_ip'] !== "0")) and (($amass['users_is_admin'] == 1)))
        {
            echo "<p><a href = 'index.html'>Главная</a> > Личный кабинет</p>";
            echo "<h1>Личный кабинет</h1>";
            echo "Приветствую вас, Админ! Без привязки к IP<br>";
            echo "<p><a href = 'logout.php'>Выйти</a></p>";
            echo "<a href = 'tables.php'>Перейти к таблицам</a>";
        }
        else if((($userdata['users_hash'] == $_COOKIE['users_hash']) or ($userdata['users_id'] == $_COOKIE['users_id']) or ($userdata['users_ip'] == $_SERVER['REMOTE_ADDR']) and ($userdata['users_ip'] !== "0")) and (($amass['users_is_admin'] == 1)))
        {
            echo "<p><a href = 'index.html'>Главная</a> > Личный кабинет</p>";
            echo "<h1>Личный кабинет</h1>";
            echo "Приветствую вас, Админ! Привязка к IP выполнена<br>";
            echo "<p><a href = 'logout.php'>Выйти</a></p>";
            echo "<a href = 'tables.php'>Перейти к таблицам</a>";
        }
        else if((($userdata['users_hash'] !== $_COOKIE['users_hash']) or ($userdata['users_id'] !== $_COOKIE['users_id']) or ($userdata['users_ip'] !== $_SERVER['REMOTE_ADDR']) and ($userdata['users_ip'] !== "0")) and (($amass['users_is_admin'] !== 1)))
        {
            echo "<p><a href = 'index.html'>Главная</a> > Личный кабинет</p>";
            echo "<h1>Личный кабинет</h1>";
            echo "Приветствую вас, ".$userdata['users_login']."! Без привязки к IP<br>";
            echo "<p><a href = 'logout.php'>Выйти</a></p>";
            echo "<a href = 'tables.php'>Перейти к таблицам</a>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > Личный кабинет</p>";
            echo "<h1>Личный кабинет</h1>";
            echo "Приветствую вас, ".$userdata['users_login']."! Привязка к IP выполнена<br>";
            echo "<p><a href = 'logout.php'>Выйти</a></p>";
            echo "<a href = 'tables.php'>Перейти к таблицам</a>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>