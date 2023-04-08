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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Абонементы читателей</a> > <a>Добавление записи об абонементе</a></p>";
            if(isset($_POST['submit']))
            {
                $readersname = $_POST['readers_name'];
                $queryforselectr = mysqli_fetch_assoc(mysqli_query($link, "SELECT readers_id FROM Readers WHERE readers_name = '".$readersname."' LIMIT 1"));
                $insertvaluer = $queryforselectr['readers_id'];
                $subscriptionsrg = date("Y-m-d");
                $booksname = $_POST['books_name'];
                $queryforselectb = mysqli_fetch_assoc(mysqli_query($link, "SELECT books_ISBN FROM Books WHERE books_name = '".$booksname."' LIMIT 1"));
                $insertvalueb = $queryforselectb['books_ISBN'];
                $subscriptionsed = date("Y-m-d");
                $subscriptionsrd = date("Y-m-d", strtotime("+13 days"));
                $workersname = $_POST['workers_name'];
                $queryforselectw = mysqli_fetch_assoc(mysqli_query($link, "SELECT workers_id FROM Workers WHERE workers_name = '".$workersname."' LIMIT 1"));
                $insertvaluew = $queryforselectw['workers_id'];
                $queryforcheck = mysqli_query($link, "SELECT readers_id, subscriptions_registration_date, books_ISBN, subscriptions_extradition_date, subscriptions_return_date, workers_id FROM Subscriptions");
                $countrowforcheck = mysqli_num_rows($queryforcheck);
                $flag = false;
                for($i = 0; $i < $countrowforcheck; $i++)
                {
                    $amforcheck = mysqli_fetch_assoc($queryforcheck);
                    if($amforcheck['readers_id'] === $insertvaluer && $amforcheck['subscriptions_registration_date'] === $subscriptionsrg && $amforcheck['books_ISBN'] === $insertvalueb && $amforcheck['subscriptions_extradition_date'] === $subscriptionsed && $amforcheck['subscriptions_return_date'] === $subscriptionsrd && $amforcheck['workers_id'] === $insertvaluew)
                    {
                        echo "Абонемент с такими данными уже существует!";
                        break;
                    }
                    if($i == ($countrowforcheck - 1))
                    {
                        $addsubscription = mysqli_query($link, "INSERT INTO Subscriptions VALUES(NULL, ".$insertvaluer.", '".$subscriptionsrg."', '".$insertvalueb."', '".$subscriptionsed."', '".$subscriptionsrd."', ".$insertvaluew.", 0)"); 
                        $updationho = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 1 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = LAST_INSERT_ID())");
                        $flag = true;
                        break;
                    }
                }
                if($flag)
                {
                    header("Location: subscriptions.php");
                }
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Выберите читателя:</p><select name = 'readers_name' required>";
            $queryr = mysqli_query($link, "SELECT readers_name FROM Readers");
            $countrowr = mysqli_num_rows($queryr);
            for($i = 0; $i <= $countrowr; $i++)
            {
                if($i == 0)
                {
                    echo "<option style = 'display: none'></option>";
                }
                else
                {
                    $row = mysqli_fetch_row($queryr);
                    for($j = 0; $j < 1; $j++)
                    {
                        echo "<option>".$row[$j]."</option>";
                    }
                }
            }
            echo "</select>
            <p>Выберите книгу:</p><select name = 'books_name' required>";
            $queryb = mysqli_query($link, "SELECT books_name FROM Books WHERE books_is_handed_over = 0");
            $countrowb = mysqli_num_rows($queryb);
            for($i = 0; $i <= $countrowb; $i++)
            {
                if($i == 0)
                {
                    echo "<option style = 'display: none'></option>";
                }
                else
                {
                    $row = mysqli_fetch_row($queryb);
                    for($j = 0; $j < 1; $j++)
                    {
                        echo "<option>".$row[$j]."</option>";
                    }
                }
            }
            echo "</select>
            <p>Выберите работника:</p><select name = 'workers_name' required>";
            $queryw = mysqli_query($link, "SELECT workers_name FROM Workers WHERE workers_position = 'библиотекарь'");
            $countroww = mysqli_num_rows($queryw);
            for($i = 0; $i <= $countroww; $i++)
            {
                if($i == 0)
                {
                    echo "<option style = 'display: none'></option>";
                }
                else
                {
                    $row = mysqli_fetch_row($queryw);
                    for($j = 0; $j < 1; $j++)
                    {
                        echo "<option>".$row[$j]."</option>";
                    }
                }
            }
            echo "</select><br><br>
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