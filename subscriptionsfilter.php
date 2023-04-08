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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Абонементы читателей</a> > <a>Фильтрация</a></p>";
            if(isset($_GET['filtersnbuttona']))
            {
                $subscriptionsid = $_GET['subscriptions_id'];
                $erra = array();
                if(!preg_match("/^[0-9]+$/", $_GET['subscriptions_id']) && $_GET['subscriptions_id'] != "")
                {
                    $erra = " ";
                    echo "Номер абонемента читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_GET['subscriptions_id'], 'UTF-8') > 11 && $_GET['subscriptions_id'] != "")
                {
                    $erra = " ";
                    echo "Номер абонемента читателя может быть длиной не более 11 символов!<br>";
                }
                if(count($erra) == 0)
                {
                    if(empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Subscriptions");
                    }
                    if(!empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Subscriptions WHERE subscriptions_id = ".$subscriptionsid."");
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1 || $_GET['subscriptions_id'] == "0")
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
                    }
                    else
                    {
                        echo "<h1>Таблица: Абонементы читателей</h1>";
                        echo "<h3>Количество позиций: ".$countrow."</h3>";
                        echo "<table border = '1'>
                        <tr style = 'text-align: center; font-weight: bold'>
                        <td>id</td>
                        <td>Идентификатор читателя</td>
                        <td>Дата регистрации абонемента читателя</td>
                        <td>ISBN-код книги</td>
                        <td>Дата выдачи книги</td>
                        <td>Дата возврата книги</td>
                        <td>Идентификатор работника</td>
                        <td>Закрыт ли абонемент</td>
                        </tr>";
                        for($i = 0; $i < $countrow; $i++)
                        {
                            $row = mysqli_fetch_row($query);
                            for($j = 0; $j < 8; $j++)
                            {
                                if($j == 0)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 1)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 2)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 3)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 4)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 5)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 6)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 7)
                                {
                                    $boolvalue;
                                    if($row[$j] == 0)
                                    {
                                        $boolvalue = 'нет';
                                    }
                                    else
                                    {
                                        $boolvalue = 'да';
                                    }
                                    echo "<td style = 'text-align: center'>".$boolvalue."</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table><br>";
                    }
                }
            }
            if(isset($_GET['filterrnabbuttona']))
            {
                $readersname = $_GET['readers_name'];
                $flag = true;
                $erra = array();
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_GET['readers_name']) && $_GET['readers_name'] != "")
                {
                    $erra = " ";
                    echo "Имя читателя может состоять только из русских букв!<br>";
                }
                if((iconv_strlen($_GET['readers_name'], 'UTF-8') < 2 or iconv_strlen($_GET['readers_name'], 'UTF-8') > 75) && $_GET['readers_name'] != "")
                {
                    $erra = " ";
                    echo "Имя читателя может быть длиной от 2 до 75 символов!<br>";
                }
                if(count($erra) == 0)
                {
                    if(empty($readersname))
                    {
                        $queryforselect = mysqli_query($link, "SELECT * FROM Subscriptions");
                        $flag = false;
                    }
                    if(!empty($readersname))
                    {
                        $queryforselect = mysqli_query($link, "SELECT * FROM Subscriptions WHERE Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_name LIKE '%".$readersname."%')");
                        $queryforcount = mysqli_query($link, "SELECT DISTINCT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_name LIKE '%".$readersname."%')");
                    }
                    $countrows = mysqli_num_rows($queryforselect);
                    if($flag)
                    {
                        $countrowc = mysqli_num_rows($queryforcount);
                    }
                    if($countrows < 1)
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
                    }
                    else
                    {
                        echo "<h1>Таблица: Абонементы читателей</h1>";
                        echo "<h3>Количество позиций: ".$countrows."</h3>";
                        echo "<table border = '1'>
                        <tr style = 'text-align: center; font-weight: bold'>
                        <td>id</td>
                        <td>Идентификатор читателя</td>
                        <td>Дата регистрации абонемента читателя</td>
                        <td>ISBN-код книги</td>
                        <td>Дата выдачи книги</td>
                        <td>Дата возврата книги</td>
                        <td>Идентификатор работника</td>
                        <td>Закрыт ли абонемент</td>
                        </tr>";
                        for($i = 0; $i < $countrows; $i++)
                        {
                            $row = mysqli_fetch_row($queryforselect);
                            for($j = 0; $j < 8; $j++)
                            {
                                if($j == 0)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 1)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 2)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 3)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 4)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 5)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 6)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 7)
                                {
                                    $boolvalue;
                                    if($row[$j] == 0)
                                    {
                                        $boolvalue = 'нет';
                                    }
                                    else
                                    {
                                        $boolvalue = 'да';
                                    }
                                    echo "<td style = 'text-align: center'>".$boolvalue."</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table><br>";
                    }
                    if($flag)
                    {
                        echo "<table border = '1'>
                        <td style = 'text-align: center; font-weight: bold'>Всего различных книг брал читатель:</td>
                        <td style = 'text-align: center'>".$countrowc."</td>
                        </table><br>";
                    }
                }
            }
            if(isset($_GET['filterhobuttona']))
            {
                $query = mysqli_query($link, "SELECT * FROM Books WHERE books_is_handed_over = 1");
                $countrow = mysqli_num_rows($query);
                if($countrow < 1)
                {
                    echo "<p style = 'font-size: 20px'>Нет выданных книг</p>";
                }
                else
                {
                    echo "<h1>Таблица: Книги</h1>";
                    echo "<h3>Количество позиций: ".$countrow."</h3>";
                    echo "<table border = '1'>
                    <tr style = 'text-align: center; font-weight: bold'>
                    <td>ISBN-код книги</td>
                    <td>Название книги</td>
                    <td>Автор книги</td>
                    <td>Год издания книги</td>
                    <td>Идентификатор поставщика книги</td>
                    <td>Количество страниц в книге</td>
                    <td>Издательство книги</td>
                    <td>Выдана ли книга</td>
                    <td>Фото книги</td>
                    </tr>";
                    for($i = 0; $i < $countrow; $i++)
                    {
                        $row = mysqli_fetch_row($query);
                        for($j = 0; $j < 9; $j++)
                        {
                            if($j == 0)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 1)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 2)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 3)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 4)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 5)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 6)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 7)
                            {
                                $boolvalue;
                                if($row[$j] == 0)
                                {
                                    $boolvalue = "нет";
                                }
                                else
                                {
                                    $boolvalue = "да";
                                }
                                echo "<td style = 'text-align: center'>".$boolvalue."</td>";
                            }
                            if($j == 8)
                            {
                                echo "<td><img src = '".$row[$j]."' width = '200px'></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    echo "</table><br>";
                }
            }
            if(count($erra) > 0)
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtersna'>
                <br><input type = 'submit' name = 'filterhobuttona' value = 'Отобрать выданные на текущий момент книги'>
                <p>Номер абонемента читателя:</p>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'filtersnbuttona' value = 'Отфильтровать'>
                </form>";
            }
            else
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtersna'>
                <input type = 'submit' name = 'filterhobuttona' value = 'Отобрать выданные на текущий момент книги'>
                <p>Номер абонемента читателя:</p>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'filtersnbuttona' value = 'Отфильтровать'>
                </form>";
            }
            echo "<hr style = 'max-width: 296px; margin: 0px'>";
            echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filterrnaba'>
            <p>Имя читателя:</p>
            <input type = 'text' name = 'readers_name'><br><br>
            <input type = 'submit' name = 'filterrnabbuttona' value = 'Отфильтровать'>
            </form>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'subscriptions.php'>Мои абонементы</a> > <a>Фильтрация</a></p>";
            if(isset($_GET['filtersnbuttonu']))
            {
                $subscriptionsid = $_GET['subscriptions_id'];
                $erru = array();
                if(!preg_match("/^[0-9]+$/", $_GET['subscriptions_id']) && $_GET['subscriptions_id'] != "")
                {
                    $erru = " ";
                    $flagforsubscription = true;
                    echo "Номер абонемента читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_GET['subscriptions_id'], 'UTF-8') > 11 && $_GET['subscriptions_id'] != "")
                {
                    $erru = " ";
                    $flagforsubscription = true;
                    echo "Номер абонемента читателя может быть длиной не более 11 символов!<br>";
                }
                if(count($erru) == 0)
                {
                    if(empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Subscriptions WHERE Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."'))");
                        $queryforuserr = mysqli_query($link, "SELECT Readers.readers_name FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."')");
                        $massivequr = mysqli_fetch_all($queryforuserr, MYSQLI_NUM);
                        $queryforuserb = mysqli_query($link, "SELECT Books.books_name FROM Books, Subscriptions, Readers, Users WHERE Books.books_ISBN = Subscriptions.books_ISBN AND Subscriptions.readers_id = Readers.readers_id AND Readers.readers_id = Users.readers_id AND Users.users_login = '".$userdata['users_login']."'");
                        $massivequb = mysqli_fetch_all($queryforuserb, MYSQLI_NUM);
                        $queryforuserw = mysqli_query($link, "SELECT Workers.workers_name FROM Workers, Subscriptions, Readers, Users WHERE Workers.workers_id = Subscriptions.workers_id AND Subscriptions.readers_id = Readers.readers_id AND Readers.readers_id = Users.readers_id AND Users.users_login = '".$userdata['users_login']."'");
                        $massivequw = mysqli_fetch_all($queryforuserw, MYSQLI_NUM);
                    }
                    if(!empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid." AND Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."'))");
                        $queryforuserr = mysqli_query($link, "SELECT Readers.readers_name FROM Readers WHERE Readers.readers_id IN (SELECT Subscriptions.readers_id FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.") AND Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."')");
                        $massivequr = mysqli_fetch_all($queryforuserr, MYSQLI_NUM);
                        $queryforuserb = mysqli_query($link, "SELECT Books.books_name FROM Books WHERE Books.books_ISBN IN (SELECT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid." AND Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."')))");
                        $massivequb = mysqli_fetch_all($queryforuserb, MYSQLI_NUM);
                        $queryforuserw = mysqli_query($link, "SELECT Workers.workers_name FROM Workers WHERE Workers.workers_id IN (SELECT Subscriptions.workers_id FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid." AND Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."')))");
                        $massivequw = mysqli_fetch_all($queryforuserw, MYSQLI_NUM);
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1 || $_GET['subscriptions_id'] == "0")
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
                    }
                    else
                    {
                        echo "<h1>Таблица: Мои абонементы</h1>";
                        echo "<h3>Количество позиций: ".$countrow."</h3>";
                        echo "<table border = '1'>
                        <tr style = 'text-align: center; font-weight: bold'>
                        <td>Номер абонемента читателя</td>
                        <td>Имя читателя</td>
                        <td>Дата регистрации абонемента читателя</td>
                        <td>Название книги</td>
                        <td>Дата выдачи книги</td>
                        <td>Дата возврата книги</td>
                        <td>Имя работника (оформившего абонемент)</td>
                        </tr>";
                        for($i = 0; $i < $countrow; $i++)
                        {
                            $row = mysqli_fetch_row($query);
                            for($j = 0; $j < 7; $j++)
                            {
                                if($j == 0)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 1)
                                {
                                    echo "<td style = 'text-align: center'>".$massivequr[0][0]."</td>";
                                }
                                if($j == 2)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 3)
                                {
                                    echo "<td style = 'text-align: center'>".$massivequb[$i][0]."</td>";
                                }
                                if($j == 4)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 5)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                if($j == 6)
                                {
                                    echo "<td style = 'text-align: center'>".$massivequw[$i][0]."</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table><br>";
                    }
                }
            }
            if(isset($_GET['filterrnabbuttonu']))
            {
                $query = mysqli_query($link, "SELECT DISTINCT Subscriptions.books_ISBN FROM Subscriptions WHERE Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."'))");
                $countrow = mysqli_num_rows($query);
                if($countrow < 1)
                {
                    echo "<p style = 'font-size: 20px'>Вы ещё не брали ни одну книгу</p>";
                }
                else
                {
                    echo "<table border = '1'>
                    <td style = 'text-align: center; font-weight: bold'>Всего книг:</td>
                    <td style = 'text-align: center'>".$countrow."</td>
                    </table><br>";
                }
            }
            if(isset($_GET['filterhobuttonu']))
            {
                $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books WHERE books_is_handed_over = 1");
                $countrow = mysqli_num_rows($query);
                if($countrow < 1)
                {
                    echo "<p style = 'font-size: 20px'>Нет выданных книг</p>";
                }
                else
                {
                    echo "<h1>Таблица: Книги</h1>";
                    echo "<h3>Количество позиций: ".$countrow."</h3>";
                    echo "<table border = '1'>
                    <tr style = 'text-align: center; font-weight: bold'>
                    <td>ISBN-код книги</td>
                    <td>Название книги</td>
                    <td>Автор книги</td>
                    <td>Год издания книги</td>
                    <td>Количество страниц в книге</td>
                    <td>Издательство книги</td>
                    <td>Выдана ли книга</td>
                    <td>Фото книги</td>
                    </tr>";
                    for($i = 0; $i < $countrow; $i++)
                    {
                        $row = mysqli_fetch_row($query);
                        for($j = 0; $j < 8; $j++)
                        {
                            if($j == 0)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 1)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 2)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 3)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 4)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 5)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            if($j == 6)
                            {
                                $boolvalue;
                                if($row[$j] == 0)
                                {
                                    $boolvalue = "нет";
                                }
                                else
                                {
                                    $boolvalue = "да";
                                }
                                echo "<td style = 'text-align: center'>".$boolvalue."</td>";
                            }
                            if($j == 7)
                            {
                                echo "<td><img src = '".$row[$j]."' width = '200px'></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    echo "</table><br>";
                }
            }
            if(count($erru) > 0)
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtersnu'>
                <br><input type = 'submit' name = 'filterrnabbuttonu' value = 'Показать количество взятых мною различных книг за всё время'><br><br>
                <input type = 'submit' name = 'filterhobuttonu' value = 'Отобрать выданные на текущий момент книги'>
                <p>Номер абонемента читателя:</p>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'filtersnbuttonu' value = 'Отфильтровать'>
                </form>";
            }
            else
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtersnu'>
                <input type = 'submit' name = 'filterrnabbuttonu' value = 'Показать количество взятых мною различных книг за всё время'><br><br>
                <input type = 'submit' name = 'filterhobuttonu' value = 'Отобрать выданные на текущий момент книги'>
                <p>Номер абонемента читателя:</p>
                <input type = 'text' name = 'subscriptions_id'><br><br>
                <input type = 'submit' name = 'filtersnbuttonu' value = 'Отфильтровать'>
                </form>";
            }
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>