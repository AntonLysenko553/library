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
    echo "<p><a href = 'index.html'>Главная</a> > Вход</p>";
    if(isset($_POST['submit']))
    {
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
        if(count($err) == 0)
        {
            $query = mysqli_query($link, "SELECT users_id, users_password FROM Users WHERE users_login = '".mysqli_real_escape_string($link, $_POST['users_login'])."' LIMIT 1");
            $countrow = mysqli_num_rows($query);
            $data = mysqli_fetch_assoc($query);
            if($countrow < 1)
            {
                echo "Пользователя с таким логином не существует!<br><p>При входе произошла ошибка</p>";
            }
            else
            {
                if($data['users_password'] === md5(md5(trim($_POST['users_password']))))
                {
                    $usershash = md5(generateCode(10));
                    if(empty($_POST['not_attach_ip']))
                    {
                        $usersip = $_SERVER['REMOTE_ADDR'];
                    }
                    else
                    {
                        $usersip = "0";
                    }
                    $updationhash = mysqli_query($link, "UPDATE Users SET users_hash = '".$usershash."', users_ip = INET_ATON('".$usersip."') WHERE users_id = '".$data['users_id']."'");
                    setcookie("users_id", $data['users_id'], time() + 60 * 60 * 24 * 30, "/");
                    setcookie("users_hash", $usershash, time() + 60 * 60 * 24 * 30, "/", null, null, true);
                    header("Location: cabinet.php");
                }
                else
                {
                    echo "Введён неправильно пароль пользователя<br><br>";
                }
            }
        }
        else
        {
            echo "<p>При входе произошла ошибка</p>";
        }
    }
    echo "<form method = 'POST' enctype = 'multipart/form-data'
    <p>Логин: <input name = 'users_login' type = 'text' required></p>
    <p>Пароль: <input name = 'users_password' type = 'password' required></p>
    <p>Не прикреплять к IP (небезопасно)<input name = 'not_attach_ip' type = 'checkbox'></p>
    <p><a href = 'phonelog.php'>Войти по номеру телефона</p>
    <p><input name = 'submit' type = 'submit' value = 'Войти'></p>
    </form>";
?>