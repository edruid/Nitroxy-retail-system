BEGIN;
ALTER TABLE products ADD COLUMN account_id INT UNSIGNED DEFAULT NULL,
ADD FOREIGN KEY (account_id) REFERENCES account (account_id);

UPDATE products SET account_id = (SELECT account_id FROM account WHERE code_name = 'rounding') WHERE product_id = 0;
COMMIT;
