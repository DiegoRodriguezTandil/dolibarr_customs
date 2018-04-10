/*******************************************************************************************************************
  Tabla llx_consolidation_day
 */
-- Tabla requerida para formulario de cotizacion en index

CREATE TABLE `llx_consolidation_day` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_day',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT NULL,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT NULL
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


/**
** Fin tabla llx_consolidation_day
 *************************************************************************************************************/


/*******************************************************************************************************************
  Tabla llx_consolidation_salesorder
 */
-- tabla requerida para salvar la cotizacion manual de cada ov

CREATE TABLE `llx_consolidation_salesorder` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_salesorder',
  `salesorder_id` int(11) NOT NULL COMMENT 'Fk de llx_salesorder',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT NULL,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT 'Manual'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_salesorder`
--
ALTER TABLE `llx_consolidation_salesorder`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_salesorder`
--
ALTER TABLE `llx_consolidation_salesorder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_salesorder', AUTO_INCREMENT=1;

  ALTER TABLE `llx_consolidation_salesorder`
  ADD CONSTRAINT `llx_consolidation_salesorder_llx_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_salesorder_llx_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_salesorder_llx_salesorder-salesorder_id` FOREIGN KEY (`salesorder_id`) REFERENCES `llx_salesorder` (`rowid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/**
** Fin tabla llx_consolidation_day
 *************************************************************************************************************/
/*******************************************************************************************************************
  Tabla llx_consolidation_deplacement
 */
-- tabla requerida para salvar la cotizacion manual de cada gasto

CREATE TABLE `llx_consolidation_deplacement` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_salesorder',
  `deplacement_id` int(11) NOT NULL COMMENT 'Fk de llx_deplacement',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT NULL,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT 'Manual'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_deplacement`
--
ALTER TABLE `llx_consolidation_deplacement`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_deplacement`
--
ALTER TABLE `llx_consolidation_deplacement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_deplacement', AUTO_INCREMENT=1;

  ALTER TABLE `llx_consolidation_deplacement`
  ADD CONSTRAINT `llx_consolidation_deplacement_llx_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_deplacement_llx_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_deplacement_llx_salesorder-deplacement_id` FOREIGN KEY (`deplacement_id`) REFERENCES `llx_deplacement` (`rowid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/**
** Fin tabla llx_consolidation_day
 *************************************************************************************************************/

