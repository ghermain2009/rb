SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema buenisimo
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `buenisimo` DEFAULT CHARACTER SET utf8 ;
USE `buenisimo` ;

-- -----------------------------------------------------
-- Table `buenisimo`.`con_estado_contrato`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenisimo`.`con_estado_contrato` ;

CREATE TABLE IF NOT EXISTS `buenisimo`.`con_estado_contrato` (
  `id_estado` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_estado`))
ENGINE = INNODB;


-- -----------------------------------------------------
-- Table `buenisimo`.`con_contrato`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenisimo`.`con_contrato` ;

CREATE TABLE IF NOT EXISTS `buenisimo`.`con_contrato` (
  `id_contrato` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT(11) NOT NULL,
  `nombre_contacto` VARCHAR(200) NOT NULL,
  `email_contacto` VARCHAR(100) NOT NULL,
  `fecha_registro` DATETIME NOT NULL,
  `nombre_documento` VARCHAR(200) NOT NULL,
  `firma_documento` VARCHAR(200) NULL,
  `fecha_firma` VARCHAR(45) NULL,
  `id_estado` INT NOT NULL,
  PRIMARY KEY (`id_contrato`),
  INDEX `fk_cup_contrato_con_estado_contrato1_idx` (`id_estado` ASC),
  INDEX `fk_con_contrato_gen_empresa1_idx` (`id_empresa` ASC),
  CONSTRAINT `fk_cup_contrato_con_estado_contrato1`
    FOREIGN KEY (`id_estado`)
    REFERENCES `buenisimo`.`con_estado_contrato` (`id_estado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_con_contrato_gen_empresa1`
    FOREIGN KEY (`id_empresa`)
    REFERENCES `buenisimo`.`gen_empresa` (`id_empresa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;


-- -----------------------------------------------------
-- Table `buenisimo`.`con_contrato_anexo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenisimo`.`con_contrato_anexo` ;

CREATE TABLE IF NOT EXISTS `buenisimo`.`con_contrato_anexo` (
  `id_contrato` INT NOT NULL,
  `id_campana` INT(11) NOT NULL,
  `nombre_documento` VARCHAR(200) NOT NULL,
  `fecha_registro` DATETIME NOT NULL,
  `firma` VARCHAR(200) NULL,
  `fecha_firma` DATETIME NULL,
  `id_estado` INT NOT NULL,
  PRIMARY KEY (`id_contrato`, `id_campana`),
  INDEX `fk_con_contrato_detalle_cup_campana1_idx` (`id_campana` ASC),
  INDEX `fk_con_contrato_detalle_con_contrato1_idx` (`id_contrato` ASC),
  INDEX `fk_con_contrato_detalle_con_estado_contrato1_idx` (`id_estado` ASC),
  CONSTRAINT `fk_con_contrato_detalle_cup_campana1`
    FOREIGN KEY (`id_campana`)
    REFERENCES `buenisimo`.`cup_campana` (`id_campana`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_con_contrato_detalle_con_contrato1`
    FOREIGN KEY (`id_contrato`)
    REFERENCES `buenisimo`.`con_contrato` (`id_contrato`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_con_contrato_detalle_con_estado_contrato1`
    FOREIGN KEY (`id_estado`)
    REFERENCES `buenisimo`.`con_estado_contrato` (`id_estado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;


-- -----------------------------------------------------
-- Table `buenisimo`.`con_tipo_observacion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenisimo`.`con_tipo_observacion` ;

CREATE TABLE IF NOT EXISTS `buenisimo`.`con_tipo_observacion` (
  `id_tipo_observacion` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_tipo_observacion`))
ENGINE = INNODB;


-- -----------------------------------------------------
-- Table `buenisimo`.`con_anexo_observacion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `buenisimo`.`con_anexo_observacion` ;

CREATE TABLE IF NOT EXISTS `buenisimo`.`con_anexo_observacion` (
  `id_anexo_observacion` INT NOT NULL AUTO_INCREMENT,
  `con_contrato_anexo_id_contrato` INT NOT NULL,
  `con_contrato_anexo_id_campana` INT(11) NOT NULL,
  `observacion` VARCHAR(45) NOT NULL,
  `nombre_documento` VARCHAR(200) NOT NULL,
  `id_tipo_observacion` INT NOT NULL,
  PRIMARY KEY (`id_anexo_observacion`),
  INDEX `fk_con_anexo_observacion_con_contrato_anexo1_idx` (`con_contrato_anexo_id_contrato` ASC, `con_contrato_anexo_id_campana` ASC),
  INDEX `fk_con_anexo_observacion_con_tipo_observacion1_idx` (`id_tipo_observacion` ASC),
  CONSTRAINT `fk_con_anexo_observacion_con_contrato_anexo1`
    FOREIGN KEY (`con_contrato_anexo_id_contrato` , `con_contrato_anexo_id_campana`)
    REFERENCES `buenisimo`.`con_contrato_anexo` (`id_contrato` , `id_campana`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_con_anexo_observacion_con_tipo_observacion1`
    FOREIGN KEY (`id_tipo_observacion`)
    REFERENCES `buenisimo`.`con_tipo_observacion` (`id_tipo_observacion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;


-- -----------------------------------------------------
-- Data for table `buenisimo`.`con_estado_contrato`
-- -----------------------------------------------------
START TRANSACTION;
USE `buenisimo`;
INSERT INTO `buenisimo`.`con_estado_contrato` (`id_estado`, `descripcion`) VALUES (1, 'Registrado');
INSERT INTO `buenisimo`.`con_estado_contrato` (`id_estado`, `descripcion`) VALUES (2, 'Por revisar');
INSERT INTO `buenisimo`.`con_estado_contrato` (`id_estado`, `descripcion`) VALUES (3, 'Cerrado');

COMMIT;


-- -----------------------------------------------------
-- Data for table `buenisimo`.`con_tipo_observacion`
-- -----------------------------------------------------
START TRANSACTION;
USE `buenisimo`;
INSERT INTO `buenisimo`.`con_tipo_observacion` (`id_tipo_observacion`, `descripcion`) VALUES (1, 'Enviado');
INSERT INTO `buenisimo`.`con_tipo_observacion` (`id_tipo_observacion`, `descripcion`) VALUES (2, 'Recepcionado');

COMMIT;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
