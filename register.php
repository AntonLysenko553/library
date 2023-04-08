<?php
    header("Content-Type: text/html; charset = utf-8");
    require_once 'connection.php';
    function generateCode($lenght = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while(strlen($code) < $lenght)
        {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }
    $link = mysqli_connect($host, $user, $password, $db) or die("Ошибка ".mysqli_error($link));
    echo "<p><a href = 'index.html'>Главная</a> > Регистрация</p>";
    if(isset($_POST['submit']))
    {
        $userslogin = $_POST['users_login'];
        $userspassword = $_POST['users_password'];
        $readersname = $_POST['readers_name'];
        $readersphone = $_POST['readers_phone'];
        $err = array();
        if(!preg_match("/^[a-zA-Z0-9_]+$/", $_POST['users_login']))
        {
            $err = " ";
            echo "Логин пользователя может состоять только из букв английского алфавита, цифр и знака \"_\"!<br>";
        }
        if(iconv_strlen($_POST['users_login'], 'UTF-8') < 2 or iconv_strlen($_POST['users_login'], 'UTF-8') > 15)
        {
            $err = " ";
            echo "Логин пользователя может быть длиной от 2 до 15 символов!<br>";
        }
        if(iconv_strlen($_POST['users_password'], 'UTF-8') < 2 or iconv_strlen($_POST['users_password'], 'UTF-8') > 15)
        {
            $err = " ";
            echo "Пароль пользователя может быть длиной от 2 до 15 символов!<br>";
        }
        if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['readers_name']))
        {
            $err = " ";
            echo "Имя пользователя может состоять только из русских букв!<br>";
        }
        if(iconv_strlen($_POST['readers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['readers_name'], 'UTF-8') > 75)
        {
            $err = " ";
            echo "Имя пользователя может быть длиной от 2 до 75 символов!<br>";
        }
        if(!preg_match("/^[0-9+]+$/", $_POST['readers_phone']))
        {
            $err = " ";
            echo "Номер телефона пользователя может состоять только из цифр и знака \"+\"!<br>";
        }
        if(iconv_strlen($_POST['readers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['readers_phone'], 'UTF-8') > 25)
        {
            $err = " ";
            echo "Номер телефона пользователя может быть длиной от 4 до 25 символов!<br>";
        }
        $queryforselect = mysqli_query($link, "SELECT Readers.readers_name, Readers.readers_phone, Users.users_login FROM Readers, Users WHERE Users.readers_id = Readers.readers_id");
        $countrow = mysqli_num_rows($queryforselect);
        $flaglnp = false;
        for($i = 0; $i < $countrow; $i++)
        {
            $am = mysqli_fetch_assoc($queryforselect);
            if($am['readers_name'] === $readersname && $am['readers_phone'] === $readersphone && $am['users_login'] === $userslogin)
            {
                $err = " ";
                echo "Пользователь с такими логином, ФИО и телефоном уже существует!<br>";
                $flaglnp = true;
                break;
            }
        }
        if(count($err) == 0)
        {
            $userspassword = md5(md5(trim($_POST['users_password'])));
            $usershash = md5(generateCode(10));
            if(empty($_POST['not_attach_ip']))
            {
                $usersip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $usersip = "0";
            }
            $queryforr = mysqli_query($link, "SELECT Readers.readers_name, Readers.readers_phone, Users.users_login FROM Readers, Users WHERE Users.readers_id = Readers.readers_id");
            $countrowr = mysqli_num_rows($queryforr);
            $flagr = true;
            $queryne = mysqli_query($link, "SELECT Readers.readers_name, Readers.readers_phone FROM Readers WHERE NOT EXISTS (SELECT Users.users_id FROM Users WHERE Users.readers_id = Readers.readers_id)");
            $countrowne = mysqli_num_rows($queryne);
            $flagne = true;
            for($i = 0; $i < $countrowr; $i++)
            {
                $amr = mysqli_fetch_assoc($queryforr);
                if($amr['readers_name'] === $readersname && $amr['readers_phone'] === $readersphone && $amr['users_login'] != $userslogin)
                {
                    $queryforid = mysqli_query($link, "SELECT readers_id FROM Readers WHERE readers_name = '".$readersname."'");
                    $ami = mysqli_fetch_assoc($queryforid);
                    $insert = mysqli_query($link, "INSERT INTO Users VALUES(NULL, '".$userslogin."', '".$userspassword."', '".$readersphone."', ".$ami['readers_id'].", INET_ATON('".$usersip."'), '".$usershash."', 0)");
                    $querynp = mysqli_query($link, "SELECT users_id, users_password FROM Users WHERE users_login = '".mysqli_real_escape_string($link, $_POST['users_login'])."' LIMIT 1");
                    $datanp = mysqli_fetch_assoc($querynp);
                    setcookie("users_id", $datanp['users_id'], time() + 60 * 60 * 24 * 30, "/");
                    setcookie("users_hash", $usershash, time() + 60 * 60 * 24 * 30, "/", null, null, true);
                    $flagr = false;
                    break;
                }
            }
            for($i = 0; $i < $countrowne; $i++)
            {
                $amne = mysqli_fetch_assoc($queryne);
                if($amne['readers_name'] === $readersname && $amne['readers_phone'] === $readersphone && !$flaglnp)
                {
                    $queryforid = mysqli_query($link, "SELECT readers_id FROM Readers WHERE readers_name = '".$readersname."'");
                    $ami = mysqli_fetch_assoc($queryforid);
                    $insert = mysqli_query($link, "INSERT INTO Users VALUES(NULL, '".$userslogin."', '".$userspassword."', '".$readersphone."', ".$ami['readers_id'].", INET_ATON('".$usersip."'), '".$usershash."', 0)");
                    $querynp = mysqli_query($link, "SELECT users_id, users_password FROM Users WHERE users_login = '".mysqli_real_escape_string($link, $_POST['users_login'])."' LIMIT 1");
                    $datanp = mysqli_fetch_assoc($querynp);
                    setcookie("users_id", $datanp['users_id'], time() + 60 * 60 * 24 * 30, "/");
                    setcookie("users_hash", $usershash, time() + 60 * 60 * 24 * 30, "/", null, null, true);
                    $flagne = false;
                    break;
                }
            }
            if($flagr && $flagne)
            {
                $insertforreaders = mysqli_query($link, "INSERT INTO Readers VALUES(NULL, '".$readersname."', NULL, NULL, '".$readersphone."')");
                $insertforusers = mysqli_query($link, "INSERT INTO Users VALUES(NULL, '".$userslogin."', '".$userspassword."', '".$readersphone."', LAST_INSERT_ID(), INET_ATON('".$usersip."'), '".$usershash."', 0)");
                $query = mysqli_query($link, "SELECT users_id, users_password FROM Users WHERE users_login = '".mysqli_real_escape_string($link, $_POST['users_login'])."' LIMIT 1");
                $data = mysqli_fetch_assoc($query);
                setcookie("users_id", $data['users_id'], time() + 60 * 60 * 24 * 30, "/");
                setcookie("users_hash", $usershash, time() + 60 * 60 * 24 * 30, "/", null, null, true);
            }
            header("Location: cabinet.php");
        }
        else
        {
            echo "<p>При регистрации произошла ошибка</p>";
        }
    }
    echo "<form method = 'POST' enctype = 'multipart/form-data'>
    <p>Логин: <input name = 'users_login' type = 'text' required></p>
    <p>Пароль: <input name = 'users_password' type = 'password' required></p>
    <p>ФИО: <input name = 'readers_name' type = 'text' required></p>
    <p>Номер телефона: <input name = 'readers_phone' type = 'text' required></p>
    <p>Не прикреплять к IP (небезопасно)<input name = 'not_attach_ip' type = 'checkbox'></p>
    <p><input name = 'submit' type = 'submit' value = 'Зарегистриоваться'></p>
    </form>";
?>