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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Абонементы читателей</a></p>";
            $queryfortable = mysqli_query($link, "SELECT subscriptions_id, readers_id, subscriptions_registration_date, books_ISBN, subscriptions_extradition_date, subscriptions_return_date, workers_id, is_closed FROM Subscriptions");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Абонементы читателей</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
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
            echo "<a href = 'addsubscription.php'><input type = 'button' value = 'Добавить запись об абонементе'></a><br><br>";
            echo "<a href = 'bookreturn.php'><input type = 'button' value = 'Принять книгу к сдаче'></a><br><br>";
            echo "<a href = 'updatesubscription.php'><input type = 'button' value = 'Редактировать информацию об абонементе'></a><br><br>";
            echo "<a href = 'deletesubscription.php'><input type = 'button' value = 'Удалить запись об абонементе'></a><br><br>";
            echo "<a href = 'subscriptionsfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Мои абонементы</a></p>";
            $queryfortable = mysqli_query($link, "SELECT * FROM Subscriptions WHERE Subscriptions.readers_id IN (SELECT Readers.readers_id FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."'))");
            $countrowfortable = mysqli_num_rows($queryfortable);
            $queryforuserr = mysqli_query($link, "SELECT Readers.readers_name FROM Readers WHERE Readers.readers_id IN (SELECT Users.readers_id FROM Users WHERE Users.users_login = '".$userdata['users_login']."')");
            $massivequr = mysqli_fetch_all($queryforuserr, MYSQLI_NUM);
            $queryforuserb = mysqli_query($link, "SELECT Books.books_name FROM Books, Subscriptions, Readers, Users WHERE Books.books_ISBN = Subscriptions.books_ISBN AND Subscriptions.readers_id = Readers.readers_id AND Readers.readers_id = Users.readers_id AND Users.users_login = '".$userdata['users_login']."'");
            $massivequb = mysqli_fetch_all($queryforuserb, MYSQLI_NUM);
            $countrowforuserb = mysqli_num_rows($queryforuserb);
            $queryforuserw = mysqli_query($link, "SELECT Workers.workers_name FROM Workers, Subscriptions, Readers, Users WHERE Workers.workers_id = Subscriptions.workers_id AND Subscriptions.readers_id = Readers.readers_id AND Readers.readers_id = Users.readers_id AND Users.users_login = '".$userdata['users_login']."'");
            $massivequw = mysqli_fetch_all($queryforuserw, MYSQLI_NUM);
            $countrowforuserw = mysqli_num_rows($queryforuserw);
            if($countrowfortable > 0)
            {
                echo "<h1>Таблица: Мои абонементы</h1>";
                echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
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
                for($i = 0; $i < $countrowfortable; $i++)
                {
                    $row = mysqli_fetch_row($queryfortable);
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
                echo "<a href = 'subscriptionsfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
            }
            else
            {
                echo "<p style = 'font-size: 20px'>У вас ещё нет абонементов<br>Чтобы оформить абонемент, обратитесь, пожалуйста, в библиотеку</p>";
            }
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>