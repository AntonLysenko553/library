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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'readers.php'>Читатели</a> > <a>Редактирование информации о читателе</a></p>";
            if(isset($_POST['submit']))
            {
                $readersid = $_POST['readers_id'];
                $readersname = $_POST['readers_name'];
                $readersps = $_POST['readers_passport_series'];
                $readerspn = $_POST['readers_passport_number'];
                $readersphone = $_POST['readers_phone'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9]+$/", $_POST['readers_id']) && $_POST['readers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_id'], 'UTF-8') > 11 && $_POST['readers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор читателя может быть длиной не более 11 символов!<br>";
                }
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['readers_name']) && $_POST['readers_name'] != "")
                {
                    $err = " ";
                    echo "Имя читателя может состоять только из букв русского алфавита!<br>";
                }
                if((iconv_strlen($_POST['readers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['readers_name'], 'UTF-8') > 75) && $_POST['readers_name'] != "")
                {
                    $err = " ";
                    echo "Имя читателя может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['readers_passport_series']) && $_POST['readers_passport_series'] != "")
                {
                    $err = " ";
                    echo "Серия паспорта читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_passport_series'], 'UTF-8') != 4 && $_POST['readers_passport_series'] != "")
                {
                    $err = " ";
                    echo "Серия паспорта читателя может быть длиной только 4 символа!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['readers_passport_number']) && $_POST['readers_passport_number'] != "")
                {
                    $err = " ";
                    echo "Номер паспорта читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_passport_number'], 'UTF-8') != 6 && $_POST['readers_passport_number'] != "")
                {
                    $err = " ";
                    echo "Номер паспорта читателя может быть длиной только 6 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['readers_phone']) && $_POST['readers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона читателя может состоять только из цифр и знака \"+\"!<br>";
                }
                if((iconv_strlen($_POST['readers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['readers_phone'], 'UTF-8') > 25) && $_POST['readers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона читателя может быть длиной от 4 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    if(empty($readersid) && empty($readersname) && empty($readersps) && empty($readerspn) && empty($readersphone) && $_POST['readers_id'] != "0")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                    }
                    if(($_POST['readers_name'] != "" || $_POST['readers_passport_series'] != "" || $_POST['readers_passport_number'] != "" || $_POST['readers_phone'] != "") && $_POST['readers_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор читателя</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT readers_id FROM Readers WHERE readers_id = ".$readersid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['readers_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор читателя не соответствует ни одному из существующих!</p>";
                        }
                        else if($_POST['readers_name'] == "" && $_POST['readers_passport_series'] == "" && $_POST['readers_passport_number'] == "" && $_POST['readers_phone'] == "" && $_POST['readers_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                        }
                        else
                        {
                            if(!empty($readersname) && empty($readersps) && empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."' WHERE readers_id = ".$readersid."");
                            }
                            if(empty($readersname) && !empty($readersps) && empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_series = '".$readersps."' WHERE readers_id = ".$readersid."");
                            }
                            if(empty($readersname) && empty($readersps) && !empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_number = '".$readerspn."' WHERE readers_id = ".$readersid."");
                            }
                            if(empty($readersname) && empty($readersps) && empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(!empty($readersname) && !empty($readersps) && empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_series = '".$readersps."' WHERE readers_id = ".$readersid."");
                            }
                            if(!empty($readersname) && empty($readersps) && !empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_number = '".$readerspn."' WHERE readers_id = ".$readersid."");
                            }
                            if(!empty($readersname) && empty($readersps) && empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(empty($readersname) && !empty($readersps) && !empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_series = '".$readersps."', readers_passport_number = '".$readerspn."' WHERE readers_id = ".$readersid."");
                            }
                            if(empty($readersname) && !empty($readersps) && empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_series = '".$readersps."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }
                            
                            if(empty($readersname) && empty($readersps) && !empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_number = '".$readerspn."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(!empty($readersname) && !empty($readersps) && !empty($readerspn) && empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_series = '".$readersps."', readers_passport_number = '".$readerspn."' WHERE readers_id = ".$readersid."");
                            }
                            if(!empty($readersname) && !empty($readersps) && empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_series = '".$readersps."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(!empty($readersname) && empty($readersps) && !empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_number = '".$readerspn."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(empty($readersname) && !empty($readersps) && !empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_passport_series = '".$readersps."', readers_passport_number = '".$readerspn."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }

                            if(!empty($readersname) && !empty($readersps) && !empty($readerspn) && !empty($readersphone))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Readers SET readers_name = '".$readersname."', readers_passport_series = '".$readersps."', readers_passport_number = '".$readerspn."', readers_phone = '".$readersphone."' WHERE readers_id = ".$readersid."");
                            }
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: readers.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br><br>Введите идентификатор читателя, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'readers_id'><br><br>
                <hr style = 'max-width: 522px; margin: 0px'>
                <p>Введите имя читателя:</p><input type = 'text' name = 'readers_name'>
                <p>Введите серию паспорта читателя:</p><input type = 'text' name = 'readers_passport_series'>
                <p>Введите номер паспорта читателя:</p><input type = 'text' name = 'readers_passport_number'>
                <p>Введите номер телефона читателя:</p><input type = 'text' name = 'readers_phone'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор читателя, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'readers_id'><br><br>
                <hr style = 'max-width: 522px; margin: 0px'>
                <p>Введите имя читателя:</p><input type = 'text' name = 'readers_name'>
                <p>Введите серию паспорта читателя:</p><input type = 'text' name = 'readers_passport_series'>
                <p>Введите номер паспорта читателя:</p><input type = 'text' name = 'readers_passport_number'>
                <p>Введите номер телефона читателя:</p><input type = 'text' name = 'readers_phone'><br><br>
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