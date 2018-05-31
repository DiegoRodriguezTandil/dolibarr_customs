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
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT 1,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT 1,
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
** Fin tabla llx_consolidation_deplacement
 *************************************************************************************************************/

/*******************************************************************************************************************
  Tabla llx_consolidation_commande_fournisseur
 */
-- tabla requerida para salvar la cotizacion manual de cada gasto

CREATE TABLE `llx_consolidation_commande_fournisseur` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_commande_fournisseur',
  `commande_fournisseur_id` int(11) NOT NULL COMMENT 'Fk de llx_commande_fournisseur',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT 1,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT 1,
  `tipo` varchar(10) DEFAULT 'Manual'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_commande_fournisseur`
--
ALTER TABLE `llx_consolidation_commande_fournisseur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_commande_fournisseur`
--
ALTER TABLE `llx_consolidation_commande_fournisseur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_commande_fournisseur', AUTO_INCREMENT=1;

 ALTER TABLE `llx_consolidation_commande_fournisseur`
  ADD CONSTRAINT `llx_consolidation_commande_f_llx_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_commande_f_llx_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_commande_f_llx_commande_f-commande_f_id` FOREIGN KEY (`commande_fournisseur_id`) REFERENCES `llx_commande_fournisseur` (`rowid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/**
** Fin tabla llx_consolidation_commande_fournisseur
 *************************************************************************************************************/


 -- TRIGGER UPDATE upd_llx_salesorderdet_line_ref
DELIMITER ;;
CREATE  TRIGGER upd_llx_salesorderdet_line_ref
  BEFORE update ON `llx_salesorderdet`
  FOR EACH ROW BEGIN
  DECLARE count_line int DEFAULT 0;
  DECLARE i int DEFAULT 0;
  DECLARE i_var varchar(3);
  DECLARE val_final int;
  DECLARE  consulta int;

  SET count_line= (SELECT COUNT(*) -1 as cant FROM llx_salesorderdet WHERE fk_salesorder = NEW.fk_salesorder);

  IF (NEW.line_ref IS NULL OR trim(NEW.line_ref)='') THEN

    SET i_var = count_line;
    SET i_var =  LPAD(i_var,3,'0');
    SET val_final=i_var;
    -- corrobora que el valor de i_var no exista para las lineas diferentes a la que se modifica
    SET consulta = (
      SELECT
        CASE
        WHEN count(R)>= 1 THEN 1
        ELSE 0
        END
      FROM
        (
          SELECT 	1 AS R
          FROM  	llx_salesorderdet so1
          WHERE  	so1.line_ref like i_var
                   AND 	so1.fk_salesorder =  new.fk_salesorder
                   AND  NEW.rowid!=so1.rowid
        ) as salesorder
    );
    IF (consulta=0) THEN
      -- corrobora que la linea a modificar no tenga el valor de i_var
      SET consulta = (
        SELECT
          CASE
          WHEN count(R)= 1 THEN 0
          ELSE - 1
          END
        FROM
          (
            SELECT 	1 AS R
            FROM  	llx_salesorderdet so1
            WHERE  	so1.line_ref like i_var
                     AND 	so1.fk_salesorder =  new.fk_salesorder
                     AND  NEW.rowid=so1.rowid
          ) as salesorder
      );
    END IF;

    WHILE consulta = 1 AND i < 100 DO
      SET consulta = (
        SELECT
          CASE
          WHEN count(R) >= 1 THEN 1
          ELSE 0
          END
        FROM
          (
            SELECT 	1 AS R
            FROM  	llx_salesorderdet so1
            WHERE  	so1.line_ref like i_var
                     AND 	so1.fk_salesorder =  new.fk_salesorder
                     AND  NEW.rowid!=so1.rowid
          ) as salesorder
      );
      IF (consulta=0) THEN
        -- corrobora que la linea no tenga el valor
        SET consulta = (
          SELECT
            CASE
            WHEN count(R) >= 1 THEN 0
            ELSE -1
            END
          FROM
            (
              SELECT 	1 AS R
              FROM  	llx_salesorderdet so1
              WHERE  	so1.line_ref like i_var
                       AND 	so1.fk_salesorder =  new.fk_salesorder
                       AND  NEW.rowid=so1.rowid
            ) as salesorder
        );
      END IF;

      SET val_final=i_var;
      SET i_var = i_var+1;
      SET i_var = LPAD(i_var,3,'0');
      SET i= i+1;
    END WHILE;


    SET NEW.line_ref = LPAD(val_final,3,'0');
  END IF;
END ;;
/*
  fin update
*/



 DELIMITER ;;
 CREATE  TRIGGER ins_llx_salesorderdet_line_ref
   BEFORE insert ON `llx_salesorderdet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) as cant FROM llx_salesorderdet WHERE fk_salesorder = NEW.fk_salesorder);
   SET i_var = count_line;
   SET i_var =  LPAD(i_var,3,'0');
   SET val_final=i_var;



   -- corrobora que el valor de i_var no exista
   SET consulta = (
     SELECT
       CASE
       WHEN count(R)>= 1 THEN 1
       ELSE 0
       END
     FROM
       (
         SELECT 	1 AS R
         FROM  	llx_salesorderdet so1
         WHERE  	so1.line_ref like i_var
                  AND 	so1.fk_salesorder =  new.fk_salesorder
       ) as salesorder
   );


   WHILE consulta = 1 AND i < 100 DO
     -- corrobora que el valor de i_var no exista
     SET consulta = (
       SELECT
         CASE
         WHEN count(R) >= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_salesorderdet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_salesorder =  new.fk_salesorder
         ) as salesorder
     );

     SET val_final=i_var;
     SET i_var = i_var+1;
     SET i_var = LPAD(i_var,3,'0');
     SET i= i+1;
   END WHILE;

   SET NEW.line_ref = LPAD(val_final,3,'0');

 END ;;



 -- -------------------------------------
 -- ------------ propal
 -- ------------------------------------

 -- TRIGGER UPDATE upd_llx_propaldet_line_ref
 DELIMITER ;;
 CREATE  TRIGGER upd_llx_propaldet_line_ref
   BEFORE update ON `llx_propaldet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) -1 as cant FROM llx_propaldet  WHERE fk_propal = NEW.fk_propal);

   IF (NEW.line_ref IS NULL OR trim(NEW.line_ref)='') THEN

     SET i_var = count_line;
     SET i_var =  LPAD(i_var,3,'0');
     SET val_final=i_var;
     -- corrobora que el valor de i_var no exista para las lineas diferentes a la que se modifica
     SET consulta = (
       SELECT
         CASE
         WHEN count(R)>= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_propaldet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_propal =  new.fk_propal
                    AND  NEW.rowid!=so1.rowid
         ) as salesorder
     );
     IF (consulta=0) THEN
       -- corrobora que la linea a modificar no tenga el valor de i_var
       SET consulta = (
         SELECT
           CASE
           WHEN count(R)= 1 THEN 0
           ELSE - 1
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_propaldet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_propal =  new.fk_propal
                      AND  NEW.rowid=so1.rowid
           ) as salesorder
       );
     END IF;

     WHILE consulta = 1 AND i < 100 DO
       SET consulta = (
         SELECT
           CASE
           WHEN count(R) >= 1 THEN 1
           ELSE 0
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_propaldet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_propal =  new.fk_propal
                      AND  NEW.rowid!=so1.rowid
           ) as salesorder
       );
       IF (consulta=0) THEN
         -- corrobora que la linea no tenga el valor
         SET consulta = (
           SELECT
             CASE
             WHEN count(R) >= 1 THEN 0
             ELSE -1
             END
           FROM
             (
               SELECT 	1 AS R
               FROM  	llx_propaldet so1
               WHERE  	so1.line_ref like i_var
                        AND 	so1.fk_propal =  new.fk_propal
                        AND  NEW.rowid=so1.rowid
             ) as salesorder
         );
       END IF;

       SET val_final=i_var;
       SET i_var = i_var+1;
       SET i_var = LPAD(i_var,3,'0');
       SET i= i+1;
     END WHILE;


     SET NEW.line_ref = LPAD(val_final,3,'0');
   END IF;
 END ;;
 /*
   fin update
 */




 DELIMITER ;;
 CREATE  TRIGGER ins_llx_propaldet_line_ref
   BEFORE insert ON `llx_propaldet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) as cant FROM llx_propaldet WHERE fk_propal = NEW.fk_propal);
   SET i_var = count_line;
   SET i_var =  LPAD(i_var,3,'0');
   SET val_final=i_var;



   -- corrobora que el valor de i_var no exista
   SET consulta = (
     SELECT
       CASE
       WHEN count(R)>= 1 THEN 1
       ELSE 0
       END
     FROM
       (
         SELECT 	1 AS R
         FROM  	llx_propaldet so1
         WHERE  	so1.line_ref like i_var
                  AND 	so1.fk_propal =  new.fk_propal
       ) as salesorder
   );


   WHILE consulta = 1 AND i < 100 DO
     -- corrobora que el valor de i_var no exista
     SET consulta = (
       SELECT
         CASE
         WHEN count(R) >= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_propaldet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_propal =  new.fk_propal
         ) as salesorder
     );

     SET val_final=i_var;
     SET i_var = i_var+1;
     SET i_var = LPAD(i_var,3,'0');
     SET i= i+1;
   END WHILE;

   SET NEW.line_ref = LPAD(val_final,3,'0');

 END ;;


 -- -------------------------------------
 -- ------------
 -- ------------------------------------


 -- -------------------------------------
 -- ------------ llx_commandedet
 -- ------------------------------------

 -- TRIGGER UPDATE upd_llx_commandedet_line_ref
 DELIMITER ;;
 CREATE  TRIGGER upd_llx_commandedet_line_ref
   BEFORE update ON `llx_commandedet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) -1 as cant FROM llx_commandedet  WHERE fk_commande = NEW.fk_commande);

   IF (NEW.line_ref IS NULL OR trim(NEW.line_ref)='') THEN

     SET i_var = count_line;
     SET i_var =  LPAD(i_var,3,'0');
     SET val_final=i_var;
     -- corrobora que el valor de i_var no exista para las lineas diferentes a la que se modifica
     SET consulta = (
       SELECT
         CASE
         WHEN count(R)>= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_commandedet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_commande =  new.fk_commande
                    AND  NEW.rowid!=so1.rowid
         ) as salesorder
     );
     IF (consulta=0) THEN
       -- corrobora que la linea a modificar no tenga el valor de i_var
       SET consulta = (
         SELECT
           CASE
           WHEN count(R)= 1 THEN 0
           ELSE - 1
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_commandedet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_commande =  new.fk_commande
                      AND  NEW.rowid=so1.rowid
           ) as salesorder
       );
     END IF;

     WHILE consulta = 1 AND i < 100 DO
       SET consulta = (
         SELECT
           CASE
           WHEN count(R) >= 1 THEN 1
           ELSE 0
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_commandedet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_commande =  new.fk_commande
                      AND  NEW.rowid!=so1.rowid
           ) as salesorder
       );
       IF (consulta=0) THEN
         -- corrobora que la linea no tenga el valor
         SET consulta = (
           SELECT
             CASE
             WHEN count(R) >= 1 THEN 0
             ELSE -1
             END
           FROM
             (
               SELECT 	1 AS R
               FROM  	llx_commandedet so1
               WHERE  	so1.line_ref like i_var
                        AND 	so1.fk_commande =  new.fk_commande
                        AND  NEW.rowid=so1.rowid
             ) as salesorder
         );
       END IF;

       SET val_final=i_var;
       SET i_var = i_var+1;
       SET i_var = LPAD(i_var,3,'0');
       SET i= i+1;
     END WHILE;


     SET NEW.line_ref = LPAD(val_final,3,'0');
   END IF;
 END ;;
 /*
   fin update
 */




 DELIMITER ;;
 CREATE  TRIGGER ins_llx_commandedet_line_ref
   BEFORE insert ON `llx_commandedet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) as cant FROM llx_commandedet WHERE fk_commande = NEW.fk_commande);
   SET i_var = count_line;
   SET i_var =  LPAD(i_var,3,'0');
   SET val_final=i_var;



   -- corrobora que el valor de i_var no exista
   SET consulta = (
     SELECT
       CASE
       WHEN count(R)>= 1 THEN 1
       ELSE 0
       END
     FROM
       (
         SELECT 	1 AS R
         FROM  	llx_commandedet so1
         WHERE  	so1.line_ref like i_var
                  AND 	so1.fk_commande =  new.fk_commande
       ) as salesorder
   );


   WHILE consulta = 1 AND i < 100 DO
     -- corrobora que el valor de i_var no exista
     SET consulta = (
       SELECT
         CASE
         WHEN count(R) >= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_commandedet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_commande =  new.fk_commande
         ) as salesorder
     );

     SET val_final=i_var;
     SET i_var = i_var+1;
     SET i_var = LPAD(i_var,3,'0');
     SET i= i+1;
   END WHILE;

   SET NEW.line_ref = LPAD(val_final,3,'0');

 END ;;


 -- -------------------------------------
 -- ------------
 -- ------------------------------------


 -- -------------------------------------
 -- ------------ llx_commande_fournisseurdet
 -- ------------------------------------

 -- TRIGGER UPDATE upd_llx_commande_fournisseurdet_line_ref
 DELIMITER ;;
 CREATE  TRIGGER upd_llx_llx_commande_fournisseurdet_line_ref
   BEFORE update ON `llx_commande_fournisseurdet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) -1 as cant FROM llx_commande_fournisseurdet  WHERE fk_commande = NEW.fk_commande);

   IF (NEW.line_ref IS NULL OR trim(NEW.line_ref)='') THEN

     SET i_var = count_line;
     SET i_var =  LPAD(i_var,3,'0');
     SET val_final=i_var;
     -- corrobora que el valor de i_var no exista para las lineas diferentes a la que se modifica
     SET consulta = (
       SELECT
         CASE
         WHEN count(R)>= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_commande_fournisseurdet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_commande =  new.fk_commande
                    AND  NEW.rowid!=so1.rowid
         ) as salesorder
     );
     IF (consulta=0) THEN
       -- corrobora que la linea a modificar no tenga el valor de i_var
       SET consulta = (
         SELECT
           CASE
           WHEN count(R)= 1 THEN 0
           ELSE - 1
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_commande_fournisseurdet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_commande =  new.fk_commande
                      AND  NEW.rowid=so1.rowid
           ) as salesorder
       );
     END IF;

     WHILE consulta = 1 AND i < 100 DO
       SET consulta = (
         SELECT
           CASE
           WHEN count(R) >= 1 THEN 1
           ELSE 0
           END
         FROM
           (
             SELECT 	1 AS R
             FROM  	llx_commande_fournisseurdet so1
             WHERE  	so1.line_ref like i_var
                      AND 	so1.fk_commande =  new.fk_commande
                      AND  NEW.rowid!=so1.rowid
           ) as salesorder
       );
       IF (consulta=0) THEN
         -- corrobora que la linea no tenga el valor
         SET consulta = (
           SELECT
             CASE
             WHEN count(R) >= 1 THEN 0
             ELSE -1
             END
           FROM
             (
               SELECT 	1 AS R
               FROM  	llx_commande_fournisseurdet so1
               WHERE  	so1.line_ref like i_var
                        AND 	so1.fk_commande =  new.fk_commande
                        AND  NEW.rowid=so1.rowid
             ) as salesorder
         );
       END IF;

       SET val_final=i_var;
       SET i_var = i_var+1;
       SET i_var = LPAD(i_var,3,'0');
       SET i= i+1;
     END WHILE;


     SET NEW.line_ref = LPAD(val_final,3,'0');
   END IF;
 END ;;
 /*
   fin update
 */




 DELIMITER ;;
 CREATE  TRIGGER ins_llx_commande_fournisseurdet_line_ref
   BEFORE insert ON `llx_commande_fournisseurdet`
   FOR EACH ROW BEGIN
   DECLARE count_line int DEFAULT 0;
   DECLARE i int DEFAULT 0;
   DECLARE i_var varchar(3);
   DECLARE val_final int;
   DECLARE  consulta int;

   SET count_line= (SELECT COUNT(*) as cant FROM llx_commande_fournisseurdet WHERE fk_commande = NEW.fk_commande);
   SET i_var = count_line;
   SET i_var =  LPAD(i_var,3,'0');
   SET val_final=i_var;



   -- corrobora que el valor de i_var no exista
   SET consulta = (
     SELECT
       CASE
       WHEN count(R)>= 1 THEN 1
       ELSE 0
       END
     FROM
       (
         SELECT 	1 AS R
         FROM  	llx_commande_fournisseurdet so1
         WHERE  	so1.line_ref like i_var
                  AND 	so1.fk_commande =  new.fk_commande
       ) as salesorder
   );


   WHILE consulta = 1 AND i < 100 DO
     -- corrobora que el valor de i_var no exista
     SET consulta = (
       SELECT
         CASE
         WHEN count(R) >= 1 THEN 1
         ELSE 0
         END
       FROM
         (
           SELECT 	1 AS R
           FROM  	llx_commande_fournisseurdet so1
           WHERE  	so1.line_ref like i_var
                    AND 	so1.fk_commande =  new.fk_commande
         ) as salesorder
     );

     SET val_final=i_var;
     SET i_var = i_var+1;
     SET i_var = LPAD(i_var,3,'0');
     SET i= i+1;
   END WHILE;

   SET NEW.line_ref = LPAD(val_final,3,'0');

 END ;;

 -- -------------------------------------
 -- ------------
 -- ------------------------------------

 -- *******************************************************************************************************************
 --  Tabla llx_consolidation_facture

-- tabla requerida para salvar la cotizacion manual de cada factura

CREATE TABLE `llx_consolidation_facture` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_feature',
  `facture_id` int(11) NOT NULL COMMENT 'Fk de llx_facture',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT NULL,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT 'Manual'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_facture`
