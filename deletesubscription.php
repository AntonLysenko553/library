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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Абонементы читателей</a> > <a>Удаление записи об абонементе</a></p>";
            if(isset($_POST['submit']))
            {
                $subscriptionsid = $_POST['subscriptions_id'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9]+$/", $_POST['subscriptions_id']) && $_POST['subscriptions_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор абонемента читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['subscriptions_id'], 'UTF-8') > 11 && $_POST['subscriptions_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор абонемента читателя может быть длиной не более 11 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    if($_POST['subscriptions_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор абонемента читателя</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT subscriptions_id FROM Subscriptions WHERE subscriptions_id = ".$subscriptionsid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['subscriptions_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор абонемента читателя не соответствует ни одному из существующих!</p>";
                        }
                        else
                        {
                            $queryforchangeiho = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                            $queryfordelete = mysqli_query($link, "DELETE FROM Subscriptions WHERE subscriptions_id = ".$subscriptionsid."");
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: subscriptions.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br>Введите идентификатор удаляемого абонемента читателя<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор удаляемого абонемента читателя<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'subscriptions_id'><br><br>
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