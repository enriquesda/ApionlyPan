ALTER TABLE `grupos_bombeo` CHANGE `potencia_bomba` `potencia_bomba` DECIMAL(10,2) NOT NULL;
UPDATE grupos_bombeo SET potencia_bomba = 1 WHERE id = 1;
UPDATE grupos_bombeo SET potencia_bomba = 1.5 WHERE id = 2;
UPDATE grupos_bombeo SET potencia_bomba = 2 WHERE id = 3;
UPDATE grupos_bombeo SET potencia_bomba = 2.5 WHERE id = 4;
UPDATE grupos_bombeo SET potencia_bomba = 3 WHERE id = 5;
UPDATE grupos_bombeo SET potencia_bomba = 3.5 WHERE id = 6;
UPDATE grupos_bombeo SET potencia_bomba = 4 WHERE id = 7;
UPDATE grupos_bombeo SET potencia_bomba = 4.5 WHERE id = 8;
UPDATE grupos_bombeo SET potencia_bomba = 5 WHERE id = 9;
UPDATE grupos_bombeo SET potencia_bomba = 5.5 WHERE id = 10;
UPDATE grupos_bombeo SET potencia_bomba = 6 WHERE id = 11;
UPDATE grupos_bombeo SET potencia_bomba = 6.5 WHERE id = 12;
UPDATE grupos_bombeo SET potencia_bomba = 7 WHERE id = 13;
UPDATE grupos_bombeo SET potencia_bomba = 7.5 WHERE id = 14;
UPDATE grupos_bombeo SET potencia_bomba = 8 WHERE id = 15;
UPDATE grupos_bombeo SET potencia_bomba = 8.5 WHERE id = 16;
UPDATE grupos_bombeo SET potencia_bomba = 9 WHERE id = 17;
UPDATE grupos_bombeo SET potencia_bomba = 9.5 WHERE id = 18;
UPDATE grupos_bombeo SET potencia_bomba = 10 WHERE id = 19;
UPDATE grupos_bombeo SET potencia_bomba = 10.5 WHERE id = 20;
UPDATE grupos_bombeo SET potencia_bomba = 11 WHERE id = 21;
UPDATE grupos_bombeo SET potencia_bomba = 11.5 WHERE id = 22;
UPDATE grupos_bombeo SET potencia_bomba = 12 WHERE id = 23;
UPDATE grupos_bombeo SET potencia_bomba = 12.5 WHERE id = 24;
UPDATE grupos_bombeo SET potencia_bomba = 13 WHERE id = 25;
UPDATE grupos_bombeo SET potencia_bomba = 13.5 WHERE id = 26;
UPDATE grupos_bombeo SET potencia_bomba = 14 WHERE id = 27;
UPDATE grupos_bombeo SET potencia_bomba = 14.5 WHERE id = 28;
UPDATE grupos_bombeo SET potencia_bomba = 15 WHERE id = 29;
UPDATE grupos_bombeo SET potencia_bomba = 15.5 WHERE id = 30;
UPDATE grupos_bombeo SET potencia_bomba = 16 WHERE id = 31;
UPDATE grupos_bombeo SET potencia_bomba = 16.5 WHERE id = 32;
UPDATE grupos_bombeo SET potencia_bomba = 17 WHERE id = 33;
UPDATE grupos_bombeo SET potencia_bomba = 17.5 WHERE id = 34;
UPDATE grupos_bombeo SET potencia_bomba = 18 WHERE id = 35;
UPDATE grupos_bombeo SET potencia_bomba = 18.5 WHERE id = 36;
UPDATE grupos_bombeo SET potencia_bomba = 19 WHERE id = 37;
UPDATE grupos_bombeo SET potencia_bomba = 19.5 WHERE id = 38;
UPDATE grupos_bombeo SET potencia_bomba = 20 WHERE id = 39;
UPDATE grupos_bombeo SET potencia_bomba = 20.5 WHERE id = 40;
UPDATE grupos_bombeo SET potencia_bomba = 21 WHERE id = 41;
UPDATE grupos_bombeo SET potencia_bomba = 21.5 WHERE id = 42;
UPDATE grupos_bombeo SET potencia_bomba = 22 WHERE id = 43;
UPDATE grupos_bombeo SET potencia_bomba = 22.5 WHERE id = 44;
UPDATE grupos_bombeo SET potencia_bomba = 23 WHERE id = 45;
UPDATE grupos_bombeo SET potencia_bomba = 23.5 WHERE id = 46;
UPDATE grupos_bombeo SET potencia_bomba = 24 WHERE id = 47;
UPDATE grupos_bombeo SET potencia_bomba = 24.5 WHERE id = 48;
UPDATE grupos_bombeo SET potencia_bomba = 25 WHERE id = 49;
UPDATE grupos_bombeo SET potencia_bomba = 25.5 WHERE id = 50;
UPDATE grupos_bombeo SET potencia_bomba = 26 WHERE id = 51;
UPDATE grupos_bombeo SET potencia_bomba = 26.5 WHERE id = 52;
UPDATE grupos_bombeo SET potencia_bomba = 27 WHERE id = 53;
UPDATE grupos_bombeo SET potencia_bomba = 27.5 WHERE id = 54;
UPDATE grupos_bombeo SET potencia_bomba = 28 WHERE id = 55;
UPDATE grupos_bombeo SET potencia_bomba = 28.5 WHERE id = 56;
UPDATE grupos_bombeo SET potencia_bomba = 29 WHERE id = 57;
UPDATE grupos_bombeo SET potencia_bomba = 29.5 WHERE id = 58;
UPDATE grupos_bombeo SET potencia_bomba = 30 WHERE id = 59;
UPDATE grupos_bombeo SET potencia_bomba = 30.5 WHERE id = 60;
UPDATE grupos_bombeo SET potencia_bomba = 31 WHERE id = 61;
UPDATE grupos_bombeo SET potencia_bomba = 31.5 WHERE id = 62;
UPDATE grupos_bombeo SET potencia_bomba = 32 WHERE id = 63;
UPDATE grupos_bombeo SET potencia_bomba = 32.5 WHERE id = 64;
UPDATE grupos_bombeo SET potencia_bomba = 33 WHERE id = 65;
UPDATE grupos_bombeo SET potencia_bomba = 33.5 WHERE id = 66;
UPDATE grupos_bombeo SET potencia_bomba = 34 WHERE id = 67;
UPDATE grupos_bombeo SET potencia_bomba = 34.5 WHERE id = 68;
UPDATE grupos_bombeo SET potencia_bomba = 35 WHERE id = 69

