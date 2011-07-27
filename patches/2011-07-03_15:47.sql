DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`user_id` int(10) unsigned not null,
	`first_name` varchar(100) not null,
	`surname` varchar(100) not null,
	`username` varchar(100) not null unique,
	PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

INSERT INTO users (`user_id`, `first_name`, `surname`, `username`) VALUES
	(100, 'Eric', 'Druid', 'druid'),
	(1, 'Robert', 'Lövlie', 'topace'),
	(2, 'Uffe', 'Lilja', 'wolhay'),
	(3, 'Kjell', 'Linden', 'kjellej'),
	(4, 'Adam', 'Höse', 'adisbladis'),
	(5, 'Patrik', 'Roos', 'roos'),
	(6, 'Peter', 'Eriksson', 'coopdot'),
	(7, 'Nils', 'Linde', 'nyct3a');

select 'account_transaction';
ALTER TABLE account_transaction add column user_id int unsigned not null;
update account_transaction set user_id = (select user_id from users where concat(first_name, ' \'', username, '\' ', surname) like concat(`user`, '%'));
ALTER TABLE account_transaction add foreign key (user_id) REFERENCES users (user_id), drop column `user`;

select 'daily_count';
ALTER TABLE daily_count add column user_id int unsigned not null;
update daily_count set user_id = (select user_id from users where concat(first_name, ' \'', username, '\' ', surname) like concat(`user`, '%'));
ALTER TABLE daily_count add foreign key (user_id) REFERENCES users (user_id), drop column `user`;

select 'deliveries';
ALTER TABLE deliveries add column user_id int unsigned not null;
update deliveries set user_id = (select user_id from users where concat(first_name, ' \'', username, '\' ', surname) like concat(`user`, '%')) WHERE user != '';
update deliveries set user_id = 100 where user_id = 0;
ALTER TABLE deliveries add foreign key (user_id) REFERENCES users (user_id), drop column `user`;

select 'product_log';
DELETE FROM product_log where user = " '' ";
ALTER TABLE product_log add column user_id int unsigned not null;
update product_log set user_id = (select user_id from users where concat(first_name, ' \'', username, '\' ', surname) = `user`);
ALTER TABLE product_log add foreign key (user_id) REFERENCES users (user_id), drop column `user`;

