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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'workers.php'>Работники</a> > <a>Редактирование информации о работнике</a></p>";
            if(isset($_POST['submit']))
            {
                $workersid = $_POST['workers_id'];
                $workersname = $_POST['workers_name'];
                $workersposition = $_POST['workers_position'];
                $workersphone = $_POST['workers_phone'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9]+$/", $_POST['workers_id']) && $_POST['workers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор работника может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['workers_id'], 'UTF-8') > 11 && $_POST['workers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор работника может быть длиной не более 11 символов!<br>";
                }
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['workers_name']) && $_POST['workers_name'] != "")
                {
                    $err = " ";
                    echo "Имя работника может состоять только из букв русского алфавита!<br>";
                }
                if((iconv_strlen($_POST['workers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['workers_name'], 'UTF-8') > 75) && $_POST['workers_name'] != "")
                {
                    $err = " ";
                    echo "Имя работника может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё ]+$/u", $_POST['workers_position']) && $_POST['workers_position'] != "")
                {
                    $err = " ";
                    echo "Должность работника может состоять только из букв английского и русского алфавитов!<br>";
                }
                if((iconv_strlen($_POST['workers_position'], 'UTF-8') < 2 or iconv_strlen($_POST['workers_position'], 'UTF-8') > 25) && $_POST['workers_position'] != "")
                {
                    $err = " ";
                    echo "Должность работника может быть длиной от 2 до 25 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['workers_phone']) && $_POST['workers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона работника может состоять только из цифр и знака \"+\"!<br>";
                }
                if((iconv_strlen($_POST['workers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['workers_phone'], 'UTF-8') > 25) && $_POST['workers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона работника может быть длиной от 4 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheckid = mysqli_query($link, "SELECT workers_id FROM Workers WHERE workers_id = '".$workersid."'");
                    $countrowid = mysqli_num_rows($queryforcheckid);
                    $flagforheader = true;
                    if($countrowid != 1 && $_POST['workers_id'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Введённый идентификатор работника не соответствует ни одному из существующих!</p>";
                    }
                    else if(($_POST['workers_name'] != "" || $_POST['workers_position'] != "" || $_POST['workers_phone'] != "") && $_POST['workers_id'] == "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор работника</p>";
                    }
                    else if($_POST['workers_name'] == "" && $_POST['workers_position'] == "" && $_POST['workers_phone'] == "" && $_POST['workers_id'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                    }
                    else
                    {
                        if(empty($workersid) && empty($workersname) && empty($workersposition) && empty($workersphone))
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                        }

                        if(!empty($workersname) && empty($workersposition) && empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_name = '".$workersname."' WHERE workers_id = ".$workersid."");
                        }
                        if(empty($workersname) && !empty($workersposition) && empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_position = '".$workersposition."' WHERE workers_id = ".$workersid."");
                        }
                        if(empty($workersname) && empty($workersposition) && !empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_phone = '".$workersphone."' WHERE workers_id = ".$workersid."");
                        }

                        if(!empty($workersname) && !empty($workersposition) && empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_name = '".$workersname."', workers_position = '".$workersposition."' WHERE workers_id = ".$workersid."");
                        }
                        if(!empty($workersname) && empty($workersposition) && !empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_name = '".$workersname."', workers_phone = '".$workersphone."' WHERE workers_id = ".$workersid."");
                        }

                        if(empty($workersname) && !empty($workersposition) && !empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_position = '".$workersposition."', workers_phone = '".$workersphone."' WHERE workers_id = ".$workersid."");
                        }

                        if(!empty($workersname) && !empty($workersposition) && !empty($workersphone))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Workers SET workers_name = '".$workersname."', workers_position = '".$workersposition."', workers_phone = '".$workersphone."' WHERE workers_id = ".$workersid."");
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: workers.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br><br>Введите идентификатор работника, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'workers_id'><br><br>
                <hr style = 'max-width: 532px; margin: 0px'>
                <p>Введите имя работника:</p><input type = 'text' name = 'workers_name'>
                <p>Введите должность работника:</p><input type = 'text' name = 'workers_position'>
                <p>Введите номер телефона работника:</p><input type = 'text' name = 'workers_phone'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор работника, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'workers_id'><br><br>
                <hr style = 'max-width: 532px; margin: 0px'>
                <p>Введите имя работника:</p><input type = 'text' name = 'workers_name'>
                <p>Введите должность работника:</p><input type = 'text' name = 'workers_position'>
                <p>Введите номер телефона работника:</p><input type = 'text' name = 'workers_phone'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
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