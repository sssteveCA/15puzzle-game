-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 12, 2021 alle 16:13
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
-- Struttura della tabella `giocatori`
--

DROP TABLE IF EXISTS `giocatori`;
CREATE TABLE `giocatori` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sequenza` varchar(100) DEFAULT NULL COMMENT 'ordine attuale dei numeri dentro la tessera',
  `tempo` varchar(30) DEFAULT NULL,
  `spostamenti` int(11) DEFAULT NULL,
  `record` bigint(20) DEFAULT NULL COMMENT 'partita con il tempo più basso in secondi',
  `codAut` varchar(100) DEFAULT NULL,
  `cambioPwd` varchar(64) DEFAULT NULL,
  `dataCambioPwd` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `giocatori`
--

INSERT INTO `giocatori` (`id`, `email`, `username`, `password`, `sequenza`, `tempo`, `spostamenti`, `record`, `codAut`, `cambioPwd`, `dataCambioPwd`) VALUES
(4, 'giocatore1@localhost.lan', 'Giocatore1', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 89500, NULL, NULL, NULL),
(5, 'giocatore2@localhost.lan', 'Giocatore2', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 79500, NULL, NULL, NULL),
(6, 'giocatore3@localhost.lan', 'Giocatore3', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 134067, NULL, NULL, NULL),
(7, 'giocatore4@localhost.lan', 'Giocatore4', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 4365, NULL, NULL, NULL),
(8, 'giocatore5@localhost.lan', 'Giocatore5', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 6700, NULL, NULL, NULL),
(9, 'giocatore6@localhost.lan', 'Giocatore6', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 2543, NULL, NULL, NULL),
(10, 'giocatore7@localhost.lan', 'Giocatore7', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 3469, NULL, NULL, NULL),
(11, 'giocatore8@localhost.lan', 'Giocatore8', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 17033, NULL, NULL, NULL),
(12, 'giocatore9@localhost.lan', 'Giocatore9', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 24862, NULL, NULL, NULL),
(13, 'giocatore10@localhost.lan', 'Giocatore10', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 103651, NULL, NULL, NULL),
(14, 'giocatore11@localhost.lan', 'Giocatore11', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 1986, NULL, NULL, NULL),
(15, 'giocatore12@localhost.lan', 'Giocatore12', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 3583, NULL, NULL, NULL),
(16, 'giocatore13@localhost.lan', 'Giocatore13', '$2y$10$GfH7755HS1SAP/5Wtg74u.kW6MEr32wSpm21PkODghAY8GW0q7Kcy', '1 14 15 9 4 11 7 8 vuoto 2 10 6 12 13 3 5', '0 0 0 36', 16, 1991, NULL, NULL, NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `giocatori`
--
ALTER TABLE `giocatori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `codAut` (`codAut`),
  ADD UNIQUE KEY `cambioPwd` (`cambioPwd`),
  ADD UNIQUE KEY `dataCambioPwd` (`dataCambioPwd`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `giocatori`
--
ALTER TABLE `giocatori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
