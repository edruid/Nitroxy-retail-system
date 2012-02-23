SET FOREIGN_KEY_CHECKS=0;
REPLACE INTO account set
	account_id = 1,
	name = 'Kassa',
	code_name = 'till',
	default_sign = 'kredit',
	warn_on_non_default = false,
	description = 'Pengar som finns i kassaapparaten',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 2,
	name = 'Differenser',
	code_name = 'diff',
	default_sign = 'kredit',
	warn_on_non_default = false,
	description = 'Skillnader vid kassaräkning mot vad som borde finnas',
	account_type = 'result';
REPLACE INTO account set
	account_id = 3,
	name = 'Inköp försäljningsvaror',
	code_name = 'purchases',
	default_sign = 'debit',
	warn_on_non_default = true,
	description = 'Pengar vi använt för att köpa varor',
	account_type = 'result';
REPLACE INTO account set
	account_id = 4,
	name = 'Bank',
	code_name = 'bank',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Pengar som ligger på bankkontot',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 5,
	name = 'Skuld/förskott till engagerad',
	code_name = 'engagerad',
	default_sign = 'kredit',
	warn_on_non_default = false,
	description = 'Pengar lånade av privatperson t ex för inköp',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 7,
	name = 'Lager',
	code_name = 'stock',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Pengar bundna i varor i kiosken',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 9,
	name = 'Lagerdifferenser',
	code_name = 'stock_diff',
	default_sign = 'kredit',
	warn_on_non_default = false,
	description = 'Skillnader vid inventering mot vad som borde finnas',
	account_type = 'result';
REPLACE INTO account set
	account_id = 10,
	name = 'Leverantörsskulder',
	code_name = 'deliveries_debts',
	default_sign = 'kredit',
	warn_on_non_default = false,
	description = 'Pengar vi är skyldiga leverantörer',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 11,
	name = 'Inköp förbrukningsinventarier',
	code_name = 'spend',
	default_sign = 'debit',
	warn_on_non_default = true,
	description = 'Saker som köpts till lokalen för förbrukning',
	account_type = 'result';
REPLACE INTO account set
	account_id = 12,
	name = 'Förseningsavgifter',
	code_name = 'delay_fees',
	default_sign = 'debit',
	warn_on_non_default = true,
	description = 'Avgifter för sena betalningar',
	account_type = 'result';
REPLACE INTO account set
	account_id = 13,
	name = 'Försäljning',
	code_name = 'sales',
	default_sign = 'kredit',
	warn_on_non_default = true,
	description = 'Försäljning av varor',
	account_type = 'result';
REPLACE INTO account set
	account_id = 14,
	name = 'Medlemsavgifter',
	code_name = 'member_fees',
	default_sign = 'kredit',
	warn_on_non_default = true,
	description = 'Inkomster av medlemsavgifter',
	account_type = 'result';
REPLACE INTO account set
	account_id = 15,
	name = 'Donationer',
	code_name = 'donations',
	default_sign = 'kredit',
	warn_on_non_default = true,
	description = 'Inkomster av donnationer',
	account_type = 'result';
REPLACE INTO account set
	account_id = 16,
	name = 'Pant',
	code_name = 'pant',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Pengar bundna i pant',
	account_type = 'balance';
REPLACE INTO account set
	account_id = 17,
	name = 'Ingående',
	code_name = 'start',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Ingående värden för start av systemet',
	account_type = 'result';
REPLACE INTO account set
	account_id = 18,
	name = 'Öresavrundningar',
	code_name = 'rounding',
	default_sign = 'debit',
	warn_on_non_default = false,
	description = 'Öresavrundningar vid inköp, försäljning mm',
	account_type = 'result';
SET FOREIGN_KEY_CHECKS=1;
