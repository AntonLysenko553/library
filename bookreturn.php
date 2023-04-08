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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Абонементы читателей</a> > <a>Сдача книги</a></p>";
            if(isset($_POST['submit']))
            {
                $booksISBN = $_POST['books_ISBN'];
                $booksname = $_POST['books_name'];
                $queryforcheckishanded = mysqli_query($link, "SELECT books_ISBN, books_name FROM Books WHERE books_is_handed_over = 0");
                $countrowforcheck = mysqli_num_rows($queryforcheckishanded);
                $flag = false;
                for($i = 0; $i < $countrowforcheck; $i++)
                {
                    $amforcheck = mysqli_fetch_assoc($queryforcheckishanded);
                    if($booksISBN == $amforcheck['books_ISBN'] && $booksname != $amforcheck['books_name'])
                    {
                        echo "Выбранный ISBN-код не соответствует выбранному названию книги!";
                        break;
                    }
                    if($i == ($countrowforcheck - 1))
                    {
                        $updationho = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        $updationic = mysqli_query($link, "UPDATE Subscriptions SET is_closed = 1 WHERE books_ISBN = '".$booksISBN."'");
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
            <p>Выберите ISBN книги:</p><select name = 'books_ISBN' required>";
            $query = mysqli_query($link, "SELECT books_ISBN FROM Books WHERE books_is_handed_over = 1");
            $countrow = mysqli_num_rows($query);
            for($i = 0; $i <= $countrow; $i++)
            {
                if($i == 0)
                {
                    echo "<option style = 'display: none'></option>";
                }
                else
                {
                    $row = mysqli_fetch_row($query);
                    for($j = 0; $j < 1; $j++)
                    {
                        echo "<option>".$row[$j]."</option>";
                    }
                }
            }
            echo "</select>
            <p>Выберите название книги:</p><select name = 'books_name' required>";
            $query = mysqli_query($link, "SELECT books_name FROM Books WHERE books_is_handed_over = 1");
            $countrow = mysqli_num_rows($query);
            for($i = 0; $i <= $countrow; $i++)
            {
                if($i == 0)
                {
                    echo "<option style = 'display: none'></option>";
                }
                else
                {
                    $row = mysqli_fetch_row($query);
                    for($j = 0; $j < 1; $j++)
                    {
                        echo "<option>".$row[$j]."</option>";
                    }
                }
            }
            echo "</select><br><br>
            <input type = 'submit' name = 'submit' value = 'Сдать'>
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