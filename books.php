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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Книги</a></p>";
            $queryfortable = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, providers_id, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Книги</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
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
            for($i = 0; $i < $countrowfortable; $i++)
            {
                $row = mysqli_fetch_row($queryfortable);
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
            echo "<a href = 'addbook.php'><input type = 'button' value = 'Добавить запись о книге'></a><br><br>";
            echo "<a href = 'updatebook.php'><input type = 'button' value = 'Редактировать информацию о книге'></a><br><br>";
            echo "<a href = 'deletebook.php'><input type = 'button' value = 'Удалить запись о книге'></a><br><br>";
            echo "<a href = 'booksfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Книги</a></p>";
            $queryfortable = mysqli_query($link, "SELECT books_ISBN, books_name, books_author, books_publishing_year, books_pages_amount, books_publishing_office, books_is_handed_over, books_photo FROM Books");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Книги</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
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
            for($i = 0; $i < $countrowfortable; $i++)
            {
                $row = mysqli_fetch_row($queryfortable);
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
                            $boolvalue = 'нет';
                        }
                        else
                        {
                            $boolvalue = 'да';
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
            echo "<a href = 'booksfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>