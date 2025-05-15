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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
  `centrale` varchar(500) NOT NULL,
  `base` varchar(500) NOT NULL,
  `apertura` varchar(500) NOT NULL,
  `percorso` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `prezzo` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prodotto_FK1` (`id_categoria`),
  CONSTRAINT `prodotto_FK1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dump dei dati della tabella ouparfum.prodotti: ~20 rows (circa)
DELETE FROM `prodotti`;
INSERT INTO `prodotti` (`id`, `nome`, `descrizione`, `centrale`, `base`, `apertura`, `percorso`, `id_categoria`, `prezzo`) VALUES
	(1, 'Selva Luminosa', 'Un viaggio nel cuore di una foresta rigogliosa e misteriosa, dove ogni respiro è carico di vita e profondità. Selva Luminosa evoca la potenza della natura con note fresche di foglie e resine, illuminate da una brezza agrumata che taglia la quiete del bosco. È un profumo che sprigiona libertà, istinto e radici profonde. Per l’uomo che ama sentirsi parte del mondo naturale, selvaggio ma armonioso.', 'Resina di pino, legno aromatico, salvia selvatica', 'Muschio di quercia, ambra verde, vetiver', 'Foglie verdi, agrumi luminosi, rugiada', 'immagini/prof1.png', 4, 49.99),
	(2, 'Ambra Sette', 'Calda, intensa, avvolgente come un tramonto dorato sulle dune. Ambra Sette è un omaggio alle molteplici sfumature dell’ambra: luminosa, profonda, balsamica e sensuale. Sette interpretazioni fuse in un solo profumo che accende i sensi e riscalda l’anima. Per la donna che ama il mistero, la profondità e la forza gentile del suo carisma.', 'Benzoino, labdano, resina dolce', 'Ambra grigia, vaniglia, legno di sandalo', 'Ambra dorata, bergamotto siciliano', 'immagini/prof2.png', 3, 39.99),
	(3, 'Pétale de Lune', 'Un bouquet lunare, delicato ma magnetico, come una notte piena di sogni. Pétale de Lune è una danza tra mandorla, fiori bianchi e muschio, dove ogni nota accarezza la pelle come un petalo bagnato di rugiada. È il profumo delle emozioni pure, delle carezze silenziose e dei pensieri sospesi sotto un cielo stellato.', 'Gelsomino sambac, tuberosa, ylang-ylang', 'Muschio bianco, vaniglia vellutata, legno chiaro', 'Mandorla dolce, fiore d’arancio', 'immagini/prof3.png', 7, 69.99),
	(4, 'Vanille Sauvage', 'Una vaniglia non addomesticata, profonda e passionale, che si contamina con spezie orientali e legni caldi. Vanille Sauvage è una fragranza che avvolge e seduce con il suo contrasto tra dolcezza e intensità. Per chi ama la vaniglia più audace, carnale e ruvida. Un profumo che risveglia i sensi e lascia il segno.', 'Cannella, noce moscata, fava tonka', 'Legno affumicato, patchouli, ambra scura', 'Vaniglia pura, pepe rosa, cardamomo', 'immagini/prof4.png', 1, 34.99),
	(5, 'Fleur de Givre', 'Fleur de Givre è il ritratto di un fiore cristallizzato, nato dal gelo e dalla luce. Una fragranza eterea, pulita e raffinata, che unisce la freschezza dell’acqua con la delicatezza dei fiori bianchi. È come un soffio d’aria fredda che accarezza la pelle in un mattino d’inverno. Per la donna elegante, luminosa e dal cuore trasparente.', 'Fiore di loto, petali di peonia, mughetto', 'Muschio bianco, ambra cristallina, legno di cedro', 'Accordo ghiacciato, limone di Sicilia, menta leggera', 'immagini/prof5.png', 7, 79.99),
	(6, 'Acqua di Roccia', 'Fresco come un’onda che si infrange su scogli selvaggi, Acqua di Roccia è una sinfonia salmastra, limpida e virile. I suoi accordi ozonati e minerali evocano la forza degli elementi: vento, mare e pietra. Un profumo per l’uomo libero, dinamico, che vive ogni giorno come un’avventura.', 'Alghe verdi, lavanda, salvia', 'Ambroxan, legno di cedro, minerali umidi', 'Sale marino, bergamotto, foglie di fico', 'immagini/prof6.png', 3, 36.99),
	(7, 'Cuoio Nero', ' Oscuro e magnetico, Cuoio Nero racconta la potenza primordiale della pelle e del legno. Una fragranza maschile e carismatica, dal carattere deciso, perfetta per chi non teme di lasciare il segno. Ruvido, profondo, indimenticabile.', ' Patchouli, incenso, noce moscata', 'Oud, vetiver, muschio animale\r\n', 'Cuoio scuro, pepe nero, salvia', 'immagini/prof7.png', 4, 47.99),
	(8, 'Iris Absolu', 'Eleganza pura e delicata, Iris Absolu è una carezza cipriata sulla pelle. L’iride vellutato si unisce a note morbide e nobili, evocando una bellezza senza tempo. Per la donna raffinata e silenziosamente intensa.', 'Iris toscano, rosa, eliotropio', 'Muschio bianco, legno di cashmere, vaniglia leggera', 'Mandarino, neroli, foglie di violetta', 'immagini/prof8.png', 7, 65.99),
	(9, 'Rouge Cerise', 'Una ciliegia scarlatta matura e succosa si fonde con fiori sensuali e muschi bianchi. Rouge Cerise è vivace, seducente, maliziosa. Per chi ama i profumi dolci ma mai banali, con un tocco di audacia.', 'Rosa rossa, gelsomino, mandorla', 'Vaniglia, muschio, ambra', 'Ciliegia nera, lampone, bergamotto', 'immagini/prof9.png', 1, 89.99),
	(10, 'Sole di Zenzero', ' Un’esplosione di energia calda e speziata. Sole di Zenzero è come una mattina assolata su una terrazza mediterranea: vivace, tonificante, irresistibile. Per l’uomo solare e dinamico, che lascia dietro di sé una scia luminosa.', 'Cardamomo, geranio, pepe rosa', ' Vetiver, legno di sandalo, muschio', 'Zenzero fresco, lime, arancia amara', 'immagini/prof10.png', 2, 65.99),
	(11, 'Limonata Bianca', 'Frizzante come una risata estiva, Limonata Bianca è pura gioia in bottiglia. Una fragranza agrumata, trasparente e dissetante, perfetta per chi vuole portare il sole ovunque vada.', ' Tè bianco, fiore di limone, gelsomino', ' Muschio, legno chiaro, zucchero a velo', 'Limone di Amalfi, lime, cedro', 'immagini/prof11.png', 5, 49.99),
	(12, 'Notte d’Ambra', 'Un profumo sensuale e misterioso come una notte stellata nel deserto. Notte d’Ambra avvolge la pelle con calore, dolcezza e fascino orientale. Per la donna magnetica, profonda, sicura del proprio potere', 'Ambra, rosa damascena, mirra', 'Oud, vaniglia, incenso', 'Prugna nera, zafferano, mandarino', 'immagini/prof12.png', 3, 39.99),
	(13, 'Ghiaccio Puro', 'Pulito, tagliente, cristallino. Ghiaccio Puro è una ventata d’aria fredda, moderna e decisa. Per l’uomo elegante, minimale, che ama la precisione e l’essenzialità.', 'Lavanda, foglie verdi, iris freddo', 'Ambroxan, muschio trasparente, legno secco', 'Menta glaciale, aldeidi, pompelmo', 'immagini/prof13.png', 7, 47.99),
	(14, 'Rosa Incantata', 'Una rosa perfetta, poetica, senza tempo. Rosa Incantata è una sinfonia romantica e sofisticata che celebra la regina dei fiori in tutta la sua eleganza. Per chi sogna con grazia e vive con stile.', 'Rosa turca, peonia, violetta', 'Muschio bianco, legno biondo, ambra soft', ' Rosa di maggio, litchi, pepe rosa', 'immagini/prof14.png', 8, 59.99),
	(15, 'Verde Tè', 'Leggero, naturale, purificante. Verde Tè è come una passeggiata all’alba tra foglie umide e aria fresca. Una fragranza rigenerante per l’uomo calmo e consapevole.', 'Gelsomino, bambù, fiori d’acqua', 'Muschio pulito, legno di betulla, ambra verde', 'Tè verde, limone, menta', 'immagini/prof15.png', 5, 49.99),
	(16, 'Cocco & Fiori', 'Un tuffo nei tropici tra spiagge bianche, fiori esotici e cocco cremoso. Cocco & Fiori è solare, giocoso e irresistibilmente vacanziero. Per la donna che vuole profumare d’estate tutto l’anno.', 'Tiaré, ylang-ylang, gelsomino', 'Vaniglia, muschio solare, ambra dolce', 'Cocco fresco, bergamotto, ananas', 'immagini/prof16.png', 1, 69.99),
	(17, 'Bosco Antico', 'Rustico, nobile, intenso. Bosco Antico racconta il fascino silenzioso degli alberi secolari. Un profumo per l’uomo maturo, radicato, che sa ascoltare la natura.', 'Muschio di quercia, vetiver, pepe nero', ' Legno di cedro, ambra verde, patchouli', 'Cipresso, aghi di pino, pompelmo', 'immagini/prof17.png', 3, 47.99),
	(18, 'Talco Rosa', ' Morbido, nostalgico, tenero. Talco Rosa è un abbraccio d’altri tempi, fatto di polvere profumata e fiori delicati. Per chi ama sentirsi coccolata.', 'Iris, lavanda, fiore di cotone', 'Talco, muschio bianco, vaniglia', 'Rosa, eliotropio, violetta', 'immagini/prof18.png', 7, 47.99),
	(19, 'Nuvola di Cotone', ' Una nuvola soffice e pulita, che sa di bucato fresco e cielo sereno. Nuvola di Cotone è una fragranza delicata, rassicurante, intima. Per la donna sensibile e autentica.', 'Mughetto, fiori bianchi, muschio pulito', 'Vaniglia leggera, ambra bianca, legno soft', 'Aldeidi, fiore di cotone, pera', 'immagini/prof19.png', 7, 69.99),
	(20, 'Spezia d’Oriente', ' Calore, mistero, avventura. Spezia d’Oriente è un viaggio sensoriale tra mercati lontani e legni preziosi. Una fragranza maschile decisa, esotica e affascinante. Per chi ama distinguersi.', 'Zafferano, patchouli, incenso', 'Oud, cuoio, ambra scura', 'Cannella, pepe nero, cardamomo', 'immagini/prof20.png', 3, 34.99);

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
