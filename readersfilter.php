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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'readers.php'>Читатели</a> > <a>Фильтрация</a></p>";
            if(isset($_GET['filterra']))
            {
                $readersname = $_GET['readers_name'];
                $subscriptionsid = $_GET['subscriptions_id'];
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
                    if(empty($readersname) && empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Readers");
                    }
                    if(!empty($readersname) && empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Readers WHERE readers_name LIKE '%".$readersname."%'");
                    }
                    if(empty($readersname) && !empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Readers WHERE Readers.readers_id IN (SELECT Subscriptions.readers_id FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                    }
                    if(!empty($readersname) && !empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT * FROM Readers WHERE readers_name LIKE '%".$readersname."%' AND Readers.readers_id IN (SELECT Subscriptions.readers_id FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.")");
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1  || $_GET['subscriptions_id'] == "0")
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
                    }
                    else
                    {
                        echo "<h1>Таблица: Читатели</h1>";
                        echo "<h3>Количество позиций: ".$countrow."</h3>";
                        echo "<table border = '1'>
                        <tr style = 'text-align: center; font-weight: bold'>
                        <td>id</td>
                        <td>Имя читателя</td>
                        <td>Серия паспорта читателя</td>
                        <td>Номер паспорта читателя</td>
                        <td>Телефон</td>
                        </tr>";
                        for($i = 0; $i < $countrow; $i++)
                        {
                            $row = mysqli_fetch_row($query);
                            for($j = 0; $j < 5; $j++)
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
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                }
            }
            echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filtera'>
            <p>Имя читателя:</p>
            <input type = 'text' name = 'readers_name'>
            <p>Номер абонемента читателя:</p>
            <input type = 'text' name = 'subscriptions_id'><br><br>
            <input type = 'submit' name = 'filterra' value = 'Отфильтровать'>
            </form>";
        }
        else
        {
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'readers.php'>Читатели</a> > <a>Фильтрация</a></p>";
            if(isset($_GET['filterru']))
            {
                $readersname = $_GET['readers_name'];
                $subscriptionsid = $_GET['subscriptions_id'];
                $erru = array();
                if(!preg_match("/^[А-Яа-яЁё ]+$/u", $_GET['readers_name']) && $_GET['readers_name'] != "")
                {
                    $erru = " ";
                    echo "Имя читателя может состоять только из русских букв!<br>";
                }
                if((iconv_strlen($_GET['readers_name'], 'UTF-8') < 2 or iconv_strlen($_GET['readers_name'], 'UTF-8') > 75) && $_GET['readers_name'] != "")
                {
                    $erru = " ";
                    echo "Имя читателя может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9]+$/", $_GET['subscriptions_id']) && $_GET['subscriptions_id'] != "")
                {
                    $erru = " ";
                    echo "Номер абонемента читателя может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_GET['subscriptions_id'], 'UTF-8') > 11 && $_GET['subscriptions_id'] != "")
                {
                    $erru = " ";
                    echo "Номер абонемента читателя может быть длиной не более 11 символов!<br>";
                }
                if(count($erru) == 0)
                {
                    $flagcn = false;
                    if($_GET['readers_name'] != "")
                    {
                        $queryfornern = mysqli_query($link, "SELECT Readers.readers_name FROM Readers WHERE NOT EXISTS (SELECT Subscriptions.subscriptions_id FROM Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id)");
                        $countrowforqnern = mysqli_num_rows($queryfornern);
                        for($i = 0; $i < $countrowforqnern; $i++)
                        {
                            $currentname = mysqli_fetch_row($queryfornern);
                            if(strpos($currentname[0], $readersname) !== false)
                            {
                                $flagcn = true;
                                break;
                            }
                        }
                    }
                    if(empty($readersname) && empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT Readers.readers_name, GROUP_CONCAT(Subscriptions.subscriptions_id SEPARATOR ', ') AS subscriptions_id FROM Readers, Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id GROUP BY Readers.readers_name UNION SELECT Readers.readers_name, NULL AS subscriptions_id FROM Readers WHERE NOT EXISTS (SELECT Subscriptions.subscriptions_id FROM Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id)");
                    }
                    if(!empty($readersname) && empty($subscriptionsid) && $flagcn)
                    {
                        $query = mysqli_query($link, "SELECT Readers.readers_name, NULL AS subscriptions_id FROM Readers WHERE NOT EXISTS (SELECT Subscriptions.subscriptions_id FROM Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id) AND readers_name LIKE '%".$readersname."%'");
                    }
                    if(!empty($readersname) && empty($subscriptionsid) && $flagcn == false)
                    {
                        $query = mysqli_query($link, "SELECT Readers.readers_name, GROUP_CONCAT(Subscriptions.subscriptions_id SEPARATOR ', ') AS subscriptions_id FROM Readers, Subscriptions WHERE readers_name LIKE '%".$readersname."%' AND Readers.readers_id = Subscriptions.readers_id GROUP BY Readers.readers_name");
                    }
                    if(empty($readersname) && !empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT Readers.readers_name, GROUP_CONCAT(Subscriptions.subscriptions_id SEPARATOR ', ') AS subscriptions_id FROM Readers, Subscriptions WHERE Readers.readers_id IN (SELECT Subscriptions.readers_id FROM Subscriptions WHERE Subscriptions.subscriptions_id = ".$subscriptionsid.") AND Readers.readers_id = Subscriptions.readers_id GROUP BY Readers.readers_name");
                    }
                    if(!empty($readersname) && !empty($subscriptionsid))
                    {
                        $query = mysqli_query($link, "SELECT Readers.readers_name, Subscriptions.subscriptions_id FROM Readers, Subscriptions WHERE Readers.readers_id = Subscriptions.readers_id AND Readers.readers_name LIKE '%".$readersname."%' AND Subscriptions.subscriptions_id = ".$subscriptionsid."");
                    }
                    $countrow = mysqli_num_rows($query);
                    if($countrow < 1 || $_GET['subscriptions_id'] == "0")
                    {
                        echo "<p style = 'font-size: 20px'>Не найдено</p>";
                    }
                    else
                    {
                        echo "<h1>Таблица: Читатели</h1>";
                        echo "<h3>Количество позиций: ".$countrow."</h3>";
                        echo "<table border = '1'>
                        <tr style = 'text-align: center; font-weight: bold'>
                        <td>Имя читателя</td>
                        <td>Номер абонемента читателя</td>
                        </tr>";
                        for($i = 0; $i < $countrow; $i++)
                        {
                            $row = mysqli_fetch_row($query);
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
                        echo "</table>";
                    }
                }
            }
            echo "<form method = 'GET' enctype = 'multipart/form-data' name = 'filteru'>
            <p>Имя читателя:</p>
            <input type = 'text' name = 'readers_name'>
            <p>Номер абонемента читателя:</p>
            <input type = 'text' name = 'subscriptions_id'><br><br>
            <input type = 'submit' name = 'filterru' value = 'Отфильтровать'>
            </form>";
        }
    }
    else
    {
        echo "Вы не авторизовались<br><br>";
        echo "<a href = 'login.php'>Перейти на форму авторизации</a>";
    }
?>