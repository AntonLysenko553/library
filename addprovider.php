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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'providers.php'>Поставщики книг</a> > <a>Добавление записи о поставщике</a></p>";
            if(isset($_POST['submit']))
            {
                $providersname = $_POST['providers_name'];
                $providersaddress = $_POST['providers_address'];
                $providersphone = $_POST['providers_phone'];
                $providersemail = $_POST['providers_email'];
                $err = array();
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё\" ]+$/u", $_POST['providers_name']))
                {
                    $err = " ";
                    echo "Название поставщика может состоять только из букв английского и русского алфавитов и знака \"!<br>";
                }
                if(iconv_strlen($_POST['providers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['providers_name'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Название поставщика может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[-А-Яа-яЁё0-9., ]+$/u", $_POST['providers_address']))
                {
                    $err = " ";
                    echo "Адрес поставщика может состоять только из букв русского алфавита, цифр, точек, запятых и дефисов!<br>";
                }
                if(iconv_strlen($_POST['providers_address'], 'UTF-8') < 2 or iconv_strlen($_POST['providers_address'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Адрес поставщика может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['providers_phone']))
                {
                    $err = " ";
                    echo "Номер телефона поставщика может состоять только из цифр и знака \"+\"!<br>";
                }
                if(iconv_strlen($_POST['providers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['providers_phone'], 'UTF-8') > 25)
                {
                    $err = " ";
                    echo "Номер телефона поставщика может быть длиной от 4 до 25 символов!<br>";
                }
                if(!preg_match("/^[a-zA-Z0-9@. ]+$/", $_POST['providers_email']))
                {
                    $err = " ";
                    echo "Email поставщика может состоять только из букв английского алфавита, цифр, точек и знака \"@\"!<br>";
                }
                if(iconv_strlen($_POST['providers_email'], 'UTF-8') < 6 or iconv_strlen($_POST['providers_email'], 'UTF-8') > 25)
                {
                    $err = " ";
                    echo "Email поставщика может быть длиной от 6 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheck = mysqli_query($link, "SELECT providers_name, providers_address, providers_phone, providers_email FROM Providers");
                    $countrowforcheck = mysqli_num_rows($queryforcheck);
                    $flag = false;
                    for($i = 0; $i < $countrowforcheck; $i++)
                    {
                        $amforcheck = mysqli_fetch_assoc($queryforcheck);
                        if($amforcheck['providers_name'] === $providersname && $amforcheck['providers_address'] === $providersaddress && $amforcheck['providers_phone'] === $providersphone && $amforcheck['providers_email'] === $providersemail)
                        {
                            echo "Поставщик с такими данными уже существует!";
                            break;
                        }
                        if($i == ($countrowforcheck - 1))
                        {
                            $addprovider = mysqli_query($link, "INSERT INTO Providers VALUES(NULL, '".$providersname."', '".$providersaddress."', '".$providersphone."', '".$providersemail."')");
                            $flag = true;
                            break;
                        }
                    }
                    if($flag)
                    {
                        header("Location: providers.php");
                    }
                }
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Введите название поставщика:</p><input type = 'text' name = 'providers_name' required>
            <p>Введите адрес поставщика:</p><input type = 'text' name = 'providers_address' required>
            <p>Введите номер телефона поставщика:</p><input type = 'text' name = 'providers_phone' required>
            <p>Введите email поставщика:</p><input type = 'text' name = 'providers_email' required><br><br>
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