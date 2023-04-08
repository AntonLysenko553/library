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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'workers.php'>Работники библиотеки</a> > <a>Добавление записи о работнике</a></p>";
            if(isset($_POST['submit']))
            {
                $workersname = $_POST['workers_name'];
                $workersposition = $_POST['workers_position'];
                $workersphone = $_POST['workers_phone'];
                $err = array();
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['workers_name']))
                {
                    $err = " ";
                    echo "Имя работника может состоять только из русских букв!<br>";
                }
                if(iconv_strlen($_POST['workers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['workers_name'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Имя работника может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё ]+$/u", $_POST['workers_position']))
                {
                    $err = " ";
                    echo "Должность работника может состоять только из букв английского и русского алфавитов!<br>";
                }
                if(iconv_strlen($_POST['workers_position'], 'UTF-8') < 2 or iconv_strlen($_POST['workers_position'], 'UTF-8') > 25)
                {
                    $err = " ";
                    echo "Должность работника может быть длиной от 2 до 25 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['workers_phone']))
                {
                    $err = " ";
                    echo "Номер телефона работника может состоять только из цифр и знака \"+\"!<br>";
                }
                if(iconv_strlen($_POST['workers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['workers_phone'], 'UTF-8') > 25)
                {
                    $err = " ";
                    echo "Номер телефона работника может быть длиной от 4 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheck = mysqli_query($link, "SELECT workers_name, workers_position, workers_phone FROM Workers");
                    $countrowforcheck = mysqli_num_rows($queryforcheck);
                    $flag = false;
                    for($i = 0; $i < $countrowforcheck; $i++)
                    {
                        $amforcheck = mysqli_fetch_assoc($queryforcheck);
                        if($amforcheck['workers_name'] === $workersname && $amforcheck['workers_position'] === $workersposition && $amforcheck['workers_phone'] === $workersphone)
                        {
                            echo "Работник с такими данными уже существует!";
                            break;
                        }
                        if($i == ($countrowforcheck - 1))
                        {
                            $addworker = mysqli_query($link, "INSERT INTO Workers VALUES(NULL, '".$workersname."', '".$workersposition."', '".$workersphone."')");
                            $flag = true;
                            break;
                        }
                    }
                    if($flag)
                    {
                        header("Location: workers.php");
                    }
                }
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Введите имя работника:</p><input type = 'text' name = 'workers_name' required>
            <p>Введите должность работника:</p><input type = 'text' name = 'workers_position' required>
            <p>Введите номер телефона работника:</p><input type = 'text' name = 'workers_phone' required><br><br>
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