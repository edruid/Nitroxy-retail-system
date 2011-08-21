ALTER TABLE account
	ADD column `code_name` varchar(16) default null unique,
	MODIFY column `account_type` enum('balance', 'result');
