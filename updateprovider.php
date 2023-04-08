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
            echo "<p><a href = 'index.html'>Главная</a> > <a href = 'cabinet.php'>Личный кабинет</a> > <a href = 'tables.php'>Таблицы</a> > <a href = 'providers.php'>Поставщики</a> > <a>Редактирование информации о поставщике</a></p>";
            if(isset($_POST['submit']))
            {
                $providersid = $_POST['providers_id'];
                $providersname = $_POST['providers_name'];
                $providersaddress = $_POST['providers_address'];
                $providersphone = $_POST['providers_phone'];
                $providersemail = $_POST['providers_email'];
                $err = array();
                $flagforheader;
                if(!preg_match("/^[0-9]+$/", $_POST['providers_id']) && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор поставщика может состоять только из цифр!<br>";
                }
                if(iconv_strlen($_POST['providers_id'], 'UTF-8') > 11 && $_POST['providers_id'] != "")
                {
                    $err = " ";
                    echo "Идентификатор поставщика может быть длиной не более 11 символов!<br>";
                }
                if(!preg_match("/^[a-zA-ZА-Яа-яЁё\" ]+$/u", $_POST['providers_name']) && $_POST['providers_name'] != "")
                {
                    $err = " ";
                    echo "Название поставщика может состоять только из букв английского и русского алфавитов и знака \"!<br>";
                }
                if((iconv_strlen($_POST['providers_name'], 'UTF-8') < 2 or iconv_strlen($_POST['providers_name'], 'UTF-8') > 75) && $_POST['providers_name'] != "")
                {
                    $err = " ";
                    echo "Название поставщика может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[-А-Яа-яЁё0-9., ]+$/u", $_POST['providers_address']) && $_POST['providers_address'] != "")
                {
                    $err = " ";
                    echo "Адрес поставщика может состоять только из букв русского алфавита, цифр, точек, запятых и дефисов!<br>";
                }
                if((iconv_strlen($_POST['providers_address'], 'UTF-8') < 2 or iconv_strlen($_POST['providers_address'], 'UTF-8') > 75) && $_POST['providers_address'] != "")
                {
                    $err = " ";
                    echo "Адрес поставщика может быть длиной от 2 до 75 символов!<br>";
                }
                if(!preg_match("/^[0-9+]+$/", $_POST['providers_phone']) && $_POST['providers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона поставщика может состоять только из цифр и знака \"+\"!<br>";
                }
                if((iconv_strlen($_POST['providers_phone'], 'UTF-8') < 4 or iconv_strlen($_POST['providers_phone'], 'UTF-8') > 25) && $_POST['providers_phone'] != "")
                {
                    $err = " ";
                    echo "Номер телефона поставщика может быть длиной от 4 до 25 символов!<br>";
                }
                if(!preg_match("/^[a-zA-Z0-9@. ]+$/", $_POST['providers_email']) && $_POST['providers_email'] != "")
                {
                    $err = " ";
                    echo "Email поставщика может состоять только из букв английского алфавита, цифр, точек и знака \"@\"!<br>";
                }
                if((iconv_strlen($_POST['providers_email'], 'UTF-8') < 6 or iconv_strlen($_POST['providers_email'], 'UTF-8') > 25) && $_POST['providers_email'] != "")
                {
                    $err = " ";
                    echo "Email поставщика может быть длиной от 6 до 25 символов!<br>";
                }
                if(count($err) == 0)
                {
                    $queryforcheckid = mysqli_query($link, "SELECT providers_id FROM Providers WHERE providers_id = '".$providersid."'");
                    $countrowid = mysqli_num_rows($queryforcheckid);
                    $flagforheader = true;
                    if($countrowid != 1 && $_POST['providers_id'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Введённый идентификатор поставщика не соответствует ни одному из существующих!</p>";
                    }
                    else if(($_POST['providers_name'] != "" || $_POST['providers_address'] != "" || $_POST['providers_phone'] != "" || $_POST['providers_email'] != "") && $_POST['providers_id'] == "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели идентификатор поставщика</p>";
                    }
                    else if($_POST['providers_name'] == "" && $_POST['providers_address'] == "" && $_POST['providers_phone'] == "" && $_POST['providers_email'] == "" && $_POST['providers_id'] != "")
                    {
                        $flagforheader = false;
                        echo "<p style = 'font-size: 20px'>Вы не ввели никаких значений</p>";
                    }
                    else
                    {
                        if(empty($providersid) && empty($providersname) && empty($providersaddress) && empty($providersphone) && empty($providersemail))
                        {
                            $flagforheader = false;
                            echo "<p style = 'font-size: 20px'>Данные не были введены</p>";
                        }

                        if(!empty($providersname) && empty($providersaddress) && empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."' WHERE providers_id = ".$providersid."");
                        }
                        if(empty($providersname) && !empty($providersaddress) && empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_address = '".$providersaddress."' WHERE providers_id = ".$providersid."");
                        }
                        if(empty($providersname) && empty($providersaddress) && !empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_phone = '".$providersphone."' WHERE providers_id = ".$providersid."");
                        }
                        if(empty($providersname) && empty($providersaddress) && empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(!empty($providersname) && !empty($providersaddress) && empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_address = '".$providersaddress."' WHERE providers_id = ".$providersid."");
                        }
                        if(!empty($providersname) && empty($providersaddress) && !empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_phone = '".$providersphone."' WHERE providers_id = ".$providersid."");
                        }
                        if(!empty($providersname) && empty($providersaddress) && empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(empty($providersname) && !empty($providersaddress) && !empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_address = '".$providersaddress."', providers_phone = '".$providersphone."' WHERE providers_id = ".$providersid."");
                        }
                        if(empty($providersname) && !empty($providersaddress) && empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_address = '".$providersaddress."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(empty($providersname) && empty($providersaddress) && !empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_phone = '".$providersphone."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(!empty($providersname) && !empty($providersaddress) && !empty($providersphone) && empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_address = '".$providersaddress."', providers_phone = '".$providersphone."' WHERE providers_id = ".$providersid."");
                        }
                        if(!empty($providersname) && !empty($providersaddress) && empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_address = '".$providersaddress."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(!empty($providersname) && empty($providersaddress) && !empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_phone = '".$providersphone."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(empty($providersname) && !empty($providersaddress) && !empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_address = '".$providersaddress."', providers_phone = '".$providersphone."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }

                        if(!empty($providersname) && !empty($providersaddress) && !empty($providersphone) && !empty($providersemail))
                        {
                            $queryforupdate = mysqli_query($link, "UPDATE Providers SET providers_name = '".$providersname."', providers_address = '".$providersaddress."', providers_phone = '".$providersphone."', providers_email = '".$providersemail."' WHERE providers_id = ".$providersid."");
                        }
                    }
                }
                if($flagforheader)
                {
                    header("Location: providers.php");
                }
            }
            if(count($err) > 0)
            {
                echo "<br><br>Введите идентификатор поставщика, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'providers_id'><br><br>
                <hr style = 'max-width: 544px; margin: 0px'>
                <p>Введите название поставщика:</p><input type = 'text' name = 'providers_name'>
                <p>Введите адрес поставщика:</p><input type = 'text' name = 'providers_address'>
                <p>Введите номер телефона поставщика:</p><input type = 'text' name = 'providers_phone'>
                <p>Введите email поставщика:</p><input type = 'text' name = 'providers_email'><br><br>
                <input type = 'submit' name = 'submit' value = 'Изменить'>
                </form>";
            }
            else
            {
                echo "Введите идентификатор поставщика, а затем значения, которые нужно изменить<br><br>";
                echo "<form method = 'POST' enctype = 'multipart/form-data'>
                <input type = 'text' name = 'providers_id'><br><br>
                <hr style = 'max-width: 544px; margin: 0px'>
                <p>Введите название поставщика:</p><input type = 'text' name = 'providers_name'>
                <p>Введите адрес поставщика:</p><input type = 'text' name = 'providers_address'>
                <p>Введите номер телефона поставщика:</p><input type = 'text' name = 'providers_phone'>
                <p>Введите email поставщика:</p><input type = 'text' name = 'providers_email'><br><br>
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