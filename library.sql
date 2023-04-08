-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 10 2022 г., 21:38
-- Версия сервера: 5.7.38-log
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `library`
--

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `books_ISBN` char(17) NOT NULL,
  `books_name` char(75) NOT NULL,
  `books_author` char(75) NOT NULL,
  `books_publishing_year` int(4) NOT NULL,
  `providers_id` int(11) DEFAULT NULL,
  `books_pages_amount` int(4) NOT NULL,
  `books_publishing_office` char(75) NOT NULL,
  `books_is_handed_over` tinyint(1) NOT NULL,
  `books_photo` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`books_ISBN`, `books_name`, `books_author`, `books_publishing_year`, `providers_id`, `books_pages_amount`, `books_publishing_office`, `books_is_handed_over`, `books_photo`) VALUES
('111-1-1111-1111-1', 'Криштиану Роналду: одержимый совершенством', 'Жорже Мендеш', 2013, 3, 250, 'France Football', 1, 'files/Криштиану Роналду - одержимый совершенством.jpg'),
('123-4-5678-9012-3', 'Всадник без головы', 'Майн Рид', 2000, 3, 540, 'Лучшие художественные произведения', 1, 'files/Всадник без головы.jpg'),
('322-9-4678-1903-7', 'Таинственный остров', 'Жюль Верн', 2010, 4, 528, 'Зарубежная литература', 0, 'files/Таинственный остров.jpg'),
('777-7-7777-7777-7', 'Кортик', 'Анатолий Рыбаков', 2015, 3, 363, 'ФТМ', 1, 'files/Кортик.jpg'),
('993-1-4678-3023-5', 'Вокруг света за 80 дней', 'Жюль Верн', 2000, 1, 462, 'Астрель', 0, 'files/Вокруг света за 80 дней.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `librarycards`
--

CREATE TABLE `librarycards` (
  `library_cards_id` int(11) NOT NULL,
  `readers_id` int(11) DEFAULT NULL,
  `subscriptions_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `librarycards`
--

INSERT INTO `librarycards` (`library_cards_id`, `readers_id`, `subscriptions_id`) VALUES
(2, 29, 1),
(3, 2, 2),
(4, 35, 6),
(5, 41, 5),
(6, 36, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `providers`
--

CREATE TABLE `providers` (
  `providers_id` int(11) NOT NULL,
  `providers_name` char(75) NOT NULL,
  `providers_address` char(75) NOT NULL,
  `providers_phone` char(25) NOT NULL,
  `providers_email` char(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `providers`
--

INSERT INTO `providers` (`providers_id`, `providers_name`, `providers_address`, `providers_phone`, `providers_email`) VALUES
(1, 'ООО \"Мир книг\"', 'г.Краснодар, ул.Красная, 103', '+79882993434', 'booksworld@info.ru'),
(3, 'ООО \"Дом Книги\"', 'г.Краснодар, ул.Северная, 252', '+79993365859', 'domknigi@info.ru'),
(4, 'АО \"Поставщик книг\"', 'г.Ростов-на-Дону, ул.Казачья, 26', '+79738562467', 'booksprovider@info.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `readers`
--

CREATE TABLE `readers` (
  `readers_id` int(11) NOT NULL,
  `readers_name` char(75) NOT NULL,
  `readers_passport_series` char(4) DEFAULT NULL,
  `readers_passport_number` char(6) DEFAULT NULL,
  `readers_phone` char(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `readers`
--

INSERT INTO `readers` (`readers_id`, `readers_name`, `readers_passport_series`, `readers_passport_number`, `readers_phone`) VALUES
(2, 'Криштиану Роналду душ Сантуш Авейру', '7777', '777777', '+79777777777'),
(29, 'Акакий Акакиевич', '2299', '222999', '+79195833912'),
(35, 'Петров', '2341', '670833', '+79000000000'),
(36, 'Илья Муромец', '2952', '381444', '+70918364152'),
(41, 'Джейсон Стетхем', '0000', '000000', '+79882994232');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscriptions_id` int(11) NOT NULL,
  `readers_id` int(11) DEFAULT NULL,
  `subscriptions_registration_date` date NOT NULL,
  `books_ISBN` char(17) DEFAULT NULL,
  `subscriptions_extradition_date` date NOT NULL,
  `subscriptions_return_date` date NOT NULL,
  `workers_id` int(11) DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subscriptions`
--

INSERT INTO `subscriptions` (`subscriptions_id`, `readers_id`, `subscriptions_registration_date`, `books_ISBN`, `subscriptions_extradition_date`, `subscriptions_return_date`, `workers_id`, `is_closed`) VALUES
(1, 29, '2022-06-01', '993-1-4678-3023-5', '2022-06-01', '2022-06-14', 2, 1),
(2, 2, '2022-06-03', '322-9-4678-1903-7', '2022-06-03', '2022-06-16', 4, 1),
(5, 41, '2022-06-21', '123-4-5678-9012-3', '2022-06-21', '2022-07-04', 2, 0),
(6, 35, '2022-06-22', '777-7-7777-7777-7', '2022-06-22', '2022-07-05', 2, 0),
(7, 29, '2022-06-22', '111-1-1111-1111-1', '2022-06-22', '2022-07-05', 4, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `users_login` char(15) NOT NULL,
  `users_password` char(255) NOT NULL,
  `readers_phone` char(25) NOT NULL,
  `readers_id` int(11) DEFAULT NULL,
  `users_ip` char(25) NOT NULL,
  `users_hash` char(255) NOT NULL,
  `users_is_admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`users_id`, `users_login`, `users_password`, `readers_phone`, `readers_id`, `users_ip`, `users_hash`, `users_is_admin`) VALUES
(1, 'AL', '1533de7f57a3321f94173ba99094eb08', '+79117771717', NULL, '2130706433', '5a1d02c7f462da8792a6991cd48bb6cd', 1),
(7, 'CR7', '9208a20086ebfef780143c1fa4262e79', '+79777777777', 2, '2130706433', '993c676d4414d5eb2086ef0103df9226', 0),
(13, 'KK', '2c7a5a6bfa4b5baee3b981b7803c3747', '+79195833912', 29, '2130706433', '7061aad82abad2de6808d57ff46243b5', 0),
(19, 'LO', 'cf3754c3232c630896183654892df1c9', '+79000000000', 35, '2130706433', '8daa88b833208e74c9dcbb7ec78252a9', 0),
(21, 'IM', '28c8edde3d61a0411511d3b1866f0636', '+70', 36, '2130706433', '7765b28d1e91a7fa571aeb76269b94f5', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `workers`
--

CREATE TABLE `workers` (
  `workers_id` int(11) NOT NULL,
  `workers_name` char(75) NOT NULL,
  `workers_position` char(25) NOT NULL,
  `workers_phone` char(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `workers`
--

INSERT INTO `workers` (`workers_id`, `workers_name`, `workers_position`, `workers_phone`) VALUES
(1, 'Фролова Ольга Алексеевна', 'директор', '+79723692890'),
(2, 'Носова Юлия Владимировна', 'библиотекарь', '+79938874682'),
(3, 'Колин Кирилл Андреевич', 'охранник', '+79182994336'),
(4, 'Ильина Софья Валерьевна', 'библиотекарь', '+79863987321');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`books_ISBN`),
  ADD KEY `providers_id` (`providers_id`);

--
-- Индексы таблицы `librarycards`
--
ALTER TABLE `librarycards`
  ADD PRIMARY KEY (`library_cards_id`),
  ADD KEY `readers_id` (`readers_id`),
  ADD KEY `subscriptions_id` (`subscriptions_id`);

--
-- Индексы таблицы `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`providers_id`);

--
-- Индексы таблицы `readers`
--
ALTER TABLE `readers`
  ADD PRIMARY KEY (`readers_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscriptions_id`),
  ADD KEY `books_ISBN` (`books_ISBN`),
  ADD KEY `workers_id` (`workers_id`),
  ADD KEY `readers_id` (`readers_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `readers_id` (`readers_id`);

--
-- Индексы таблицы `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`workers_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `librarycards`
--
ALTER TABLE `librarycards`
  MODIFY `library_cards_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `providers`
--
ALTER TABLE `providers`
  MODIFY `providers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `readers`
--
ALTER TABLE `readers`
  MODIFY `readers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscriptions_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `workers`
--
ALTER TABLE `workers`
  MODIFY `workers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`providers_id`) REFERENCES `providers` (`providers_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `librarycards`
--
ALTER TABLE `librarycards`
  ADD CONSTRAINT `librarycards_ibfk_1` FOREIGN KEY (`readers_id`) REFERENCES `readers` (`readers_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `librarycards_ibfk_2` FOREIGN KEY (`subscriptions_id`) REFERENCES `subscriptions` (`subscriptions_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`books_ISBN`) REFERENCES `books` (`books_ISBN`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`workers_id`) REFERENCES `workers` (`workers_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_3` FOREIGN KEY (`readers_id`) REFERENCES `readers` (`readers_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`readers_id`) REFERENCES `readers` (`readers_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
