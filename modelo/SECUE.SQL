alter table `buenisimo`.`cup_cupon` 
   add column `fecha_validacion` datetime NULL after `fecha_compra`, 
   add column `fecha_liquidacion` datetime NULL after `fecha_validacion`
   
alter table `buenisimo`.`cup_liquidacion` 
   add column `estado_liquidacion` char(1) NULL after `id_campana`