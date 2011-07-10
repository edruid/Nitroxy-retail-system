CREATE TABLE `users` (
	`user_id` int(10) unsigned not null,
	`first_name` varchar(100) not null,
	`surname` varchar(100) not null,
	`username` varchar(100) not null,
	PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO users (`user_id`, `first_name`, `surname`, `username`) VALUES
	(100, 'Eric', 'Druid', 'druid')
	(, 'Robert', 'Lövlie', 'topace')
	(, 'Uffe', 'Lilja', 'wolhay')
	(, 'Kjell', 'Liden', 'kjellej')
	(, 'Adam', 'Höse', 'adisbladis')
	(, 'Patrik', 'Roos', 'roos')
	(, 'Peter', 'Eriksson', 'coopdot')

ALTER TABLE account_transaction
