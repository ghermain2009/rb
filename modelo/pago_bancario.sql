ALTER TABLE `buenisimo`.`cup_cupon`   
  ADD COLUMN `fecha_registro` DATETIME NULL AFTER `id_estado_compra`,
  ADD COLUMN `observacion` VARCHAR(100) NULL AFTER `fecha_compra`;
  
ALTER TABLE `buenisimo`.`cup_cupon`   
  ADD COLUMN `fecha_operacion` DATETIME NULL AFTER `observacion`;