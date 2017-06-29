/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.21-MariaDB : Database - duralex
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`duralex` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `duralex`;

/*Table structure for table `abogado` */

DROP TABLE IF EXISTS `abogado`;

CREATE TABLE `abogado` (
  `rut` varchar(12) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `fecha_cont` date DEFAULT NULL,
  `especialidad` int(3) DEFAULT NULL,
  `valor_hora` int(15) DEFAULT NULL,
  PRIMARY KEY (`rut`),
  KEY `especialidad` (`especialidad`),
  /*CONSTRAINT `abogado_ibfk_1` FOREIGN KEY (`especialidad`) REFERENCES `especialidad` (`cod_espe`)*/
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `atencion` */

DROP TABLE IF EXISTS `atencion`;

CREATE TABLE `atencion` (
  `numero_ate` int(15) NOT NULL AUTO_INCREMENT,
  `fecha_hora` date DEFAULT NULL,
  `cliente_rut` varchar(12) DEFAULT NULL,
  `abogado_rut` varchar(12) DEFAULT NULL,
  `estado_cod` int(3) DEFAULT NULL,
  `valor_ate` int(15) DEFAULT NULL,
  PRIMARY KEY (`numero_ate`),
  KEY `cliente_rut` (`cliente_rut`),
  KEY `abogado_rut` (`abogado_rut`),
  KEY `estado_cod` (`estado_cod`),
  CONSTRAINT `atencion_ibfk_1` FOREIGN KEY (`cliente_rut`) REFERENCES `cliente` (`rut`),
  CONSTRAINT `atencion_ibfk_2` FOREIGN KEY (`abogado_rut`) REFERENCES `abogado` (`rut`),
  CONSTRAINT `atencion_ibfk_3` FOREIGN KEY (`estado_cod`) REFERENCES `estado` (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cliente` */

DROP TABLE IF EXISTS `cliente`;

CREATE TABLE `cliente` (
  `rut` varchar(12) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `fecha_inc` date DEFAULT NULL,
  `tipo_persona` char(1) DEFAULT NULL,
  `direccion` varchar(30) DEFAULT NULL,
  `telefono` int(12) DEFAULT NULL,
  PRIMARY KEY (`rut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `especialidad` */

DROP TABLE IF EXISTS `especialidad`;

CREATE TABLE `especialidad` (
  `cod_espe` int(3) NOT NULL,
  `nom_espe` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cod_espe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `estado` */

DROP TABLE IF EXISTS `estado`;

CREATE TABLE `estado` (
  `cod_estado` int(3) NOT NULL,
  `nombre_estado` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `perfil` */

DROP TABLE IF EXISTS `perfil`;

CREATE TABLE `perfil` (
  `cod_perfil` int(3) NOT NULL,
  `nombre_perfil` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cod_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `rut` varchar(12) NOT NULL,
  `nombre_completo` varchar(50) DEFAULT NULL,
  `perfil_cod` int(3) DEFAULT NULL,
  `contraseña` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`rut`),
  KEY `perfil_cod` (`perfil_cod`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`perfil_cod`) REFERENCES `perfil` (`cod_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
