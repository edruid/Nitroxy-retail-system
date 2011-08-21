create table `product_package` (
	`product_id` int unsigned not null,
	`package` int unsigned not null,
	`count` smallint not null default 1,
	PRIMARY KEY (`product_id`, `package`),
	FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
	FOREIGN KEY (`package`) REFERENCES `products` (`product_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
