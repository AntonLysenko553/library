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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'books.php'>Книги</a> > <a>Удаление записи о книге</a></p>";
            if(isset($_POST['submit']))
            {
                $booksISBN = $_POST['books_ISBN'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9-]+$/", $_POST['books_ISBN']) && $_POST['books_ISBN'] != "")
                {
                    $err = " ";
                    echo "ISBN-код книги может состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['books_ISBN'], 'UTF-8') != 17 && $_POST['books_ISBN'] != "")
                {
                    $err = " ";
                    echo "ISBN-код книги может быть длиной только 17 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheckISBN = mysqli_query($link, "SELECT books_ISBN FROM Books WHERE books_ISBN = '".$booksISBN."'");
                    $countrowISBN = mysqli_num_rows($queryforcheckISBN);
                    $flagforheader = true;
                    if($countrowISBN != 1 && $_POST['books_ISBN'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Введённый ISBN-код книги не соответствует ни одному из существующих!</p>";
                    }
                    else if($_POST['books_ISBN'] == "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели ISBN-код книги</p>";
                    }
                    else
                    {
                        $queryfordelete = mysqli_query($link, "DELETE FROM Books WHERE books_ISBN = '".$booksISBN."'");
                    }
                }
                if($flagforheader)
                {
                    header("Location: books.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br>Введите ISBN-код удаляемой книги<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'books_ISBN'><br><br>
                <input type = 'submit' name = 'submit' value = 'Удалить'>
                </form>";
            }
            else
            {
                echo "Введите ISBN-код удаляемой книги<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'books_ISBN'><br><br>
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