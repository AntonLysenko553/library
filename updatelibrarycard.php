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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'librarycards.php'>Читательские билеты</a> > <a>Редактирование информации о читательском билете</a></p>";
            if(isset($_POST['submit']))
            {
                $librarycardsid = $_POST['library_cards_id'];
                $readersid = $_POST['readers_id'];
                $subscriptionsid = $_POST['subscriptions_id'];
                $queryforreader = mysqli_query($link, "SELECT readers_id FROM Readers");
                $countrowreader = mysqli_num_rows($queryforreader);
                $queryforsubscription = mysqli_query($link, "SELECT subscriptions_id FROM Subscriptions");
                $countrowsubscription = mysqli_num_rows($queryforsubscription);
                $err = array();
                $flagforheader;
                $flagforreader = false;
                $flagforsubscription = false;
                $flagforform = false;
                if(!preg_match("/^[0-9]+$/", $_POST['library_cards_id']) && $_POST['library_cards_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор читательского билета может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['library_cards_id'], 'UTF-8') > 11 && $_POST['library_cards_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор читательского билета может быть длиной не более 11 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['readers_id']) && $_POST['readers_id'] != "")
                {
                    $err = " ";
                    $flagforreader = true;
                    echo "Идентификатор читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['readers_id'], 'UTF-8') > 11 && $_POST['readers_id'] != "")
                {
                    $err = " ";
                    $flagforreader = true;
                    echo "Идентификатор читателя может быть длиной не более 11 символов!<br>";
                }
                for($i = 0; $i < $countrowreader; $i++)
                {
                    $amp = mysqli_fetch_row($queryforreader);
                    if($amp[0] == $_POST['readers_id'])
                    {
                        $flagforreader = true;
                        break;
                    }
                }
                if($flagforreader == false && $_POST['readers_id'] != "" && $_POST['library_cards_id'] != "")
                {
                    echo "Введённый идентификатор читателя не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(!preg_match("/^[0-9]+$/", $_POST['subscriptions_id']) && $_POST['subscriptions_id'] != "")
                {
                    $err = " ";
                    $flagforsubscription = true;
                    echo "Идентификатор абонемента читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['subscriptions_id'], 'UTF-8') > 11 && $_POST['subscriptions_id'] != "")
                {
                    $err = " ";
                    $flagforsubscription = true;
                    echo "Идентификатор абонемента читателя может быть длиной не более 11 символов!<br>";
                }
                for($i = 0; $i < $countrowsubscription; $i++)
                {
                    $amp = mysqli_fetch_row($queryforsubscription);
                    if($amp[0] == $_POST['subscriptions_id'])
                    {
                        $flagforsubscription = true;
                        break;
                    }
                }
                if($flagforsubscription == false && $_POST['subscriptions_id'] != "" && $_POST['library_cards_id'] != "")
                {
                    echo "Введённый идентификатор абонемента читателя не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(count($err) == 0 && $flagforform == false)
                {
                    $flagforheader = true;
                    $flagforcheck = true;
                    $flagfornull = true;
                    if($_POST['library_cards_id'] != "" || $_POST['readers_id'] != "" || $_POST['subscriptions_id'] != "")
                    {
                        $flagfornull = false;
                    }
                    if($_POST['library_cards_id'] == "0")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        $flagfornull = false;
                        echo "<p style = 'font-size: 20px'>Введённый идентификатор читательского билета не соответствует ни одному из существующих!</p>";
                    }
                    if(empty($librarycardsid) && empty($readersid) && empty($subscriptionsid) && $flagfornull)
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                    }
                    if(($_POST['readers_id'] != "" || $_POST['subscriptions_id'] != "") && $_POST['library_cards_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор читательского билета</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT library_cards_id FROM Librarycards WHERE library_cards_id = ".$librarycardsid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['library_cards_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор читательского билета не соответствует ни одному из существующих!</p>";
                        }
                        else if($_POST['readers_id'] == "" && $_POST['subscriptions_id'] == "" && $_POST['library_cards_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                        }
                        else
                        {
                            if(!empty($readersid) && empty($subscriptionsid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Librarycards SET readers_id = ".$readersid." WHERE library_cards_id = ".$librarycardsid."");
                            }

                            if(empty($readersid) && !empty($subscriptionsid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Librarycards SET subscriptions_id = ".$subscriptionsid." WHERE library_cards_id = ".$librarycardsid."");
                            }

                            if(!empty($readersid) && !empty($subscriptionsid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Librarycards SET readers_id = ".$readersid.", subscriptions_id = ".$subscriptionsid." WHERE library_cards_id = ".$librarycardsid."");
                            }
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: librarycards.php");
                }
            }
            if(count($err) > 0 || $flagforform)
            {
                echo "<br><br>Введите идентификатор читательского билета, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'library_cards_id'><br><br>
                <hr style = 'max-width: 610px; margin: 0px'>
                <p>Введите идентификатор читателя:</p><input type = 'text' name = 'readers_id'>
                <p>Введите идентификатор абонемента читателя:</p><input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор читательского билета, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'library_cards_id'><br><br>
                <hr style = 'max-width: 610px; margin: 0px'>
                <p>Введите идентификатор читателя:</p><input type = 'text' name = 'readers_id'>
                <p>Введите идентификатор абонемента читателя:</p><input type = 'text' name = 'subscriptions_id'><br><br>
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