ALTER TABLE `cultivo_parcela` ADD `agua` INT NULL COMMENT 'consumo de agua (m3/ha)' AFTER `superficie_cultivada`;
ALTER TABLE `cultivo_parcela` ADD `dias_ciclo` INT NULL COMMENT 'dias hasta recoleccion' AFTER `produccion_t_ha`;
UPDATE `fitosanitarios` SET `materia_activa` = 'COBRE' WHERE `fitosanitarios`.`id` = 80; UPDATE `fitosanitarios` SET `materia_activa` = 'COBRE' WHERE `fitosanitarios`.`id` = 82;
ALTER TABLE aperos_cultivo ADD PRIMARY KEY (`id_apero`, `id_cultivo_parcela`);
ALTER TABLE ciclovida_bd.parcelas ADD fecha_baja TIMESTAMP NULL;
ALTER TABLE ciclovida_bd.cultivo_parcela MODIFY COLUMN cosecha tinyint(1) DEFAULT 1 NOT NULL COMMENT 'Boolean para indicar si la cosecha se realiza de forma manual, (true (1)) o mediante maquinaria, es decir cosechadora (false (0))';
ALTER TABLE `cultivo_parcela` ADD `combustible_bomba` TINYINT(3) NULL COMMENT '1 diesel, 2gasolina, 3 electrica' AFTER `bomba`;
ALTER TABLE `cultivo_parcela` ADD `n_sectores` TINYINT NOT NULL COMMENT 'Numero de sectores para calcular superficie de los sectores' AFTER `cosecha`;
ALTER TABLE `cultivo_parcela` CHANGE `cosecha` `cosecha` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Boolean para indicar si la cosecha se realiza de forma manual, (true (1)) o mediante maquinaria, es decir cosechadora (false (0))';

/**MOFICACIONES NUEVA VERSIÓN TID_4AGRO
*/
ALTER TABLE `fitosanitarios` ADD `tipo` TINYINT NOT NULL DEFAULT '1' COMMENT 'Para indicar si es un herbicida, un plaguicida, bactericida, fungicida, acaricida, rodenticida , otro, 1, 2, 3, 4, 5, 6, 7 ' AFTER `densidad`;

