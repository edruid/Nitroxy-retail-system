INSERT INTO account set
	name = 'Öresavrundningar',
	code_name = 'rounding',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Öresavrundningar vid inköp, försäljning mm',
	account_type = 'result';

insert into account_transaction_contents
	(account_id, account_transaction_id, amount)
	select
		(select account_id from account where code_name = 'rounding') as account_id,
		account_transaction_id,
		-sum(amount)
	from account_transaction_contents
	group by account_transaction_id
	having abs(sum(amount)) <= 0.5 and sum(amount)!= 0;
