CREATE TABLE `daily_count` (
	`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`amount` decimal(10,2) NOT NULL,
	`account_transaction_id` int unsigned DEFAULT NULL,
	`user` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
	PRIMARY KEY (`time`),
	FOREIGN KEY (`account_transaction_id`) REFERENCES `account_transaction` (`account_transaction_id`)
);
