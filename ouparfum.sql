-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              11.6.2-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database ouparfum
CREATE DATABASE IF NOT EXISTS `ouparfum` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `ouparfum`;

-- Dump della struttura di tabella ouparfum.categoria
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aroma` varchar(50) NOT NULL,
  `sesso` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.categoria: ~8 rows (circa)
INSERT INTO `categoria` (`id`, `aroma`, `sesso`) VALUES
	(1, 'Fruttato', 'Femminile'),
	(2, 'Fruttato', 'Maschile'),
	(3, 'Legnoso', 'Femminile'),
	(4, 'Legnoso', 'Maschile'),
	(5, 'Agrumato', 'Femminile'),
	(6, 'Agrumato', 'Maschile'),
	(7, 'Floreale', 'Femminile'),
	(8, 'Floreale', 'Maschile');

-- Dump della struttura di tabella ouparfum.dettagli
CREATE TABLE IF NOT EXISTS `dettagli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ordine` int(11) NOT NULL,
  `id_prodotto` int(11) NOT NULL,
  `quantita` int(50) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `dettagli_FK1` (`id_ordine`),
  KEY `dettagli_FK2` (`id_prodotto`),
  CONSTRAINT `dettagli_FK1` FOREIGN KEY (`id_ordine`) REFERENCES `ordine` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `dettagli_FK2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.dettagli: ~0 rows (circa)

-- Dump della struttura di tabella ouparfum.ordine
CREATE TABLE IF NOT EXISTS `ordine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `citta` varchar(50) DEFAULT NULL,
  `codice postale` varchar(5) DEFAULT NULL,
  `provincia` varchar(2) DEFAULT NULL,
  `via` varchar(50) DEFAULT NULL,
  `civico` varchar(50) DEFAULT NULL,
  `stato` char(1) NOT NULL DEFAULT 'c',
  `id_dettagli` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordine_FK1` (`id_dettagli`),
  KEY `ordine_FK2` (`id_utente`),
  CONSTRAINT `ordine_FK1` FOREIGN KEY (`id_dettagli`) REFERENCES `dettagli` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `ordine_FK2` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.ordine: ~0 rows (circa)

-- Dump della struttura di tabella ouparfum.prodotto
CREATE TABLE IF NOT EXISTS `prodotto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descrizione` varchar(1000) NOT NULL,
  `base` varchar(500) NOT NULL,
  `centrale` varchar(500) NOT NULL,
  `apertura` varchar(500) NOT NULL,
  `percorso` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.prodotto: ~0 rows (circa)

-- Dump della struttura di tabella ouparfum.utente
CREATE TABLE IF NOT EXISTS `utente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pw` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.utente: ~0 rows (circa)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
