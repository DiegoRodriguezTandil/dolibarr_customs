CREATE TABLE `llx_consolidation_day` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_day',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` decimal(11,2) DEFAULT NULL,
  `valor_divisa_destino` decimal(11,2) DEFAULT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión';

--
-- Indices de la tabla `llx_consolidation_day`
--
ALTER TABLE `llx_consolidation_day`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_day`
--
ALTER TABLE `llx_consolidation_day`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_day', AUTO_INCREMENT=1;

  ALTER TABLE `llx_consolidation_day`
  ADD CONSTRAINT `consolidation_day_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `consolidation_day_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION;