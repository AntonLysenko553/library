<?php
    header("Content-Type: text/html; charset = utf-8");
    require_once 'connection.php';
    $link = mysqli_connect($host, $user, $password, $db) or die("Ошибка ".mysqli_error($link));
    $queryisdeleted = mysqli_query($link, "SELECT *, INET_NTOA(users_ip) AS users_ip FROM Users WHERE users_id = '".intval($_COOKIE['users_id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($queryisdeleted);
    $countrowisdeleted = mysqli_num_rows($queryisdeleted);
    if(isset($_COOKIE['users_id']) and isset($_COOKIE['users_hash']) and ($countrowisdeleted >= 1))
    {
        $queryforadmin = mysqli_query($link, "SELECT users_is_admin FROM Users WHERE users_id = ".$userdata['users_id']."");
        $amass = mysqli_fetch_assoc($queryforadmin);
        if($amass['users_is_admin'] == 1)
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'readers.php'>Читатели</a> > <a>Добавление записи о читателе</a></p>";
            if(isset($_POST['submit']))
            {
                $readersname = $_POST['readers_name'];
                $readersps = $_POST['readers_passport_series'];
                $readerspn = $_POST['readers_passport_number'];
                $readersphone = $_POST['readers_phone'];
                $err = array();
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['readers_name']))
                {
                    $err = " ";
                    echo "Имя читателя может состоять только из русских букв!<br>";
                }
                if(iconv_strlen($_POST['readers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['readers_name'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Имя читателя может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['readers_passport_series']))
                {
                    $err = " ";
                    echo "Серия паспорта читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_passport_series'], 'UTF-8') != 4)
                {
                    $err = " ";
                    echo "Серия паспорта читателя может быть длиной только 4 символа!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['readers_passport_number']))
                {
                    $err = " ";
                    echo "Номер паспорта читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_passport_number'], 'UTF-8') != 6)
                {
                    $err = " ";
                    echo "Номер паспорта читателя может быть длиной только 6 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['readers_phone']))
                {
                    $err = " ";
                    echo "Номер телефона читателя может состоять только из цифр и знака \"+\"!<br>";
                }
                if(iconv_strlen($_POST['readers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['readers_phone'], 'UTF-8') > 25)
                {
                    $err = " ";
                    echo "Номер телефона читателя может быть длиной от 4 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheck = mysqli_query($link, "SELECT readers_name, readers_passport_series, readers_passport_number, readers_phone FROM Readers");
                    $countrowforcheck = mysqli_num_rows($queryforcheck);
                    $flag = false;
                    for($i = 0; $i < $countrowforcheck; $i++)
                    {
                        $amforcheck = mysqli_fetch_assoc($queryforcheck);
                        if($amforcheck['readers_name'] === $readersname && $amforcheck['readers_passport_series'] === $readersps && $amforcheck['readers_passport_number'] === $readerspn && $amforcheck['readers_phone'] === $readersphone)
                        {
                            echo "Читатель с такими данными уже существует!";
                            break;
                        }
                        if($amforcheck['readers_name'] === $readersname && $amforcheck['readers_phone'] === $readersphone)
                        {
                            $updationreader = mysqli_query($link, "UPDATE Readers SET readers_passport_series = '".$readersps."', readers_passport_number = '".$readerspn."' WHERE readers_name = '".$readersname."' AND readers_phone = '".$readersphone."' AND readers_passport_series IS NULL AND readers_passport_number IS NULL");
                            $flag = true;
                            break;
                        }
                        if($i == ($countrowforcheck - 1))
                        {
                            $addreader = mysqli_query($link, "INSERT INTO Readers VALUES(NULL, '".$readersname."', '".$readersps."', '".$readerspn."', '".$readersphone."')");
                            $flag = true;
                            break;
                        }
                    }
                    if($flag)
                    {
                        header("Location: readers.php");
                    }
                }
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Введите имя читателя:</p><input type = 'text' name = 'readers_name' required>
            <p>Введите серию паспорта читателя:</p><input type = 'text' name = 'readers_passport_series' required>
            <p>Введите номер паспорта читателя:</p><input type = 'text' name = 'readers_passport_number' required>
            <p>Введите номер телефона читателя:</p><input type = 'text' name = 'readers_phone' required><br><br>
            <input type = 'submit' name = 'submit' value = 'Добавить'>
            </form>";
        }
        else
        {
            echo "Вы не обладаете достаточными правами для доступа на эту страницу<br><br>";
            echo "<a href = 'index.html'>Вернуться на главную страницу</a>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>