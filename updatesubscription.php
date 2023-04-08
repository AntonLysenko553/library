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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Абонементы читателей</a> > <a>Редактирование информации об абонементе</a></p>";
            if(isset($_POST['submit']))
            {
                $subscriptionsid = $_POST['subscriptions_id'];
                $readersid = $_POST['readers_id'];
                $subscriptionsrd = $_POST['subscriptions_registration_date'];
                $booksISBN = $_POST['books_ISBN'];
                $subscriptionsed = $_POST['subscriptions_extradition_date'];
                $subscriptionsrnd = $_POST['subscriptions_return_date'];
                $workersid = $_POST['workers_id'];
                $queryforreader = mysqli_query($link, "SELECT readers_id FROM Readers");
                $countrowreader = mysqli_num_rows($queryforreader);
                $queryforISBN = mysqli_query($link, "SELECT books_ISBN FROM Books");
                $countrowISBN = mysqli_num_rows($queryforISBN);
                $queryforworker = mysqli_query($link, "SELECT workers_id FROM Workers");
                $countrowworker = mysqli_num_rows($queryforworker);
                $err = array();
                $flagforheader;
                $flagforreader = false;
                $flagforISBN = false;
                $flagforworker = false;
                $flagforform = false;
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
                if($flagforreader == false && $_POST['readers_id'] != "" && $_POST['subscriptions_id'] != "")
                {
                    echo "Введённый идентификатор читателя не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}+$/", $_POST['subscriptions_registration_date']) && $_POST['subscriptions_registration_date'] != "")
                {
                    $err = " ";
                    echo "Дата регистрации абонемента может вводиться только в формате \"гггг-мм-дд\" и состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['subscriptions_registration_date'], 'UTF-8') != 10 && $_POST['subscriptions_registration_date'] != "")
                {
                    $err = " ";
                    echo "Дата регистрации абонемента может быть длиной только 10 символов!<br>";
                }
                if(!preg_match("/^[0-9-]+$/", $_POST['books_ISBN']) && $_POST['books_ISBN'] != "")
                {
                    $err = " ";
                    $flagforISBN = true;
                    echo "ISBN-код книги может состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['books_ISBN'], 'UTF-8') != 17 && $_POST['books_ISBN'] != "")
                {
                    $err = " ";
                    $flagforISBN = true;
                    echo "ISBN-код книги может быть длиной только 17 символов!<br>";
                }
                for($i = 0; $i < $countrowISBN; $i++)
                {
                    $amp = mysqli_fetch_row($queryforISBN);
                    if($amp[0] == $_POST['books_ISBN'])
                    {
                        $flagforISBN = true;
                        break;
                    }
                }
                if($flagforISBN == false && $_POST['books_ISBN'] != "" && $_POST['subscriptions_id'] != "")
                {
                    echo "Введённый ISBN-код книги не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}+$/", $_POST['subscriptions_extradition_date']) && $_POST['subscriptions_extradition_date'] != "")
                {
                    $err = " ";
                    echo "Дата выдачи книги может вводиться только в формате \"гггг-мм-дд\" и состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['subscriptions_extradition_date'], 'UTF-8') != 10 && $_POST['subscriptions_extradition_date'] != "")
                {
                    $err = " ";
                    echo "Дата выдачи книги может быть длиной только 10 символов!<br>";
                }
                if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}+$/", $_POST['subscriptions_return_date']) && $_POST['subscriptions_return_date'] != "")
                {
                    $err = " ";
                    echo "Дата возврата книги может вводиться только в формате \"гггг-мм-дд\" и состоять только из цифр и дефисов!<br>";
                }
                if(iconv_strlen($_POST['subscriptions_return_date'], 'UTF-8') != 10 && $_POST['subscriptions_return_date'] != "")
                {
                    $err = " ";
                    echo "Дата возврата книги может быть длиной только 10 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_POST['workers_id']) && $_POST['workers_id'] != "")
                {
                    $err = " ";
                    $flagforworker = true;
                    echo "Идентификатор работника может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['workers_id'], 'UTF-8') > 11 && $_POST['workers_id'] != "")
                {
                    $err = " ";
                    $flagforworker = true;
                    echo "Идентификатор работника может быть длиной не более 11 символов!<br>";
                }
                for($i = 0; $i < $countrowworker; $i++)
                {
                    $amp = mysqli_fetch_row($queryforworker);
                    if($amp[0] == $_POST['workers_id'])
                    {
                        $flagforworker = true;
                        break;
                    }
                }
                if($flagforworker == false && $_POST['workers_id'] != "" && $_POST['subscriptions_id'] != "")
                {
                    echo "Введённый идентификатор работника не соответствует ни одному из существующих!<br>";
                    $flagforform = true;
                }
                if(count($err) == 0 && $flagforform == false)
                {
                    $flagiscloseddate = false;
                    $queryforiscloseddate = mysqli_query($link, "SELECT subscriptions_id FROM Subscriptions WHERE is_closed = 1");
                    $countrowiscloseddate = mysqli_num_rows($queryforiscloseddate);
                    for($i = 0; $i < $countrowiscloseddate; $i++)
                    {
                        $amiscloseddate = mysqli_fetch_row($queryforiscloseddate);
                        if($amiscloseddate[0] == $_POST['subscriptions_id'])
                        {
                            $flagiscloseddate = true;
                            break;
                        }
                    }
                    $flagforheader = true;
                    $flagforcheck = true;
                    $flagfornull = true;
                    if($_POST['subscriptions_id'] != "" || $_POST['readers_id'] != "" || $_POST['subscriptions_registration_date'] != "" || $_POST['books_ISBN'] != "" || $_POST['subscriptions_extradition_date'] != "" || $_POST['subscriptions_return_date'] != "" || $_POST['workers_id'] != "")
                    {
                        $flagfornull = false;
                    }
                    if($_POST['subscriptions_id'] == "0")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        $flagfornull = false;
                        echo "<p style = 'font-size: 20px'>Введённый идентификатор абонемента не соответствует ни одному из существующих!</p>";
                    }
                    if(empty($subscriptionsid) && empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagfornull)
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                    }
                    if(($_POST['readers_id'] != "" || $_POST['subscriptions_registration_date'] != "" || $_POST['books_ISBN'] != "" || $_POST['subscriptions_extradition_date'] != "" || $_POST['subscriptions_return_date'] != "" || $_POST['workers_id'] != "") && $_POST['subscriptions_id'] == "")
                    {
                        $flagforheader = false;
                        $flagforcheck = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор абонемента</p>";
                    }
                    if($flagforcheck)
                    {
                        $queryforcheckid = mysqli_query($link, "SELECT subscriptions_id FROM Subscriptions WHERE subscriptions_id = ".$subscriptionsid."");
                        $countrowid = mysqli_num_rows($queryforcheckid);
                        if($countrowid != 1 && $_POST['subscriptions_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Введённый идентификатор абонемента не соответствует ни одному из существующих!</p>";
                        }
                        else if($_POST['readers_id'] == "" && $_POST['subscriptions_registration_date'] == "" && $_POST['books_ISBN'] == "" && $_POST['subscriptions_extradition_date'] == "" && $_POST['subscriptions_return_date'] == "" && $_POST['workers_id'] == "" && $_POST['subscriptions_id'] != "")
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                        }
                        else
                        {
                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(3!p1)
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(3!p1)

                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(3!p2)
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(3!p2)

                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(3!p3)
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(3!p3)

                            //(3!p4)
                            if(empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(3!p4)

                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(4!p1)
                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(4!p1)

                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(4!p2)
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(4!p2)

                            //(4!p3)
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(4!p3)

                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."' WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            //(5!p1)
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && !empty($subscriptionsrd) && empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid))
                            {
                                $queryforupdate = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }

                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(5!p1)

                            //(5!p2)
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                            //(5!p2)

                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate == false)
                            {
                                $queryforupdateob = mysqli_query($link, "UPDATE Books SET Books.books_is_handed_over = 0 WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                                $queryforupdatenb = mysqli_query($link, "UPDATE Books SET books_is_handed_over = 1 WHERE books_ISBN = '".$booksISBN."'");
                            }
                            if(!empty($readersid) && !empty($subscriptionsrd) && !empty($booksISBN) && !empty($subscriptionsed) && !empty($subscriptionsrnd) && !empty($workersid) && $flagiscloseddate)
                            {
                                $queryforupdates = mysqli_query($link, "UPDATE Subscriptions SET readers_id = ".$readersid.", subscriptions_registration_date = '".$subscriptionsrd."', books_ISBN = '".$booksISBN."', subscriptions_extradition_date = '".$subscriptionsed."', subscriptions_return_date = '".$subscriptionsrnd."', workers_id = ".$workersid." WHERE subscriptions_id = ".$subscriptionsid."");
                            }
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: subscriptions.php");
                }
            }
            if(count($err) > 0 || $flagforform)
            {
                echo "<br><br>Введите идентификатор абонемента читателя, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <hr style = 'max-width: 606px; margin: 0px'>
                <p>Введите идентификатор читателя:</p><input type = 'text' name = 'readers_id'>
                <p>Введите дату регистрации абонемента читателя:</p><input type = 'text' name = 'subscriptions_registration_date'>
                <p>Введите ISBN-код книги:</p><input type = 'text' name = 'books_ISBN'>
                <p>Введите дату выдачи книги:</p><input type = 'text' name = 'subscriptions_extradition_date'>
                <p>Введите дату возврата книги:</p><input type = 'text' name = 'subscriptions_return_date'>
                <p>Введите идентификатор работника:</p><input type = 'text' name = 'workers_id'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор абонемента читателя, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <hr style = 'max-width: 606px; margin: 0px'>
                <p>Введите идентификатор читателя:</p><input type = 'text' name = 'readers_id'>
                <p>Введите дату регистрации абонемента читателя:</p><input type = 'text' name = 'subscriptions_registration_date'>
                <p>Введите ISBN-код книги:</p><input type = 'text' name = 'books_ISBN'>
                <p>Введите дату выдачи книги:</p><input type = 'text' name = 'subscriptions_extradition_date'>
                <p>Введите дату возврата книги:</p><input type = 'text' name = 'subscriptions_return_date'>
                <p>Введите идентификатор работника:</p><input type = 'text' name = 'workers_id'><br><br>
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