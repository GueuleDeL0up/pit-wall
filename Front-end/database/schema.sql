-- ============================================================
-- Alpine Pitwall — Schéma base de données
-- Encodage : utf8mb4 | Moteur : InnoDB
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS alpine_pitwall
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE alpine_pitwall;

-- ------------------------------------------------------------
-- Circuits
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS circuits (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  track_id    VARCHAR(20)  NOT NULL UNIQUE,
  name        VARCHAR(100) NOT NULL,
  location    VARCHAR(100) NOT NULL,
  flag_emoji  VARCHAR(20)  DEFAULT NULL,
  latitude    DECIMAL(10,6) DEFAULT NULL,
  longitude   DECIMAL(10,6) DEFAULT NULL,
  group_name  VARCHAR(60)  DEFAULT 'Calendrier 2026',
  created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Pilotes Alpine
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS drivers (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  number              INT          NOT NULL,
  name                VARCHAR(100) NOT NULL,
  nationality         VARCHAR(60)  DEFAULT NULL,
  flag_url            VARCHAR(255) DEFAULT NULL,
  points              INT          DEFAULT 0,
  championship_pos    INT          DEFAULT NULL,
  age                 INT          DEFAULT NULL,
  car                 VARCHAR(20)  DEFAULT 'A526',
  img_url             VARCHAR(500) DEFAULT NULL,
  live_fuel           INT          DEFAULT 0,
  live_brake          INT          DEFAULT 0,
  live_ers            INT          DEFAULT 0,
  live_engine_status  VARCHAR(10)  DEFAULT 'OK',
  created_at          TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Courses
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS races (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  round         INT          NOT NULL,
  flag_emoji    VARCHAR(20)  DEFAULT NULL,
  gp_name       VARCHAR(100) NOT NULL,
  circuit_name  VARCHAR(100) DEFAULT NULL,
  race_date     VARCHAR(30)  DEFAULT NULL,
  winner_name   VARCHAR(100) DEFAULT NULL,
  winner_team   VARCHAR(100) DEFAULT NULL,
  pole_position VARCHAR(100) DEFAULT NULL,
  fastest_lap   VARCHAR(100) DEFAULT NULL,
  alpine_result VARCHAR(255) DEFAULT NULL,
  created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Résultats top 5 par course
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS race_results (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  race_id     INT          NOT NULL,
  position    INT          NOT NULL,
  driver_name VARCHAR(100) DEFAULT NULL,
  team        VARCHAR(100) DEFAULT NULL,
  FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Classement pilotes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS driver_standings (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  position         INT          NOT NULL,
  driver_name      VARCHAR(100) NOT NULL,
  nationality_flag VARCHAR(20)  DEFAULT NULL,
  team             VARCHAR(100) DEFAULT NULL,
  points           INT          DEFAULT 0,
  updated_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Classement constructeurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS constructor_standings (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  position   INT          NOT NULL,
  team       VARCHAR(100) NOT NULL,
  points     INT          DEFAULT 0,
  updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Lectures capteur température
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sensor_readings (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  value       DECIMAL(5,2) NOT NULL,
  circuit_id  INT          DEFAULT NULL,
  recorded_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (circuit_id) REFERENCES circuits(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- États LEDs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS led_states (
  id         INT          PRIMARY KEY,
  color      VARCHAR(20)  NOT NULL,
  label      VARCHAR(60)  DEFAULT 'À définir',
  is_on      TINYINT(1)   DEFAULT 0,
  updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Circuits calendrier 2026
INSERT INTO circuits (track_id, name, location, flag_emoji, latitude, longitude, group_name) VALUES
('au-1953','Albert Park','Melbourne, Australie','🇦🇺',-37.849700,144.968000,'Calendrier 2026'),
('cn-2004','Shanghai International','Shanghai, Chine','🇨🇳',31.338900,121.219700,'Calendrier 2026'),
('jp-1962','Suzuka','Suzuka, Japon','🇯🇵',34.843100,136.541900,'Calendrier 2026'),
('us-2022','Miami Autodrome','Miami, USA','🇺🇸',25.958000,-80.238900,'Calendrier 2026'),
('it-1953','Imola','Imola, Italie','🇮🇹',44.343900,11.716700,'Calendrier 2026'),
('mc-1929','Circuit de Monaco','Monte-Carlo','🇲🇨',43.734700,7.420600,'Calendrier 2026'),
('ca-1978','Gilles-Villeneuve','Montréal, Canada','🇨🇦',45.500600,-73.522600,'Calendrier 2026'),
('es-1991','Catalunya','Barcelone, Espagne','🇪🇸',41.570000,2.261100,'Calendrier 2026'),
('es-2026','Madring','Madrid, Espagne','🇪🇸',40.471900,-3.563900,'Calendrier 2026'),
('at-1969','Red Bull Ring','Spielberg, Autriche','🇦🇹',47.219700,14.764700,'Calendrier 2026'),
('gb-1948','Silverstone','Silverstone, GB','🇬🇧',52.078600,-1.016900,'Calendrier 2026'),
('be-1925','Spa-Francorchamps','Spa, Belgique','🇧🇪',50.437200,5.971400,'Calendrier 2026'),
('hu-1986','Hungaroring','Budapest, Hongrie','🇭🇺',47.582800,19.251100,'Calendrier 2026'),
('nl-1948','Zandvoort','Zandvoort, Pays-Bas','🇳🇱',52.388800,4.540900,'Calendrier 2026'),
('it-1922','Monza','Monza, Italie','🇮🇹',45.615600,9.281100,'Calendrier 2026'),
('az-2016','Baku City','Bakou, Azerbaïdjan','🇦🇿',40.372500,49.853300,'Calendrier 2026'),
('sg-2008','Marina Bay','Singapour','🇸🇬',1.291400,103.864300,'Calendrier 2026'),
('us-2012','COTA','Austin, USA','🇺🇸',30.132800,-97.641100,'Calendrier 2026'),
('mx-1962','Hermanos Rodriguez','Mexico, Mexique','🇲🇽',19.404200,-99.090700,'Calendrier 2026'),
('br-1940','Interlagos','São Paulo, Brésil','🇧🇷',-23.703600,-46.699700,'Calendrier 2026'),
('us-2023','Las Vegas Strip','Las Vegas, USA','🇺🇸',36.114700,-115.172800,'Calendrier 2026'),
('qa-2004','Lusail','Doha, Qatar','🇶🇦',25.490000,51.454200,'Calendrier 2026'),
('ae-2009','Yas Marina','Abu Dhabi, EAU','🇦🇪',24.467200,54.603100,'Calendrier 2026'),
-- Circuits historiques
('bh-2002','Bahrain International','Sakhir, Bahreïn','🇧🇭',26.032500,50.510600,'Circuits historiques'),
('sa-2021','Jeddah Corniche','Djeddah, Arabie Saoudite','🇸🇦',21.631900,39.104400,'Circuits historiques'),
('de-1927','Nürburgring','Nurburg, Allemagne','🇩🇪',50.335600,6.947500,'Circuits historiques'),
('de-1932','Hockenheim','Hockenheim, Allemagne','🇩🇪',49.327800,8.565600,'Circuits historiques'),
('pt-1972','Estoril','Estoril, Portugal','🇵🇹',38.750600,-9.394200,'Circuits historiques'),
('pt-2008','Algarve (Portimão)','Portimão, Portugal','🇵🇹',37.227200,-8.626700,'Circuits historiques'),
('fr-1960','Magny-Cours','Magny-Cours, France','🇫🇷',46.864200,3.163300,'Circuits historiques'),
('fr-1969','Paul Ricard','Le Castellet, France','🇫🇷',43.250600,5.791700,'Circuits historiques'),
('my-1999','Sepang','Kuala Lumpur, Malaisie','🇲🇾',2.761100,101.737800,'Circuits historiques'),
('tr-2005','Istanbul Park','Istanbul, Turquie','🇹🇷',40.951900,29.405000,'Circuits historiques'),
('ru-2014','Sochi Autodrom','Sotchi, Russie','🇷🇺',43.405700,39.957800,'Circuits historiques'),
('it-1914','Mugello','Scarperia, Italie','🇮🇹',43.997500,11.371700,'Circuits historiques'),
('us-1909','Indianapolis','Indianapolis, USA','🇺🇸',39.795000,-86.234700,'Circuits historiques'),
('us-1956','Watkins Glen','New York, USA','🇺🇸',42.337000,-76.927500,'Circuits historiques'),
('ar-1952','Buenos Aires','Buenos Aires, Argentine','🇦🇷',-34.694300,-58.459200,'Circuits historiques'),
('za-1961','Kyalami','Johannesburg, Afrique du Sud','🇿🇦',-25.998900,28.068900,'Circuits historiques'),
('br-1977','Jacarepaguá','Rio, Brésil','🇧🇷',-22.975600,-43.395300,'Circuits historiques');

-- Pilotes Alpine
INSERT INTO drivers (number, name, nationality, flag_url, points, championship_pos, age, car, img_url, live_fuel, live_brake, live_ers, live_engine_status) VALUES
(10,'Pierre Gasly','France','https://flagcdn.com/w40/fr.png',26,10,30,'A526',
 'https://media.formula1.com/image/upload/c_fill,w_400,h_400,g_face/q_auto/v1740000001/common/f1/2026/alpine/piegas01/2026alpinepiegas01right.jpg',
 48,560,90,'OK'),
(43,'Franco Colapinto','Argentine','https://flagcdn.com/w40/ar.png',15,12,22,'A526',
 'https://media.formula1.com/image/upload/c_fill,w_400,h_400,g_face/q_auto/v1740000001/common/f1/2026/alpine/fracol01/2026alpinefracol01right.jpg',
 51,545,86,'OK');

-- LEDs
INSERT INTO led_states (id, color, label, is_on) VALUES
(1,'#36E0A6','À définir',0),
(2,'#FFC247','À définir',0),
(3,'#FF5757','À définir',0),
(4,'#1E6FC4','À définir',0);

-- Classement pilotes (après 6 manches)
INSERT INTO driver_standings (position, driver_name, nationality_flag, team, points) VALUES
(1,'Kimi Antonelli','🇮🇹','Mercedes',156),
(2,'Lewis Hamilton','🇬🇧','Ferrari',90),
(3,'George Russell','🇬🇧','Mercedes',88),
(4,'Charles Leclerc','🇲🇨','Ferrari',75),
(5,'Oscar Piastri','🇦🇺','McLaren',60),
(6,'Lando Norris','🇬🇧','McLaren',58),
(7,'Max Verstappen','🇳🇱','Red Bull',43),
(8,'Isack Hadjar','🇫🇷','Red Bull',29),
(9,'Liam Lawson','🇳🇿','Racing Bulls',26),
(10,'Pierre Gasly','🇫🇷','Alpine',26),
(11,'Oliver Bearman','🇬🇧','Haas',18),
(12,'Franco Colapinto','🇦🇷','Alpine',15),
(13,'Arvid Lindblad','🇬🇧','Racing Bulls',13),
(14,'Carlos Sainz','🇪🇸','Williams',6),
(15,'Alexander Albon','🇹🇭','Williams',5),
(16,'Esteban Ocon','🇫🇷','Haas',3);

-- Classement constructeurs
INSERT INTO constructor_standings (position, team, points) VALUES
(1,'Mercedes',244),
(2,'Ferrari',165),
(3,'McLaren',118),
(4,'Red Bull',72),
(5,'Alpine',41),
(6,'Racing Bulls',39),
(7,'Haas',21),
(8,'Williams',11);

-- Courses 2026
INSERT INTO races (round, flag_emoji, gp_name, circuit_name, race_date, winner_name, winner_team, pole_position, fastest_lap, alpine_result) VALUES
(1,'🇦🇺','Australie','Albert Park','8 mar','George Russell','Mercedes','George Russell','Max Verstappen','Gasly P11 · Colapinto P14'),
(2,'🇨🇳','Chine','Shanghai','15 mar','Kimi Antonelli','Mercedes','Kimi Antonelli','George Russell','Gasly P9 · Colapinto P13'),
(3,'🇯🇵','Japon','Suzuka','29 mar','Kimi Antonelli','Mercedes','George Russell','Oscar Piastri','Gasly P8 · Colapinto P15'),
(4,'🇺🇸','Miami','Miami Autodrome','3 mai','Kimi Antonelli','Mercedes','Charles Leclerc','Lando Norris','Gasly P10 · Colapinto P12'),
(5,'🇨🇦','Canada','Gilles-Villeneuve','24 mai','Kimi Antonelli','Mercedes','Kimi Antonelli','George Russell','Gasly P9 · Colapinto P11'),
(6,'🇲🇨','Monaco','Circuit de Monaco','7 juin','Kimi Antonelli','Mercedes','Kimi Antonelli','Charles Leclerc','Gasly P7 · Colapinto P14');

-- Top 5 par course
SET @r1 = (SELECT id FROM races WHERE round = 1);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r1,1,'Russell','Mercedes'),(@r1,2,'Antonelli','Mercedes'),(@r1,3,'Leclerc','Ferrari'),
(@r1,4,'Hamilton','Ferrari'),(@r1,5,'Norris','McLaren');

SET @r2 = (SELECT id FROM races WHERE round = 2);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r2,1,'Antonelli','Mercedes'),(@r2,2,'Russell','Mercedes'),(@r2,3,'Piastri','McLaren'),
(@r2,4,'Hamilton','Ferrari'),(@r2,5,'Verstappen','Red Bull');

SET @r3 = (SELECT id FROM races WHERE round = 3);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r3,1,'Antonelli','Mercedes'),(@r3,2,'Russell','Mercedes'),(@r3,3,'Leclerc','Ferrari'),
(@r3,4,'Norris','McLaren'),(@r3,5,'Hamilton','Ferrari');

SET @r4 = (SELECT id FROM races WHERE round = 4);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r4,1,'Antonelli','Mercedes'),(@r4,2,'Leclerc','Ferrari'),(@r4,3,'Piastri','McLaren'),
(@r4,4,'Hamilton','Ferrari'),(@r4,5,'Russell','Mercedes');

SET @r5 = (SELECT id FROM races WHERE round = 5);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r5,1,'Antonelli','Mercedes'),(@r5,2,'Hamilton','Ferrari'),(@r5,3,'Piastri','McLaren'),
(@r5,4,'Norris','McLaren'),(@r5,5,'Verstappen','Red Bull');

SET @r6 = (SELECT id FROM races WHERE round = 6);
INSERT INTO race_results (race_id, position, driver_name, team) VALUES
(@r6,1,'Antonelli','Mercedes'),(@r6,2,'Hamilton','Ferrari'),(@r6,3,'Hadjar','Red Bull'),
(@r6,4,'Lawson','Racing Bulls'),(@r6,5,'Lindblad','Racing Bulls');