ALTER TABLE `fitosanitarios_cultivo` DROP FOREIGN KEY `fitosanitarios_cultivo_ibfk_1`; ALTER TABLE `fitosanitarios_cultivo` ADD CONSTRAINT `fitosanitarios_cultivo_ibfk_1` FOREIGN KEY (`id_fitosanitario`) REFERENCES `fitosanitarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE
ALTER TABLE `cultivo_parcela` CHANGE `produccion_t_ha` `produccion_t_ha` DECIMAL(9,3) NULL DEFAULT NULL;
ALTER TABLE `cultivo_parcela` ADD `caudal_gotero` DECIMAL(5,3) NULL AFTER `n_sectores`;
ALTER TABLE `maquinaria_agricultor` ADD `id_sensor` INT NULL AFTER `id_agricultor`;
ALTER TABLE `maquinaria_agricultor` ADD `device` VARCHAR(16) NULL COMMENT 'Identifica el dispositivo (sensor por nombre en mensaje LoRa)' AFTER `id_sensor`;
ALTER TABLE `maquinaria` ADD `img` VARCHAR(255) NULL COMMENT '255 caracteres para guardar el path de la imagen' AFTER `reparacion`;

    /**
        Dentro de los cambios de TID_4AGRO, cambios del 05/05/2025
    */

CREATE TABLE `cicloVida_bd`.`tanque_fertilizantre` (`id` INT NOT NULL AUTO_INCREMENT , `id_sensor` INT NULL , `alto` DECIMAL(10,3) NOT NULL , `ancho` DECIMAL(10,3) NULL , `largo` DECIMAL(10,3) NULL , `volumen` DECIMAL(10,3) NULL , `porcentaje_P` DECIMAL(5,3) NOT NULL , `porcentaje_N` DECIMAL(5,3) NOT NULL , `porcentaje_K` DECIMAL(5,3) NOT NULL , `nombre_fert` VARCHAR(255) NULL COMMENT 'Nombre del fertilizante si se quisiera especificar' , `id_parcela` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`id_sensor`)) ENGINE = InnoDB COMMENT = 'Información de tanque de fertilizante líquido';
ALTER TABLE `tanque_fertilizantre` ADD CONSTRAINT `fk_id_parcela_tank` FOREIGN KEY (`id_parcela`) REFERENCES `parcelas`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
CREATE TABLE `cicloVida_bd`.`fertilizantes` (`id` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(255) NOT NULL , `porcentaje_P` INT NOT NULL , `porcentaje_K` INT NOT NULL , `porcentaje_N` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `cicloVida_bd`.`aplicaciones_fertilizantes` (`id` INT NOT NULL AUTO_INCREMENT , `id_cultivo` INT NOT NULL , `id_agricultor` INT NOT NULL , `porcentaje_K` DECIMAL(5,3) NULL , `porcentaje_N` DECIMAL(5,3) NULL , `porcentaje_P` DECIMAL(5,3) NULL , `kg_ha` DECIMAL(10,2) NULL , `unidades_K` DECIMAL(6,3) NULL , `unidades_P` DECIMAL(6,3) NULL , `unidades_N` DECIMAL(6,3) NULL , `fecha` TIMESTAMP NULL , PRIMARY KEY (`id`), INDEX (`id_cultivo`), INDEX (`id_agricultor`)) ENGINE = InnoDB;
ALTER TABLE `aplicaciones_fertilizantes` ADD CONSTRAINT `fk_fertilizante_aplic_cultivo` FOREIGN KEY (`id_cultivo`) REFERENCES `cultivo_parcela`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `aplicaciones_fertilizantes` ADD CONSTRAINT `fk_agricultor_aplica_fert` FOREIGN KEY (`id_agricultor`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `fertilizantes_cultivo` DROP FOREIGN KEY `fertilizantes_cultivo_ibfk_1`; ALTER TABLE `fertilizantes_cultivo` ADD CONSTRAINT `fertilizantes_cultivo_ibfk_1` FOREIGN KEY (`id_cultivo_parcela`) REFERENCES `cultivo_parcela`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `aplicaciones_fertilizantes` ADD `km` DECIMAL(8,2) NOT NULL COMMENT 'kilometros de ida y vuelta hasta el lugar de compra' AFTER `fecha`;
ALTER TABLE `users` CHANGE `rol` `rol` INT NOT NULL DEFAULT '3';
ALTER TABLE `aplicaciones_fertilizantes` ADD `id_lectura_sensor` INT NULL COMMENT 'sirve para indicar si la cantidad de fertilizante aplicado viene de la lectura del sensor de nivel del tanque.' AFTER `km`;

/**
Ultimas modificaciones lunes 12 de mayo de 2025
**/
ALTER TABLE `fertilizantes` CHANGE `porcentaje_P` `porcentaje_P` DECIMAL(5,2) NOT NULL, CHANGE `porcentaje_K` `porcentaje_K` DECIMAL(5,2) NOT NULL, CHANGE `porcentaje_N` `porcentaje_N` DECIMAL(5,2) NOT NULL;

/** Modificaciones 14/05/2025
    Tambien necesario ejetura: php artisan storage:link
    Esto ultimo sirve para linkear la carpeta en /storage/app/public con la carpeta /public/storage para servir archivos directamente con http desde la carpeta public de laravel .
*/
ALTER TABLE `aplicaciones_fertilizantes` ADD `nombre_fert` VARCHAR(128) NULL COMMENT 'Si lo hubiera nombre del fertilizante si no inespecifico' AFTER `id_lectura_sensor`;
ALTER TABLE `maquinaria` CHANGE `consumo_h_ha` `consumo_l_ha` DECIMAL(4,2) NOT NULL;
ALTER TABLE `maquinaria_agricultor` ADD `id` INT NOT NULL AUTO_INCREMENT COMMENT 'El agricultor puede tener varias maquinas iguales por ello hay que identificarlas en esta tabla pivote con un id unico' FIRST, ADD PRIMARY KEY (`id`);

/**
Hasta aqui esta actualizado en producción con lo cual de aqui para arriba no hay que ejecutar sentencias.
De aqui para abajo habrá que insertar esos cambios en la bd de producción
**/
CREATE TABLE policy_current ( id INT PRIMARY KEY AUTO_INCREMENT, policy_url VARCHAR(255) NOT NULL, policy_version VARCHAR(32) NOT NULL, policy_hash CHAR(64) NOT NULL, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP );
ALTER TABLE `policy_current` ADD `created_at` TIMESTAMP NOT NULL AFTER `updated_at`;

CREATE TABLE `consents` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `policy_version` int(11) NOT NULL,
  `fecha_aceptacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_revocado` timestamp NULL DEFAULT NULL,
  `ip_user` varchar(100) NOT NULL,
  `hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `consents`
