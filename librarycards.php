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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Читательские билеты</a></p>";
            $queryfortable = mysqli_query($link, "SELECT library_cards_id, readers_id, subscriptions_id FROM Librarycards");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Читательские билеты</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
            echo "<table border = '1'>
            <tr style = 'text-align: center; font-weight: bold'>
            <td>id</td>
            <td>Идентификатор читателя</td>
            <td>Номер абонемента читателя</td>
            </tr>";
            for($i = 0; $i < $countrowfortable; $i++)
            {
                $row = mysqli_fetch_row($queryfortable);
                for($j = 0; $j < 3; $j++)
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
                }
                echo "</tr>";
            }
            echo "</table><br>";
            echo "<a href = 'addlibrarycard.php'><input type = 'button' value = 'Добавить запись о читательском билете'></a><br><br>";
            echo "<a href = 'addstolc.php'><input type = 'button' value = 'Добавить информацию об абонементе к читательскому билету'></a><br><br>";
            echo "<a href = 'updatelibrarycard.php'><input type = 'button' value = 'Редактировать информацию о читательском билете'></a><br><br>";
            echo "<a href = 'deletelibrarycard.php'><input type = 'button' value = 'Удалить запись о читательском билете'></a>";
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