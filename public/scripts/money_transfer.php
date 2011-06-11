<?php
require "../../includes.php";
if(empty($_SESSION['login'])) {
	kick('login?kickback='.htmlspecialchars(kickback_url()));
}
$user = new User($_SESSION['login']);
$db->autocommit(false);
try{
	$transaction = new AccountTransaction();
	$transaction->description = ClientData::post('description');
	$transaction->user = $user->__toString();
	$from = new AccountTransactionContent();
	$from->amount = -1*ClientData::post('amount');
	$from->account_id = ClientData::post('from_account_id');
	$to = new AccountTransactionContent();
	$to->amount = ClientData::post('amount');
	$to->account_id = ClientData::post('to_account_id');
	$transaction->commit();
	$from->account_transaction_id = $transaction->id;
	$to->account_transaction_id = $transaction->id;
	$from->commit();
	$to->commit();
	$db->commit();
} catch(Exception $e) {
	die("Nånting gick fel:<pre>{$e->getMessage()}</pre>");
}
kick('accounts');
?>