--
ALTER TABLE `consents`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `consents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
ALTER TABLE `users` ADD `consent_id` INT NULL COMMENT 'id referente a la tabla de consetimiento de la política de privacidad' AFTER `updated_pass`;
ALTER TABLE `users` ADD INDEX(`consent_id`);
ALTER TABLE `users` ADD CONSTRAINT `fk_consent_it` FOREIGN KEY (`consent_id`) REFERENCES `consents`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- Hasta aqui ya esta todo en producción actualizado 02/06/2025 
ALTER TABLE fitosanitarios_cultivo ADD UNIQUE(`id_cultivo_parcela`, `id_fitosanitario`); --Esto también está en producción hay que ejecutar en la versión de la bd de la oficina

ALTER TABLE `consents`
  ADD UNIQUE KEY `id_user` (`id_user`,`policy_version`),
  ADD KEY `fk_policy_version` (`policy_version`);


ALTER TABLE `consents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `consents`
  ADD CONSTRAINT `fk_id_user_policy_acceptance` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_policy_version` FOREIGN KEY (`policy_version`) REFERENCES `policy_current` (`id`);
COMMIT;

ALTER TABLE `aperos_agricultor` ADD `id_sensor` INT NOT NULL AFTER `id_agricultor`, ADD `device` VARCHAR(100) NOT NULL AFTER `id_sensor`, ADD `id` INT NOT NULL AUTO_INCREMENT AFTER `device`, ADD PRIMARY KEY (`id`);
ALTER TABLE `parcelas` ADD `id_parcela_pp` VARCHAR(32) NULL COMMENT 'id parcela plataforma de Pedro uex' AFTER `superficie`;
ALTER TABLE `cultivo_parcela` ADD `id_sector_pp` VARCHAR(31) NULL COMMENT 'id sector plataforma pedro uex' AFTER `n_sectores`;
ALTER TABLE `aperos` ADD `img` VARCHAR(100) NULL COMMENT 'nombre de la imagen si la tiene' AFTER `updated_at`;
ALTER TABLE `aperos_agricultor` CHANGE `id_sensor` `id_sensor` INT(11) NULL;
ALTER TABLE `aperos_agricultor` CHANGE `device` `device` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
