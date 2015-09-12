alter table `buenisimo`.`gen_empresa` 
   add column `tipo_documento_representante` char(3) NULL after `descripcion`, 
   add column `documento_representante` varchar(20) NULL after `tipo_documento_representante`, 
   add column `nombre_representante` varchar(200) NULL after `documento_representante`,
   ADD CONSTRAINT `fk_gen_empresa_gen_tipo_documento1` FOREIGN KEY (`tipo_documento_representante`) REFERENCES `buenisimo`.`gen_tipo_documento` (`id_tipo_documento`) ON DELETE NO ACTION ON UPDATE NO ACTION
   
  