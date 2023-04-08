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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'books.php'>Книги</a> > <a>Редактирование информации о книге</a></p>";
            if(isset($_POST['submit']))
            {
                $booksISBN = $_POST['books_ISBN'];
                $booksname = $_POST['books_name'];
                $booksauthor = $_POST['books_author'];
                $bookspy = $_POST['books_publishing_year'];
                $providersid = $_POST['providers_id'];
                $bookspa = $_POST['books_pages_amount'];
                $bookspo = $_POST['books_publishing_office'];
                $booksiho = $_POST['books_is_handed_over'];
                $queryforprovider = mysqli_query($link, "SELECT providers_id FROM Providers");
                $countrowprovider = mysqli_num_rows($queryforprovider);
                $err = array();
                $flagforheader;
                $flagforprovider = false;
                $flagforform = false;
                $permittedextensions = "/^(jpg|png)/";
                $fileextension = pathinfo($_FILES['books_photo']['name'], PATHINFO_EXTENSION);
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
                if(!preg_match("/^[0-9a-zA-ZА-Яа-яЁё\"!:?() ]+$/u", $_POST['books_name']) && $_POST['books_name'] != "")
                {
                    $err = " ";
                    echo "Название книги может состоять только из букв английского и русского алфавитов, цифр и символов !?:\"()!<br>";
                }
                if((iconv_strlen($_POST['books_name'], 'UTF-8') < 2 or iconv_strlen($_POST['books_name'], 'UTF-8') > 75) && $_POST['books_name'] != "")
                {
                    $err = " ";
                    echo "Название книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_POST['books_author']) && $_POST['books_author'] != "")
                {
                    $err = " ";
                    echo "Имя автора книги может состоять только из русских букв!<br>";
                }
                if((iconv_strlen($_POST['books_author'], 'UTF-8') < 2 or iconv_strlen($_POST['books_author'], 'UTF-8') > 75) && $_POST['books_author'] != "")
                {
                    $err = " ";
                    echo "Имя автора книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['books_publishing_year']) && $_POST['books_publishing_year'] != "")
                {
                    $err = " ";
                    echo "Год издания книги может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['books_publishing_year'], 'UTF-8') > 4 && $_POST['books_publishing_year'] != "")
                {
                    $err = " ";
                    echo "Год издания книги может быть длиной не более 4 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['providers_id']) && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    $flagforprovider = true;
                    echo "Идентификатор поставщика книги может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['providers_id'], 'UTF-8') > 11 && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    $flagforprovider = true;
                    echo "Идентификатор поставщика книги может быть длиной не более 11 символов!<br>";
                }
                for($i = 0; $i < $countrowprovider; $i++)
                {
                    $amp = mysqli_fetch_row($queryforprovider);
                    if($amp[0] == $_POST['providers_id'])
                    {
                        $flagforprovider = true;
                        break;
                    }
                }
                if($flagforprovider == false && $_POST['providers_id'] != "" && $_POST['books_ISBN'] != "")
                {
                    echo "Введённый идентификатор поставщика книги не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(!preg_match("/^[0-9]+$/", $_POST['books_pages_amount']) && $_POST['books_pages_amount'] != "")
                {
                    $err = " ";
                    echo "Количество страниц книги может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['books_pages_amount'], 'UTF-8') > 4 && $_POST['books_pages_amount'] != "")
                {
                    $err = " ";
                    echo "Количество страниц книги может быть длиной не более 4 символов!<br>";
                }
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё\" ]+$/u", $_POST['books_publishing_office']) && $_POST['books_publishing_office'] != "")
                {
                    $err = " ";
                    echo "Название издательства книги может состоять только из букв английского и русского алфавитов и знака \"!<br>";
                }
                if((iconv_strlen($_POST['books_publishing_office'], 'UTF-8') < 2 or iconv_strlen($_POST['books_publishing_office'], 'UTF-8') > 75) && $_POST['books_publishing_office'] != "")
                {
                    $err = " ";
                    echo "Название издательства книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!($_POST['books_is_handed_over'] == "да" || $_POST['books_is_handed_over'] == "нет") && $_POST['books_is_handed_over'] != "")
                {
                    $err = " ";
                    echo "В поле \"Выдана ли книга\" можно вводить только \"да\" или \"нет\"!<br>";
                }
                if(($_FILES['books_photo']['error'] > 0 || $_FILES['books_photo']['size'] > 1073741800) && $_FILES['books_photo']['error'] != 4)
                {
                    $err = " ";
                    echo "Максимальный размер загружаемого файла - 1 гигабайт!<br>";
                }
                if(!preg_match($permittedextensions, $fileextension) && $_FILES['books_photo']['error'] != 4)
                {
                    $err = " ";
                    echo "Могут быть загружены файлы только с расширениями \"jpg\" и \"png\"!<br>";
                }
                if(count($err) == 0 && $flagforform == false)
                {
                    $queryforcheckISBN = mysqli_query($link, "SELECT books_ISBN FROM Books WHERE books_ISBN = '".$booksISBN."'");
                    $countrowISBN = mysqli_num_rows($queryforcheckISBN);
                    $flagforheader = true;
                    if($countrowISBN != 1 && $_POST['books_ISBN'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Введённый ISBN-код книги не соответствует ни одному из существующих!</p>";
                    }
                    else if(($_POST['books_name'] != "" || $_POST['books_author'] != "" || $_POST['books_publishing_year'] != "" || $_POST['providers_id'] != "" || $_POST['books_pages_amount'] != "" || $_POST['books_publishing_office'] != "" || $_POST['books_is_handed_over'] != "" || $_FILES['books_photo']['error'] != 4) && $_POST['books_ISBN'] == "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели ISBN-код книги</p>";
                    }
                    else if($_POST['books_name'] == "" && $_POST['books_author'] == "" && $_POST['books_publishing_year'] == "" && $_POST['providers_id'] == "" && $_POST['books_pages_amount'] == "" && $_POST['books_publishing_office'] == "" && $_POST['books_is_handed_over'] == "" && $_FILES['books_photo']['error'] == 4 && $_POST['books_ISBN'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                    }
                    else
                    {
                        if(empty($booksISBN) && empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                        }

                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspa."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(3!p1)
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p1)

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(3!p2)
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p2)

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(3!p3)
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p3)

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(3!p4)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p4)

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(3!p5)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p5)

                        //(3!p6)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(3!p6)

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(4!p1)
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(4!p1)

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(4!p2)
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(4!p2)

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(4!p3)
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(4!p3)

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(4!p4)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(4!p4)

                        //(4!p5)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(4!p5)

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa." WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(5!p1)
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(5!p1)

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(5!p2)
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(5!p2)

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(5!p3)
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(5!p3)

                        //(5!p4)
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(5!p4)

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(6!p1)
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(6!p1)
                        
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(6!p2)
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(6!p2)

                        //(6!p3)
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(6!p3)

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] > 0)
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0 WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && empty($booksiho) && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        //(7!p1)
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(7!p1)

                        //(7!p2)
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        //(7!p2)

                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "да" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 1, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                        if(!empty($booksname) && !empty($booksauthor) && !empty($bookspy) && !empty($providersid) && !empty($bookspa) && !empty($bookspo) && $booksiho == "нет" && $_FILES['books_photo']['error'] == 0)
                        {
                            $file = "files/".$_FILES['books_photo']['name'];
                            move_uploaded_file($_FILES['books_photo']['tmp_name'], $file);
                            $queryforupdate = mysqli_query($link, "UPDATE Books SET books_name = '".$booksname."', books_author = '".$booksauthor."', books_publishing_year = ".$bookspy.", providers_id = ".$providersid.", books_pages_amount = ".$bookspa.", books_publishing_office = '".$bookspo."', books_is_handed_over = 0, books_photo = '".$file."' WHERE books_ISBN = '".$booksISBN."'");
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: books.php");
                }
            }
            if(count($err) > 0 || $flagforform)
            {
                echo "<br><br>Введите ISBN-код книги, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'books_ISBN'><br><br>
                <hr style = 'max-width: 466px; margin: 0px'>
                <p>Введите название книги:</p><input type = 'text' name = 'books_name'>
                <p>Введите имя автора книги:</p><input type = 'text' name = 'books_author'>
                <p>Введите год издания книги:</p><input type = 'text' name = 'books_publishing_year'>
                <p>Введите идентификатор поставщика книги:</p><input type = 'text' name = 'providers_id'>
                <p>Введите количество страниц книги:</p><input type = 'text' name = 'books_pages_amount'>
                <p>Введите название издательства книги:</p><input type = 'text' name = 'books_publishing_office'>
                <p>Введите выдана ли книга:</p><input type = 'text' name = 'books_is_handed_over'>
                <p>Загрузите фото книги:</p>
                <input type = 'hidden' name = 'MAX_FILE_SIZE' value = '1073741800'>
                <input type = 'file' name = 'books_photo'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите ISBN-код книги, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'books_ISBN'><br><br>
                <hr style = 'max-width: 466px; margin: 0px'>
                <p>Введите название книги:</p><input type = 'text' name = 'books_name'>
                <p>Введите имя автора книги:</p><input type = 'text' name = 'books_author'>
                <p>Введите год издания книги:</p><input type = 'text' name = 'books_publishing_year'>
                <p>Введите идентификатор поставщика книги:</p><input type = 'text' name = 'providers_id'>
                <p>Введите количество страниц книги:</p><input type = 'text' name = 'books_pages_amount'>
                <p>Введите название издательства книги:</p><input type = 'text' name = 'books_publishing_office'>
                <p>Введите выдана ли книга:</p><input type = 'text' name = 'books_is_handed_over'>
                <p>Загрузите фото книги:</p>
                <input type = 'hidden' name = 'MAX_FILE_SIZE' value = '1073741800'>
                <input type = 'file' name = 'books_photo'><br><br>
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