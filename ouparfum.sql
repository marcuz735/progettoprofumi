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
DROP DATABASE IF EXISTS `ouparfum`;
CREATE DATABASE IF NOT EXISTS `ouparfum` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `ouparfum`;

-- Dump della struttura di tabella ouparfum.categoria
DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aroma` varchar(50) NOT NULL,
  `genere` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.categoria: ~8 rows (circa)
DELETE FROM `categoria`;
INSERT INTO `categoria` (`id`, `aroma`, `genere`) VALUES
	(1, 'Fruttato', 'Femminile'),
	(2, 'Fruttato', 'Maschile'),
	(3, 'Legnoso', 'Femminile'),
	(4, 'Legnoso', 'Maschile'),
	(5, 'Agrumato', 'Femminile'),
	(6, 'Agrumato', 'Maschile'),
	(7, 'Floreale', 'Femminile'),
	(8, 'Floreale', 'Maschile');

-- Dump della struttura di tabella ouparfum.dettagli
DROP TABLE IF EXISTS `dettagli`;
CREATE TABLE IF NOT EXISTS `dettagli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ordine` int(11) NOT NULL,
  `id_prodotto` int(11) NOT NULL,
  `quantita` int(50) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `dettagli_FK1` (`id_ordine`),
  KEY `dettagli_FK2` (`id_prodotto`),
  CONSTRAINT `dettagli_FK1` FOREIGN KEY (`id_ordine`) REFERENCES `ordini` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `dettagli_FK2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.dettagli: ~0 rows (circa)
DELETE FROM `dettagli`;

-- Dump della struttura di tabella ouparfum.ordini
DROP TABLE IF EXISTS `ordini`;
CREATE TABLE IF NOT EXISTS `ordini` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `citta` varchar(50) DEFAULT NULL,
  `cap` varchar(5) DEFAULT NULL,
  `provincia` varchar(2) DEFAULT NULL,
  `via` varchar(50) DEFAULT NULL,
  `civico` varchar(50) DEFAULT NULL,
  `stato` char(1) NOT NULL DEFAULT 'c',
  `id_utente` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `ordine_FK2` (`id_utente`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.ordini: ~1 rows (circa)
DELETE FROM `ordini`;
INSERT INTO `ordini` (`id`, `citta`, `cap`, `provincia`, `via`, `civico`, `stato`, `id_utente`) VALUES
	(2, NULL, NULL, NULL, NULL, NULL, 'c', 3);

-- Dump della struttura di tabella ouparfum.prodotti
DROP TABLE IF EXISTS `prodotti`;
CREATE TABLE IF NOT EXISTS `prodotti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descrizione` varchar(1000) NOT NULL,
  `base` varchar(500) NOT NULL,
  `centrale` varchar(500) NOT NULL,
  `apertura` varchar(500) NOT NULL,
  `percorso` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `prezzo` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prodotto_FK1` (`id_categoria`),
  CONSTRAINT `prodotto_FK1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.prodotti: ~5 rows (circa)
DELETE FROM `prodotti`;
INSERT INTO `prodotti` (`id`, `nome`, `descrizione`, `base`, `centrale`, `apertura`, `percorso`, `id_categoria`, `prezzo`) VALUES
	(1, 'Selva Luminosa', 'Un viaggio nel cuore di una foresta rigogliosa e misteriosa, dove ogni respiro è carico di vita e profondità. Selva Luminosa evoca la potenza della natura con note fresche di foglie e resine, illuminate da una brezza agrumata che taglia la quiete del bosco. È un profumo che sprigiona libertà, istinto e radici profonde. Per l’uomo che ama sentirsi parte del mondo naturale, selvaggio ma armonioso.', 'Muschio di quercia, ambra verde, vetiver', 'Resina di pino, legno aromatico, salvia selvatica', 'Foglie verdi, agrumi luminosi, rugiada', 'immagini/prof1.png', 4, 49.99),
	(2, 'Ambra Sette', 'Calda, intensa, avvolgente come un tramonto dorato sulle dune. Ambra Sette è un omaggio alle molteplici sfumature dell’ambra: luminosa, profonda, balsamica e sensuale. Sette interpretazioni fuse in un solo profumo che accende i sensi e riscalda l’anima. Per la donna che ama il mistero, la profondità e la forza gentile del suo carisma.', 'Ambra grigia, vaniglia, legno di sandalo', 'Benzoino, labdano, resina dolce', 'Ambra dorata, bergamotto siciliano', 'immagini/prof2.png', 3, 39.99),
	(3, 'Pétale de Lune', 'Un bouquet lunare, delicato ma magnetico, come una notte piena di sogni. Pétale de Lune è una danza tra mandorla, fiori bianchi e muschio, dove ogni nota accarezza la pelle come un petalo bagnato di rugiada. È il profumo delle emozioni pure, delle carezze silenziose e dei pensieri sospesi sotto un cielo stellato.', 'Muschio bianco, vaniglia vellutata, legno chiaro', 'Gelsomino sambac, tuberosa, ylang-ylang', 'Mandorla dolce, fiore d’arancio', 'immagini/prof3.png', 7, 69.99),
	(4, 'Vanille Sauvage', 'Una vaniglia non addomesticata, profonda e passionale, che si contamina con spezie orientali e legni caldi. Vanille Sauvage è una fragranza che avvolge e seduce con il suo contrasto tra dolcezza e intensità. Per chi ama la vaniglia più audace, carnale e ruvida. Un profumo che risveglia i sensi e lascia il segno.', 'Legno affumicato, patchouli, ambra scura', 'Cannella, noce moscata, fava tonka', 'Vaniglia pura, pepe rosa, cardamomo', 'immagini/prof4.png', 1, 34.99),
	(5, 'Fleur de Givre', 'Fleur de Givre è il ritratto di un fiore cristallizzato, nato dal gelo e dalla luce. Una fragranza eterea, pulita e raffinata, che unisce la freschezza dell’acqua con la delicatezza dei fiori bianchi. È come un soffio d’aria fredda che accarezza la pelle in un mattino d’inverno. Per la donna elegante, luminosa e dal cuore trasparente.', 'Muschio bianco, ambra cristallina, legno di cedro', 'Fiore di loto, petali di peonia, mughetto', 'Accordo ghiacciato, limone di Sicilia, menta leggera', 'immagini/prof5.png', 7, 79.99);

-- Dump della struttura di tabella ouparfum.utenti
DROP TABLE IF EXISTS `utenti`;
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(50) NOT NULL,
  `pw` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.utenti: ~1 rows (circa)
DELETE FROM `utenti`;
INSERT INTO `utenti` (`id`, `mail`, `pw`) VALUES
	(3, 'cavina.sara06@gmail.com', '$2y$12$XLoCxZVtUPu9ERxzyyGlzeTy0zaN4NEVBoR3FXWnmWB.D7XjPl0by');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
