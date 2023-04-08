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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'librarycards.php'>Читательские билеты</a> > <a>Добавление записи о читательском билете</a></p>";
            if(isset($_POST['submit']))
            {
                $readersname = $_POST['readers_name'];
                $queryforselectr = mysqli_fetch_assoc(mysqli_query($link, "SELECT readers_id FROM Readers WHERE readers_name = '".$readersname."' LIMIT 1"));
                $insertvaluer = $queryforselectr['readers_id'];
                $addlibrarycard = mysqli_query($link, "INSERT INTO Librarycards VALUES(NULL, ".$insertvaluer.", NULL)");
                header("Location: librarycards.php");
            }
            echo "<form method = 'POST' enctype = 'multipart/form-data'>
            <p>Выберите читателя:</p><select name = 'readers_name' required>";
            $query = mysqli_query($link, "SELECT Readers.readers_name FROM Readers WHERE NOT EXISTS (SELECT Librarycards.library_cards_id FROM Librarycards WHERE Readers.readers_id = Librarycards.readers_id)");
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
            echo "</select><br><br>
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