CREATE TABLE `account` (
	`account_id` int unsigned not null auto_increment,
	`name` varchar(30) not null unique,
	`default_sign` enum('debit', 'kredit') not null,
	`warn_on_non_default` boolean not null default false,
	`description` text,
	`account_type` enum('asset', 'debt', 'cost', 'income', 'special'),
	PRIMARY KEY (`account_id`)
) engine=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1;

INSERT INTO `account` SET
	`name` = "Kassa",
	`default_sign` = 'kredit',
	`description` = 'Pengarna som ligger i kassalådan',
	`account_type` = 'asset';
INSERT INTO `account` SET
	`name` = "Diff",
	`default_sign` = 'debit',
	`description` = 'Skillnad mot vad som borde finnas i kassan/kontot',
	`account_type` = 'asset';
INSERT INTO `account` SET
	`name` = "Inköp varor",
	`default_sign` = 'debit',
	`description` = 'Pengar spenderade på att köpa varor',
	`account_type` = 'cost';
INSERT INTO `account` SET
	`name` = "Bank",
	`default_sign` = 'debit',
	`description` = 'Pengarna som ligger i på banken',
	`account_type` = 'asset';

CREATE TABLE `account_transaction` (
	`account_transaction_id` int unsigned not null auto_increment,
	`description` text,
	`user` varchar(100),
	`timestamp` timestamp default CURRENT_TIMESTAMP,
	PRIMARY KEY (`account_transaction_id`)
) engine=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `account_transaction_contents` (
	`account_transaction_id` int unsigned not null,
	`account_id` int unsigned not null,
	`amount` decimal(10,2) not null,
	PRIMARY KEY (`account_transaction_id`, `account_id`),
	FOREIGN KEY (`account_transaction_id`) REFERENCES `account_transaction` (`account_transaction_id`),
	FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`)
) engine=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

