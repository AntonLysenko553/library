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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'providers.php'>Поставщики</a> > <a>Удаление записи о поставщике</a></p>";
            if(isset($_POST['submit']))
            {
                $providersid = $_POST['providers_id'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9]+$/", $_POST['providers_id']) && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор поставщика может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['providers_id'], 'UTF-8') > 11 && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор поставщика может быть длиной не более 11 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    if($_POST['providers_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор поставщика</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT providers_id FROM Providers WHERE providers_id = ".$providersid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['providers_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор поставщика не соответствует ни одному из существующих!</p>";
                        }
                        else
                        {
                            $queryfordelete = mysqli_query($link, "DELETE FROM Providers WHERE providers_id = ".$providersid."");
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: providers.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br>Введите идентификатор удаляемого поставщика<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'providers_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор удаляемого поставщика<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'providers_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
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