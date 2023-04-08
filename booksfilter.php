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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'books.php'>Книги</a> > <a>Фильтрация</a></p>";
            $erra = array();
            if(isset($_GET['filterana']))
            {
                $booksauthor = $_GET['books_author'];
                $booksname = $_GET['books_name'];
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_GET['books_author']) && $_GET['books_author'] != "")
                {
                    $erra = " ";
                    echo "Имя автора книги может состоять только из русских букв!<br>";
                }
                if((iconv_strlen($_GET['books_author'], 'UTF-8') < 2 or iconv_strlen($_GET['books_author'], 'UTF-8') > 75) && $_GET['books_author'] != "")
                {
                    $erra = " ";
                    echo "Имя автора книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9a-zA-ZА-Яа-яЁё\"!:?() ]+$/u", $_GET['books_name']) && $_GET['books_name'] != "")
                {
                    $erra = " ";
                    echo "Название книги может состоять только из букв английского и русского алфавитов, цифр и символов !?:\"()!<br>";
                }
                if((iconv_strlen($_GET['books_name'], 'UTF-8') < 2 or iconv_strlen($_GET['books_name'], 'UTF-8') > 75) && $_GET['books_name'] != "")
                {
                    $erra = " ";
                    echo "Название книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(count($erra) == 0)
                {
                    if(empty($booksauthor) && empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Books");
                    }
                    if(!empty($booksauthor) && empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Books WHERE books_author LIKE '%".$booksauthor."%'");
                    }
                    if(empty($booksauthor) && !empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Books WHERE books_name LIKE '%".$booksname."%'");
                    }
                    if(!empty($booksauthor) && !empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Books WHERE books_author LIKE '%".$booksauthor."%' AND books_name LIKE '%".$booksname."%'");
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1)
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
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
                                else if($j == 1)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 2)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 3)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 4)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 5)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 6)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 7)
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
                                else if($j == 8)
                                {
                                    echo "<td><img src = '".$row[$j]."' width = '200px'></td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table><br>";
                    }
                }
            }
            if(isset($_GET['filternhoa']))
            {
                $query = mysqli_query($link, "SELECT * FROM Books WHERE books_is_handed_over = 0");
                $countrow = mysqli_num_rows($query);
                if($countrow < 1)
                {
                    echo "<p style = 'font-size: 20px'>На данный момент все книги выданы</p>";
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
                            else if($j == 1)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 2)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 3)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 4)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 5)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 6)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 7)
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
                            else if($j == 8)
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
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtera'>
                <br><input type = 'submit' name = 'filternhoa' value = 'Отобрать невыданные книги'>
                <p>Имя автора:</p>
                <input type = 'text' name = 'books_author'>
                <p>Название книги:</p>
                <input type = 'text' name = 'books_name'><br><br>
                <input type = 'submit' name = 'filterana' value = 'Отфильтровать'>
                </form>";
            }
            else
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtera'>
                <input type = 'submit' name = 'filternhoa' value = 'Отобрать невыданные книги'>
                <p>Имя автора:</p>
                <input type = 'text' name = 'books_author'>
                <p>Название книги:</p>
                <input type = 'text' name = 'books_name'><br><br>
                <input type = 'submit' name = 'filterana' value = 'Отфильтровать'>
                </form>";
            }
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'books.php'>Книги</a> > <a>Фильтрация</a></p>";
            $erru = array();
            if(isset($_GET['filteranu']))
            {
                $booksauthor = $_GET['books_author'];
                $booksname = $_GET['books_name'];
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_GET['books_author']) && $_GET['books_author'] != "")
                {
                    $erru = " ";
                    echo "Имя автора книги может состоять только из русских букв!<br>";
                }
                if((iconv_strlen($_GET['books_author'], 'UTF-8') < 2 or iconv_strlen($_GET['books_author'], 'UTF-8') > 75) && $_GET['books_author'] != "")
                {
                    $erru = " ";
                    echo "Имя автора книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9a-zA-ZА-Яа-яЁё\"!:?() ]+$/u", $_GET['books_name']) && $_GET['books_name'] != "")
                {
                    $erru = " ";
                    echo "Название книги может состоять только из букв английского и русского алфавитов, цифр и символов !?:\"()!<br>";
                }
                if((iconv_strlen($_GET['books_name'], 'UTF-8') < 2 or iconv_strlen($_GET['books_name'], 'UTF-8') > 75) && $_GET['books_name'] != "")
                {
                    $erru = " ";
                    echo "Название книги может быть длиной от 2 до 75 символов!<br>";
                }
                if(count($erru) == 0)
                {
                    if(empty($booksauthor) && empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books");
                    }
                    if(!empty($booksauthor) && empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books WHERE books_author LIKE '%".$booksauthor."%'");
                    }
                    if(empty($booksauthor) && !empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books WHERE books_name LIKE '%".$booksname."%'");
                    }
                    if(!empty($booksauthor) && !empty($booksname))
                    {
                        $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books WHERE books_author LIKE '%".$booksauthor."%' AND books_name LIKE '%".$booksname."%'");
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1)
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
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
                                else if($j == 1)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 2)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 3)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 4)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 5)
                                {
                                    echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                                }
                                else if($j == 6)
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
                                else if($j == 7)
                                {
                                    echo "<td><img src = '".$row[$j]."' width = '200px'></td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table><br>";
                    }
                }
            }
            if(isset($_GET['filternhou']))
            {
                $query = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books WHERE books_is_handed_over = 0");
                $countrow = mysqli_num_rows($query);
                if($countrow < 1)
                {
                    echo "<p style = 'font-size: 20px'>На данный момент все книги выданы</p>";
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
                            else if($j == 1)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 2)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 3)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 4)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 5)
                            {
                                echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                            }
                            else if($j == 6)
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
                            else if($j == 7)
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
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filteru'>
                <br><input type = 'submit' name = 'filternhou' value = 'Отобрать невыданные книги'>
                <p>Имя автора:</p>
                <input type = 'text' name = 'books_author'>
                <p>Название книги:</p>
                <input type = 'text' name = 'books_name'><br><br>
                <input type = 'submit' name = 'filteranu' value = 'Отфильтровать'>
                </form>";
            }
            else
            {
                echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filteru'>
                <input type = 'submit' name = 'filternhou' value = 'Отобрать невыданные книги'>
                <p>Имя автора:</p>
                <input type = 'text' name = 'books_author'>
                <p>Название книги:</p>
                <input type = 'text' name = 'books_name'><br><br>
                <input type = 'submit' name = 'filteranu' value = 'Отфильтровать'>
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