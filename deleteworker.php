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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'workers.php'>Работники</a> > <a>Удаление записи о работнике</a></p>";
            if(isset($_POST['submit']))
            {
                $workersid = $_POST['workers_id'];
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
                if(count($err) == 0)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    if($_POST['workers_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор работника</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT workers_id FROM Workers WHERE workers_id = ".$workersid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['workers_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор работника не соответствует ни одному из существующих!</p>";
                        }
                        else
                        {
                            $queryfordelete = mysqli_query($link, "DELETE FROM Workers WHERE workers_id = ".$workersid."");
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
                echo "<br>Введите идентификатор удаляемого работника<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'workers_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор удаляемого работника<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'workers_id'><br><br>
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