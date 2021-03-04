CREATE TABLE `zasilkovna_packetery_carriers` (
	`carrier_id` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci',
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`country` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`currency` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`maxWeight` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`labelRouting` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`labelName` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`pickupPoints` SMALLINT(1) NULL DEFAULT NULL,
	`separateHouseNumber` SMALLINT(1) NULL DEFAULT NULL,
	`customsDeclarations` SMALLINT(1) NULL DEFAULT NULL,
	`disallowsCod` SMALLINT(1) NULL DEFAULT NULL,
	`requiresPhone` SMALLINT(1) NULL DEFAULT NULL,
	`requiresEmail` SMALLINT(1) NULL DEFAULT NULL,
	`requiresSize` SMALLINT(1) NULL DEFAULT NULL,
	`apiAllowed` SMALLINT(1) NULL DEFAULT NULL,
	`in_feed` SMALLINT(1) NULL DEFAULT NULL,
	PRIMARY KEY (`carrier_id`) USING BTREE
)
	COLLATE='utf8_general_ci'
	ENGINE=InnoDB
;
