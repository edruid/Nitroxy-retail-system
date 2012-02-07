begin;
delete from account_transaction_contents
where exists(
	select 1 from account a
	where   account_transaction_contents.account_id = a.account_id
	and a.code_name in ('stock_change', 'purchases')
)
and exists(
	select 1 from account_transaction at
	where account_transaction_contents.account_transaction_id = at.account_transaction_id
	and at.description like 'Inköp id: %'
);

update account_transaction_contents set
	account_id = (select account_id from account where code_name = 'purchases')
where exists (
	select 1 from account a
	where   account_transaction_contents.account_id = a.account_id
	and code_name = 'stock_change'
)
and exists(
	select 1 from account_transaction at
	where account_transaction_contents.account_transaction_id = at.account_transaction_id
	and description = 'Dagsavslut'
);

delete from account where code_name = 'stock_change';
commit;

