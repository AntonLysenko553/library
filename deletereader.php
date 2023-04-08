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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'readers.php'>Читатели</a> > <a>Удаление записи о читателе</a></p>";
            if(isset($_POST['submit']))
            {
                $readersid = $_POST['readers_id'];
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
                if(count($err) == 0)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    if($_POST['readers_id'] == "")
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
                        else
                        {
                            $queryfordelete = mysqli_query($link, "DELETE FROM Readers WHERE readers_id = ".$readersid."");
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
                echo "<br>Введите идентификатор удаляемого читателя<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'readers_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор удаляемого читателя<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'readers_id'><br><br>
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