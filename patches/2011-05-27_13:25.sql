ALTER TABLE `transaction_contents`
	ADD COLUMN `stock_usage` decimal(10,4) not null default 0;

UPDATE `transaction_contents` SET
	`stock_usage` = `transaction_contents`.`count` * (
		SELECT 
			 `products`.`value`
		FROM
			`products`
		WHERE
			`products`.`product_id` = `transaction_contents`.`product_id`
	);
