-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 16, 2021 alle 15:27
-- Versione del server: 10.4.17-MariaDB
-- Versione PHP: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stefano`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `salvataggi`
--

DROP TABLE IF EXISTS `salvataggi`;
CREATE TABLE `salvataggi` (
  `id` int(11) NOT NULL,
  `idg` int(11) NOT NULL COMMENT 'id del giocatore a cui appartiene il salvataggio',
  `data` datetime NOT NULL COMMENT 'data in cui il salvataggio è stato creato',
  `slot` int(11) NOT NULL COMMENT 'dove il giocatore vuole salvare la partita',
  `sequenza` varchar(100) NOT NULL COMMENT 'posizione di ciascuna tessera',
  `tempo` varchar(30) NOT NULL COMMENT 'tempo passato pfino al momento in cui  stato creato il salvataggio',
  `spostamenti` int(11) NOT NULL COMMENT 'numero di tessere spostate fino al momento in cui è stato creato il salvataggio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `salvataggi`
--

INSERT INTO `salvataggi` (`id`, `idg`, `data`, `slot`, `sequenza`, `tempo`, `spostamenti`) VALUES
(5, 4, '2021-04-16 14:44:19', 2, '5 vuoto 11 9 6 7 1 14 2 8 12 15 10 4 3 13', '0 0 0 5', 2),
(6, 4, '2021-04-16 13:42:00', 4, '5 14 9 4 2 1 3 15 13 12 11 7 vuoto 10 8 6', '0 0 0 11', 5),
(7, 4, '2021-04-16 14:47:00', 1, '5 7 11 9 6 1 14 15 2 8 12 13 10 4 vuoto 3', '0 0 0 13', 6),
(8, 4, '2021-04-16 14:47:09', 3, '5 7 11 9 6 1 14 15 2 8 12 13 10 4 vuoto 3', '0 0 0 16', 6),
(9, 4, '2021-04-16 14:45:56', 5, '5 7 11 9 6 8 1 14 2 12 3 15 10 4 vuoto 13', '0 0 0 7', 4);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `salvataggi`
--
ALTER TABLE `salvataggi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `salvataggi`
--
ALTER TABLE `salvataggi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
