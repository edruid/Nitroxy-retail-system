<?php
require_once "../../includes.php";
if(empty($_SESSION['login'])) {
	kick('login?kickback='.kickback_url('daily_count'));
}
$time = date('Y-m-d H:i:s');
$daily_count = DailyCount::last();
$sales_amount = Transaction::sum('amount', array(
	'timestamp:>' => $daily_count->time,
	'timestamp:<=' => $time,
));

$old_till = AccountTransactionContent::sum('amount', array(
	'Account.code_name' => 'till',
	'AccountTransaction.timestamp:>' => $daily_count->time,
	'AccountTransaction.timestamp:<=' => $time,
));
$old_till += $daily_count->amount;

$stock_amount = TransactionContent::sum('stock_usage', array(
	'Transaction.timestamp:>' => $daily_count->time,
	'Transaction.timestamp:<=' => $time,
));
	

$db->autocommit(false);

$transaction = new AccountTransaction();
$transaction->description = 'Dagsavslut';
$transaction->user_id = $_SESSION['login'];
$transaction->timestamp = $time;
$transaction->commit();

$sales = new AccountTransactionContent();
$sales->account_transaction_id = $transaction->id;
$sales->account_id = Account::from_code_name('sales')->id;
$sales->amount = -$sales_amount;
$sales->commit();

$till = new AccountTransactionContent();
$till->account_transaction_id = $transaction->id;
$till->account_id = Account::from_code_name('till')->id;
$till->amount =  ClientData::post('till') - $old_till;
$till->commit();

$diff = new AccountTransactionContent();
$diff->account_transaction_id = $transaction->id;
$diff->account_id = Account::from_code_name('diff')->id;
$diff->amount = -($till->amount + $sales->amount);
$diff->commit();

$stock = new AccountTransactionContent();
$stock->account_transaction_id = $transaction->id;
$stock->account_id = Account::from_code_name('stock')->id;
$stock->amount = - $stock_amount;
$stock->commit();

$stock_usage = new AccountTransactionContent();
$stock_usage->account_transaction_id = $transaction->id;
$stock_usage->account_id = Account::from_code_name('stock_change')->id;
$stock_usage->amount = $stock_amount;
$stock_usage->commit();

$daily_count = new DailyCount();
$daily_count->time = $time;
$daily_count->amount = ClientData::post('till');
$daily_count->account_transaction_id = $transaction->id;
$daily_count->user = $_SESSION['login'];
$daily_count->commit();

$db->commit();
?>
