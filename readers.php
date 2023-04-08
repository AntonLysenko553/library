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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Читатели</a></p>";
            $queryfortable = mysqli_query($link, "SELECT readers_id, readers_name, readers_passport_series, readers_passport_number, readers_phone FROM Readers");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Читатели</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
            echo "<table border = '1'>
            <tr style = 'text-align: center; font-weight: bold'>
            <td>id</td>
            <td>Имя читателя</td>
            <td>Серия паспорта читателя</td>
            <td>Номер паспорта читателя</td>
            <td>Телефон читателя</td>
            </tr>";
            for($i = 0; $i < $countrowfortable; $i++)
            {
                $row = mysqli_fetch_row($queryfortable);
                for($j = 0; $j < 5; $j++)
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
                }
                echo "</tr>";
            }
            echo "</table><br>";
            echo "<a href = 'addreader.php'><input type = 'button' value = 'Добавить запись о читателе'></a><br><br>";
            echo "<a href = 'updatereader.php'><input type = 'button' value = 'Редактировать информацию о читателе'></a><br><br>";
            echo "<a href = 'deletereader.php'><input type = 'button' value = 'Удалить запись о читателе'></a><br><br>";
            echo "<a href = 'readersfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Читатели</a></p>";
            $queryfortable = mysqli_query($link, "SELECT Readers.readers_name, GROUP_CONCAT(Subscriptions.subscriptions_id SEPARATOR ', ') AS subscriptions_id FROM Readers, Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id GROUP BY Readers.readers_name UNION SELECT Readers.readers_name, NULL AS subscriptions_id FROM Readers WHERE NOT EXISTS (SELECT Subscriptions.subscriptions_id FROM Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id)");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Читатели</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
            echo "<table border = '1'>
            <tr style = 'text-align: center; font-weight: bold'>
            <td>Имя читателя</td>
            <td>Номер абонемента читателя</td>
            </tr>";
            for($i = 0; $i < $countrowfortable; $i++)
            {
                $row = mysqli_fetch_row($queryfortable);
                for($j = 0; $j < 2; $j++)
                {
                    if($j == 0)
                    {
                        echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                    }
                    if($j == 1)
                    {
                        echo "<td style = 'text-align: center'>".$row[$j]."</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table><br>";
            echo "<a href = 'readersfilter.php'><input type = 'button' value = 'Перейти к фильтрации'></a>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>