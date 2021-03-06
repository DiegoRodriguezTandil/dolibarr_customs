-- ===================================================================
-- Copyright (C) 2000-2002 Rodolphe Quiedeville <rodolphe@quiedeville.org>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ===================================================================

create table llx_compta
(
  rowid             integer AUTO_INCREMENT PRIMARY KEY,
  datec             datetime,
  datev             date,           -- date de valeur
  amount            real DEFAULT 0 NOT NULL ,
  label             varchar(255),
  fk_compta_account integer,
  fk_user_author    integer,
  fk_user_valid     integer,
  valid             tinyint DEFAULT 0,
  note              text

)ENGINE=innodb;