--
ALTER TABLE `llx_consolidation_facture`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_facture`
--
ALTER TABLE `llx_consolidation_facture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_feature', AUTO_INCREMENT=1;

  ALTER TABLE `llx_consolidation_facture`
  ADD CONSTRAINT `llx_consolidation_facture_llx_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_facture_llx_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_facture_llx_facture-facture_id` FOREIGN KEY (`facture_id`) REFERENCES `llx_facture` (`rowid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Fin tabla llx_consolidation_facture
-- *************************************************************************************************************/
-- *******************************************************************************************************************

 -- *******************************************************************************************************************
 --  Tabla llx_consolidation_domain_salesorder

-- tabla requerida para indicar si si se prioriza la entidad padre (ejemplo salesorder y no facture)

CREATE TABLE `llx_consolidation_domain_salesorder` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_domain_salesorder',
  `entidad_id` int(11) NOT NULL COMMENT 'Fk',
  `domain` int(11) NOT NULL COMMENT 'Fk de llx_facture',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_domain_salesorder`
--
ALTER TABLE `llx_consolidation_domain_salesorder`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_domain_salesorder`
--
ALTER TABLE `llx_consolidation_domain_salesorder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_domain_salesorder', AUTO_INCREMENT=1;


-- Fin tabla llx_consolidation_domain_salesorder
-- *************************************************************************************************************/
-- *******************************************************************************************************************

-- *******************************************************************************************************************
 --  Tabla llx_consolidation_policy

-- tabla requerida para salvar la cotizacion manual de cada poliza

CREATE TABLE `llx_consolidation_policy` (
  `id` int(11) NOT NULL COMMENT 'Identificador de llx_consolidation_feature',
  `policy_id` int(11) NOT NULL COMMENT 'Fk de llx_policy',
  `fecha_ingreso` date DEFAULT NULL COMMENT 'Fecha en que se realizo el ingreso',
  `divisa_origen` varchar(3) DEFAULT NULL  COMMENT 'Divisa a la cual se realizara la conversion a la divisa destino',
  `divisa_destino` varchar(3) DEFAULT "USD" COMMENT 'Divisa a la cual se convertira la divisa origen',
  `valor_divisa_origen` DECIMAL (11,2) DEFAULT NULL,
  `valor_divisa_destino` DECIMAL(11,2) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT 'Manual',
  `fk_doctype` varchar(2) DEFAULT 'SO'
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Registra las divisas de conversión para ordenes de venta';

--
-- Indices de la tabla `llx_consolidation_policy`
--
ALTER TABLE `llx_consolidation_policy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de la tabla `llx_consolidation_policy`
--
ALTER TABLE `llx_consolidation_policy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de llx_consolidation_policy', AUTO_INCREMENT=1;

  ALTER TABLE `llx_consolidation_policy`
  ADD CONSTRAINT `llx_consolidation_policy_llx_c_currencies-divisa_origen` FOREIGN KEY (`divisa_origen`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_policy_llx_c_currencies-divisa_destino` FOREIGN KEY (`divisa_destino`) REFERENCES `llx_c_currencies` (`code_iso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `llx_consolidation_policy_llx_policy-policy_id` FOREIGN KEY (`policy_id`) REFERENCES `llx_policy` (`rowid`) ON DELETE NO ACTION ON UPDATE NO ACTION;
 alter table llx_policy add fk_currency varchar(3) default "USD";
-- Fin tabla llx_consolidation_policy
-- *************************************************************************************************************/
-- *******************************************************************************************************************


/**


-- view suma las facturas de un proyecto
-- drop view vw_sum_fatcure_by_project;    - v
create view vw_sum_fatcure_by_salesorder as
select
	llx_element_element.fk_source as salesorder_rowid,
	sum(llx_facture.total_ttc) as total_sum_facture
from  llx_element_element
join  llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
where llx_element_element.sourcetype="salesorder"
and   llx_element_element.targettype="facture"
group by llx_element_element.fk_source;
-- ---------------------------------

 -- obtine si domina la ov o si domina la factura agrupando por la pk de ov
 -- drop view vw_domain_entity_between_salesorder_and_facture;
  /*create view vw_domain_entity_between_salesorder_and_facture as
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			1 as  domain_salesorder,
			0 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
where 		vw_sum_fatcure_by_salesorder.total_sum_facture < llx_salesorder.total_ttc
union
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			0 as  domain_salesorder,
			1 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
where 		vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc;



  create view vw_domain_entity_between_salesorder_and_facture as select 			vw_sum_fatcure_by_salesorder.salesorder_rowid , 			1 as  domain_salesorder, 			0 as  domain_facture from 		llx_salesorder left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid) where 		vw_sum_fatcure_by_salesorder.total_sum_facture < llx_salesorder.total_ttc union select			vw_sum_fatcure_by_salesorder.salesorder_rowid ,			0 as  domain_salesorder,			1 as  domain_facture from 		llx_salesorder left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid) where 		vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc;
*/

-- --------------------------------------------------------------------
 -- verifica si tienen misma moneda laov y la fatura, factura agrupando por la pk de ov
create view vw_same_currency_between_salesorder_and_facture as
select
		llx_salesorder.rowid as salesorder_rowid ,
		1 as  same_currency
from 	llx_salesorder
join 	llx_element_element
on		(
			llx_element_element.fk_source=llx_salesorder.rowid
		and
			llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		 )
join  	llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
where 	llx_facture.fk_currency=llx_salesorder.fk_currency
union
select
		llx_salesorder.rowid as salesorder_rowid ,
		0 as  same_currency
from 	llx_salesorder
join 	llx_element_element
on		(
			llx_element_element.fk_source=llx_salesorder.rowid
			and
			llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		)
join  	llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
where 	llx_facture.fk_currency<>llx_salesorder.fk_currency;




 /****************************************************************************************************************/
/*
select
	llx_salesorder.rowid as salesorder_rowid,
	 vwed.domain_salesorder,
     vwed.domain_facture,
     vwsc.same_currency,
	 llx_salesorder.fk_currency as moneda_salesorder,
	 llx_salesorder.fk_projet,
     llx_salesorder.ref as salesorder_name
from llx_salesorder
join vw_domain_entity_between_salesorder_and_facture vwed on (llx_salesorder.rowid=vwed.salesorder_rowid)
join vw_same_currency_between_salesorder_and_facture vwsc on (llx_salesorder.rowid=vwsc.salesorder_rowid);
*/


 /****************************************************************************************************************/

create view vw_domain_entity_between_salesorder_and_facture as
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			1 as  domain_salesorder,
			0 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where 		((llx_salesorder.total_ttc > vw_sum_fatcure_by_salesorder.total_sum_facture  and vwsc.same_currency=1)
or
			 (vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc and vwsc.same_currency=0))
union
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			0 as  domain_salesorder,
			1 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where 		(vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc and vwsc.same_currency=1) ;

 /****************************************************************************************************************/

create view vw_salesorder_domain as
	select
		llx_salesorder.rowid as salesorder_rowid,
		 vwed.domain_salesorder,
		 vwed.domain_facture,
		 vwsc.same_currency,
		 llx_salesorder.fk_currency as moneda_salesorder,
		 llx_salesorder.fk_projet,
		 llx_salesorder.ref as salesorder_name
	from llx_salesorder
	join vw_domain_entity_between_salesorder_and_facture vwed on (llx_salesorder.rowid=vwed.salesorder_rowid)
	join vw_same_currency_between_salesorder_and_facture vwsc on (llx_salesorder.rowid=vwsc.salesorder_rowid);


 /****************************************************************************************************************/
 create view vw_salesorder_facture_cotizacion as
 select
         salesorder.domain_salesorder
         ,salesorder.domain_facture
        ,salesorder.salesorder_rowid
        ,salesorder.same_currency
        ,salesorder.moneda_salesorder
        ,salesorder.fk_projet
        ,salesorder.salesorder_name
        ,llx_facture.fk_currency
        ,llx_facture.rowid as facture_rowid
        ,llx_facture.facnumber as facture_name
from
    vw_salesorder_domain as salesorder
	join llx_element_element on
		(
			llx_element_element.fk_source=salesorder.salesorder_rowid
			and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		)
	join llx_facture on(llx_facture.rowid=llx_element_element.fk_target);

/************************************************************************************************************/

 create view vw_salesorder_facture_cotizacion_priorizada as
select
	 vw_sf.*
    ,domain as domain1
	,ifnull(domain,0)as  domain
	,ds.entidad_id
	,ds.fecha_ingreso
	,ds.id

from 	vw_salesorder_facture_cotizacion vw_sf
left join 	llx_consolidation_domain_salesorder ds  on(ds.entidad_id= vw_sf.salesorder_rowid );









/*
 drop view vw_name_domain;
create view vw_name_domain as
select
			vw_sf.salesorder_rowid,
			vw_sf.salesorder_name  name_domain

from 		vw_salesorder_facture_cotizacion vw_sf
left join 	llx_consolidation_domain_salesorder ds  on(ds.entidad_id= vw_sf.salesorder_rowid )
where 	    vw_sf.domain_salesorder =1
-- group by  	vw_sf.salesorder_rowid,facture_name
union
select
			vw_sf.salesorder_rowid,
			vw_sf.facture_name  name_domain
from 		vw_salesorder_facture_cotizacion vw_sf
left join 	llx_consolidation_domain_salesorder ds  on(ds.entidad_id= vw_sf.salesorder_rowid )
where 	   	vw_sf.domain_salesorder=0
-- group by  	vw_sf.salesorder_rowid,facture_name ;
 ;

*/




--  drop view vw_domain_entity_between_salesorder_and_facture;
 -- create view vw_domain_entity_between_salesorder_and_facture as
/*
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			1 as  domain_salesorder,
			0 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where 		(((((llx_salesorder.total_ttc > vw_sum_fatcure_by_salesorder.total_sum_facture  and vwsc.same_currency=1)
or
			 (vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc and vwsc.same_currency=0))
and  not
exists (
	select  1
    from 	llx_consolidation_domain_salesorder
    where  	entidad_id=llx_salesorder.rowid
    and 	domain=1
))
)
or exists (
	select  1
    from 	llx_consolidation_domain_salesorder
    where  	entidad_id=llx_salesorder.rowid
    and 	domain=1
))
 -- and llx_salesorder.rowid=387;
   union
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			0 as  domain_salesorder,
			1 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where 		((vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc and vwsc.same_currency=1)
and not
exists (
	select  1
    from 	llx_consolidation_domain_salesorder
    where  	entidad_id=llx_salesorder.rowid
    and domain=1

));
-- and llx_salesorder.rowid=387;
*/





-- drop view vw_salesorder_facture_cotizacion_priorizada ;

*/







/************************************************************************************************************/
/*DEFINITIVO*/

create view vw_sum_fatcure_by_salesorder as
select
	llx_element_element.fk_source as salesorder_rowid,
	sum(llx_facture.total_ttc) as total_sum_facture
from  llx_element_element
join  llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
where llx_element_element.sourcetype="salesorder"
and   llx_element_element.targettype="facture"
group by llx_element_element.fk_source;


/************************************************************************************************************/

 drop view diff_cant_currencies_between_facture_salesorder;
 create view diff_cant_currencies_between_facture_salesorder2 as
select  s1.rowid as salesorder_rowid,1 as diff
from llx_salesorder s1
where exists(
select
      1
from
    llx_salesorder
     join llx_element_element on( llx_element_element.fk_source=llx_salesorder.rowid and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture")
    join llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
    where llx_facture.fk_currency<>llx_salesorder.fk_currency
	and   llx_salesorder.rowid=s1.rowid
group by llx_salesorder.rowid
)
union
select s1.rowid as salesorder_rowid,0 as diff
from llx_salesorder s1
where not exists(
select
      1
from
    llx_salesorder
     join llx_element_element on( llx_element_element.fk_source=llx_salesorder.rowid and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture")
    join llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
    where llx_facture.fk_currency<>llx_salesorder.fk_currency
	and   llx_salesorder.rowid=s1.rowid
group by llx_salesorder.rowid
);


create view vw_same_currency_between_salesorder_and_facture as
select
salesorder_rowid ,
1 as  same_currency
from diff_cant_currencies_between_facture_salesorder
where diff=0
union
select
salesorder_rowid ,
0 as  same_currency
from diff_cant_currencies_between_facture_salesorder
where diff=1;


/************************************************************************************************************/

/************************************************************************************************************/
create view vw_domain_entity_between_salesorder_and_facture as
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			1 as  domain_salesorder,
			0 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where
	  (
      (llx_salesorder.total_ttc >= vw_sum_fatcure_by_salesorder.total_sum_facture  and vwsc.same_currency=1)
        or
      (vwsc.same_currency=0)
    )
union
select
			vw_sum_fatcure_by_salesorder.salesorder_rowid ,
			0 as  domain_salesorder,
			1 as  domain_facture
from 		llx_salesorder
left join  	vw_sum_fatcure_by_salesorder on(llx_salesorder.rowid=vw_sum_fatcure_by_salesorder.salesorder_rowid)
join 		vw_same_currency_between_salesorder_and_facture vwsc on(llx_salesorder.rowid=vwsc.salesorder_rowid)
where 		(vw_sum_fatcure_by_salesorder.total_sum_facture > llx_salesorder.total_ttc and vwsc.same_currency=1);

/************************************************************************************************************/
create view vw_salesorder_domain as
	select
		llx_salesorder.rowid as salesorder_rowid,
		 vwed.domain_salesorder,
		 vwed.domain_facture,
		 vwsc.same_currency,
		 llx_salesorder.fk_currency as moneda_salesorder,
		 llx_salesorder.fk_projet,
		 llx_salesorder.ref as salesorder_name
	from llx_salesorder
	join vw_domain_entity_between_salesorder_and_facture vwed on (llx_salesorder.rowid=vwed.salesorder_rowid)
	join vw_same_currency_between_salesorder_and_facture vwsc on (llx_salesorder.rowid=vwsc.salesorder_rowid);

-- create view vw_salesorder_domain as select llx_salesorder.rowid as salesorder_rowid,vwed.domain_salesorder,vwed.domain_facture,vwsc.same_currency,llx_salesorder.fk_currency as moneda_salesorder,llx_salesorder.fk_projet, llx_salesorder.ref as salesorder_name from llx_salesorder join vw_domain_entity_between_salesorder_and_facture vwed on (llx_salesorder.rowid=vwed.salesorder_rowid) join vw_same_currency_between_salesorder_and_facture vwsc on (llx_salesorder.rowid=vwsc.salesorder_rowid);


/************************************************************************************************************/
create view vw_salesorder_facture_cotizacion as
 select
         salesorder.domain_salesorder
         ,salesorder.domain_facture
        ,salesorder.salesorder_rowid
        ,salesorder.same_currency
        ,salesorder.moneda_salesorder
        ,salesorder.fk_projet
        ,salesorder.salesorder_name
        ,llx_facture.fk_currency
        ,llx_facture.rowid as facture_rowid
        ,llx_facture.facnumber as facture_name
from
    vw_salesorder_domain as salesorder
	join llx_element_element on
		(
			llx_element_element.fk_source=salesorder.salesorder_rowid
			and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		)
	join llx_facture on(llx_facture.rowid=llx_element_element.fk_target);

-- select  *
-- from     vw_salesorder_domain where salesorder.salesorder_rowid=1047 ;

-- select * from llx_element_element where llx_element_element.fk_source=1047 and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"	;





 -- create view vw_salesorder_facture_cotizacion as  select salesorder.domain_salesorder,salesorder.domain_facture,salesorder.salesorder_rowid,salesorder.same_currency,salesorder.moneda_salesorder,salesorder.fk_projet,salesorder.salesorder_name,llx_facture.fk_currency,llx_facture.rowid as facture_rowid,llx_facture.facnumber as facture_name from vw_salesorder_domain as salesorder join llx_element_element on(llx_element_element.fk_source=salesorder.salesorder_rowid and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture")join llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
/************************************************************************************************************/
create view vw_salesorder_facture_cotizacion_priorizada as
select
	 vw_sf.*
    ,domain as domain1
	,ifnull(domain,0)as  domain
	,ds.entidad_id
	,ds.fecha_ingreso
	,ds.id

from 	vw_salesorder_facture_cotizacion vw_sf
left join 	llx_consolidation_domain_salesorder ds  on(ds.entidad_id= vw_sf.salesorder_rowid );

/*
CREATE
    ALGORITHM = UNDEFINED
    DEFINER = `root`@`%`
    SQL SECURITY DEFINER
VIEW `vw_salesorder_facture_cotizacion_priorizada` AS
    SELECT
        `vw_sf`.`domain_salesorder` AS `domain_salesorder`,
        `vw_sf`.`domain_facture` AS `domain_facture`,
        `vw_sf`.`salesorder_rowid` AS `salesorder_rowid`,
        `vw_sf`.`same_currency` AS `same_currency`,
        `vw_sf`.`moneda_salesorder` AS `moneda_salesorder`,
        `vw_sf`.`fk_projet` AS `fk_projet`,
        `vw_sf`.`salesorder_name` AS `salesorder_name`,
        `vw_sf`.`fk_currency` AS `fk_currency`,
        `vw_sf`.`facture_rowid` AS `facture_rowid`,
        `vw_sf`.`facture_name` AS `facture_name`,
        `ds`.`domain` AS `domain1`,
        IFNULL(`ds`.`domain`, 0) AS `domain`,
        `ds`.`entidad_id` AS `entidad_id`,
        `ds`.`fecha_ingreso` AS `fecha_ingreso`,
        `ds`.`id` AS `id`
    FROM
        (`dolibar`.`vw_salesorder_facture_cotizacion` `vw_sf`
        LEFT JOIN `dolibar`.`llx_consolidation_domain_salesorder` `ds` ON ((`ds`.`entidad_id` = `vw_sf`.`salesorder_rowid`)))
*/
-- create view vw_salesorder_facture_cotizacion_priorizada as select vw_sf.*,domain as domain1,ifnull(domain,0)as  domain,ds.entidad_id,ds.fecha_ingreso,ds.id from vw_salesorder_facture_cotizacion vw_sf left join llx_consolidation_domain_salesorder ds on(ds.entidad_id= vw_sf.salesorder_rowid );
/************************************************************************************************************/






