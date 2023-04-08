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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'books.php'>Книги</a> > <a>Добавление записи о книге</a></p>";
            if(isset($_POST['submit']))
            {
                $booksISBN = $_POST['books_ISBN'];
                $booksname = $_POST['books_name'];
                $booksauthor = $_POST['books_author'];
                $bookspy = $_POST['books_publishing_year'];
                $providersname = $_POST['providers_name'];
                $queryforselectp = mysqli_fetch_assoc(mysqli_query($link, "SELECT providers_id FROM Providers WHERE providers_name = '".$providersname."' LIMIT 1"));
                $insertvaluep = $queryforselectp['providers_id'];
                $bookspa = $_POST['books_pages_amount'];
                $bookspo = $_POST['books_publishing_office'];
                $err = array();
                $permittedextensions = '/^(jpg|png)/';
                $fileextension = pathinfo($_FILES['books_photo']['name'], PATHINFO_EXTENSION);
                if(!preg_match("/^[0-9-]+$/", $_POST['books_ISBN']))
                {
                    $err = " ";
                    echo "ISBN-код книги может состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['books_ISBN'], 'UTF-8') != 17)
                {
                    $err = " ";
                    echo "ISBN-код книги может быть длиной только 17 символов!<br>";
                }
                if(!preg_match("/^[0-9a-zA-ZА-Яа-яЁё\"!:?() ]+$/u", $_POST['books_name']))
                {
                    $err = " ";
                    echo "Название книги может состоять только из букв английского и русского алфавитов, цифр и символов !?:\"()!<br>";
                }
                if(iconv_strlen($_POST['books_name'], 'UTF-8') < 2 or iconv_strlen($_POST['books_name'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Название книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['books_author']))
                {
                    $err = " ";
                    echo "Имя автора книги может состоять только из русских букв!<br>";
                }
                if(iconv_strlen($_POST['books_author'], 'UTF-8') < 2 or iconv_strlen($_POST['books_author'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Имя автора книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['books_publishing_year']))
                {
                    $err = " ";
                    echo "Год издания книги может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['books_publishing_year'], 'UTF-8') > 4)
                {
                    $err = " ";
                    echo "Год издания книги может быть длиной не более 4 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['books_pages_amount']))
                {
                    $err = " ";
                    echo "Количество страниц книги может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['books_pages_amount'], 'UTF-8') > 4)
                {
                    $err = " ";
                    echo "Количество страниц книги может быть длиной не более 4 символов!<br>";
                }
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё\" ]+$/u", $_POST['books_publishing_office']))
                {
                    $err = " ";
                    echo "Название издательства книги может состоять только из букв английского и русского алфавитов и знака \"!<br>";
                }
                if(iconv_strlen($_POST['books_publishing_office'], 'UTF-8') < 2 or iconv_strlen($_POST['books_publishing_office'], 'UTF-8') > 75)
                {
                    $err = " ";
                    echo "Название издательства книги может быть длиной от 2 до 75 символов!<br>";
                }
                if($_FILES['books_photo']['error'] > 0 || $_FILES['books_photo']['size'] > 1073741800)
                {
                    $err = " ";
                    echo "Максимальный размер загружаемого файла - 1 гигабайт!<br>";
                }
                if(!preg_match($permittedextensions, $fileextension))
                {
                    $err = " ";
                    echo "Могут быть загружены файлы только с расширениями \"jpg\" и \"png\"!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheck = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, providers_id, books_pages_amount, books_publishing_office, books_photo FROM Books");
                    $countrowforcheck = mysqli_num_rows($queryforcheck);
                    $flag = false;
                    for($i = 0; $i < $countrowforcheck; $i++)
                    {
                        $amforcheck = mysqli_fetch_assoc($queryforcheck);
                        if($amforcheck['books_ISBN'] === $booksISBN && $amforcheck['books_name'] === $booksname && $amforcheck['books_author'] === $booksauthor && $amforcheck['books_publishing_year'] === $bookspy && $amforcheck['providers_id'] === $insertvaluep && $amforcheck['books_pages_amount'] === $bookspa && $amforcheck['books_publishing_office'] === $bookspo && $amforcheck['books_photo'] === $file)
                        {
                            echo "Книга с такими данными уже существует!<br>";
                            break;
                        }
                        if($amforcheck['books_ISBN'] === $booksISBN)
                        {
                            echo "Книга с таким ISBN-кодом уже существует!<br>";
                            break;
                        }
                        if($i == ($countrowforcheck - 1))
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $addbook = mysqli_query($link, "INSERT INTO Books VALUES('".$booksISBN."', '".$booksname."', '".$booksauthor."', ".$bookspy.", ".$insertvaluep.", ".$bookspa.", '".$bookspo."', 0, '".$file."')");
                            $flag = true;
                            break;
                        }
                    }
                    if($flag)
                    {
                        header("Location: books.php");
                    }
                }
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Введите ISBN-код книги:</p><input type = 'text' name = 'books_ISBN' required>
            <p>Введите название книги:</p><input type = 'text' name = 'books_name' required>
            <p>Введите имя автора книги:</p><input type = 'text' name = 'books_author' required>
            <p>Введите год издания книги:</p><input type = 'text' name = 'books_publishing_year' required>
            <p>Выберите поставщика книги:</p><select name = 'providers_name' required>";
            $query = mysqli_query($link, "SELECT providers_name FROM Providers");
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
            <p>Введите количество страниц книги:</p><input type = 'text' name = 'books_pages_amount' required>
            <p>Введите название издательства книги:</p><input type = 'text' name = 'books_publishing_office' required>
            <p>Загрузите фото книги:</p>
            <input type = 'hidden' name = 'MAX_FILE_SIZE' value = '1073741800'>
            <input type = 'file' name = 'books_photo' required><br><br>
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