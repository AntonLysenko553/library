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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a>Поставщики книг</a></p>";
            $queryfortable = mysqli_query($link, "SELECT providers_id, providers_name, providers_address, providers_phone, providers_email FROM Providers");
            $countrowfortable = mysqli_num_rows($queryfortable);
            echo "<h1>Таблица: Поставщики книг</h1>";
            echo "<h3>Количество позиций: ".$countrowfortable."</h3>";
            echo "<table border = '1'>
            <tr style = 'text-align: center; font-weight: bold'>
            <td>id</td>
            <td>Название поставщика</td>
            <td>Адрес поставщика</td>
            <td>Телефон поставщика</td>
            <td>Email поставщика</td>
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
            echo "<a href = 'addprovider.php'><input type = 'button' value = 'Добавить запись о поставщике'></a><br><br>";
            echo "<a href = 'updateprovider.php'><input type = 'button' value = 'Редактировать информацию о поставщике'></a><br><br>";
            echo "<a href = 'deleteprovider.php'><input type = 'button' value = 'Удалить запись о поставщике'></a>";
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