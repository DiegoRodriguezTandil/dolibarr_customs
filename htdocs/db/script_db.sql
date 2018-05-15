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

/*
 create view vw_salesorder_facture_cotizacion as
 select
        case
                when salesorder.same_currency =0 or domain_salesorder =1  then 1
                else  0
                end domain_salesorder,
        case
                when salesorder.same_currency =1 or domain_salesorder =1  then 1
                else  0
         end domain_facture
        ,salesorder.salesorder_rowid
        ,salesorder.same_currency
        ,salesorder.moneda_salesorder
        ,salesorder.fk_projet
        ,salesorder.salesorder_name
        ,llx_facture.fk_currency

        ,llx_facture.rowid as facture_rowid
        ,llx_facture.facnumber as facture_name,
        case
          when salesorder.same_currency =0 or domain_salesorder =1  then salesorder_name
          when salesorder.same_currency =1 and domain_salesorder =1  then salesorder_name
          when salesorder.same_currency =1 and domain_salesorder =0  then llx_facture.facnumber
        end name_domain
from
    (
		select
		  llx_salesorder.rowid as salesorder_rowid,
      CASE
        WHEN sum(llx_facture.total_ttc) < llx_salesorder.total_ttc THEN 1
        ELSE 0
		  END as domain_salesorder,
      CASE
        WHEN sum(llx_facture.total_ttc) > llx_salesorder.total_ttc THEN 1
        ELSE 0
      END as domain_facture,
      CASE
        WHEN llx_facture.fk_currency=llx_salesorder.fk_currency THEN 1
        ELSE 0
      END as same_currency,
      llx_salesorder.fk_currency as moneda_salesorder,
      llx_salesorder.fk_projet,
      llx_salesorder.ref as salesorder_name
		from llx_salesorder
		join llx_element_element on(llx_element_element.fk_source=llx_salesorder.rowid
		and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		 )
		join  llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
		group by llx_facture.fk_projet,llx_salesorder.total_ttc,llx_salesorder.rowid,llx_salesorder.fk_currency,llx_facture.fk_currency,llx_salesorder.ref
	) salesorder
	join llx_element_element on
		(
			llx_element_element.fk_source=salesorder.salesorder_rowid
			and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		)
	join llx_facture on(llx_facture.rowid=llx_element_element.fk_target);
	 -- where salesorder.fk_projet=358;
*/
create view vw_salesorder_facture_cotizacion as
 select
        case
                when salesorder.same_currency =0	then 1
                when domain_salesorder =1 		  	then 1 
                else  0
                end domain_salesorder,
        case
                when salesorder.same_currency =1 && domain_salesorder =0  then 1
                else  0
         end domain_facture
        ,salesorder.salesorder_rowid
        ,salesorder.same_currency
        ,salesorder.moneda_salesorder
        ,salesorder.fk_projet
        ,salesorder.salesorder_name
        ,llx_facture.fk_currency
        ,llx_facture.rowid as facture_rowid
        ,llx_facture.facnumber as facture_name
from
    (
		select
		  llx_salesorder.rowid as salesorder_rowid,
      CASE
        WHEN sum(llx_facture.total_ttc) < llx_salesorder.total_ttc THEN 1
        ELSE 0
		  END as domain_salesorder,
      CASE
        WHEN sum(llx_facture.total_ttc) > llx_salesorder.total_ttc THEN 1
        ELSE 0
      END as domain_facture,
      CASE
        WHEN llx_facture.fk_currency=llx_salesorder.fk_currency THEN 1
        ELSE 0
      END as same_currency,
      llx_salesorder.fk_currency as moneda_salesorder,
      llx_salesorder.fk_projet,
      llx_salesorder.ref as salesorder_name
		from llx_salesorder
		join llx_element_element on(llx_element_element.fk_source=llx_salesorder.rowid
		and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		 )
		join  llx_facture on(llx_facture.rowid=llx_element_element.fk_target)
		group by llx_facture.fk_projet,llx_salesorder.total_ttc,llx_salesorder.rowid,llx_salesorder.fk_currency,llx_facture.fk_currency,llx_salesorder.ref
	) salesorder
	join llx_element_element on
		(
			llx_element_element.fk_source=salesorder.salesorder_rowid
			and llx_element_element.sourcetype="salesorder" and llx_element_element.targettype="facture"
		)
	join llx_facture on(llx_facture.rowid=llx_element_element.fk_target);







 
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
create view vw_salesorder_facture_cotizacion_priorizada as
select 
	vw_sf.*	
    ,domain as domain1
	,case
		when domain is null then 0
		else domain
	end domain
	,ds.entidad_id
	,ds.fecha_ingreso
	,ds.id
	,
	case
	  when (domain_salesorder =1)  &&  (domain=0) 			then facture_name
       when (domain_salesorder =1)  &&  (domain=1) 			then salesorder_name
	  when (domain_salesorder =1)  &&  (domain is null) 	then salesorder_name
	  when (domain_salesorder =0)  &&  (domain is not null && domain=1) then  salesorder_name 
	  when (domain_salesorder =0) then facture_name
      else salesorder_name
	end name_domain
from 	vw_salesorder_facture_cotizacion vw_sf 
left join 	llx_consolidation_domain_salesorder ds  on(ds.entidad_id= vw_sf.salesorder_rowid ); 

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

-- Fin tabla llx_consolidation_policy
-- *************************************************************************************************************/
-- *******************************************************************************************************************



alter table llx_policy add fk_currency varchar(3) default "USD";