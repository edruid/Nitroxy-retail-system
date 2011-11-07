DROP PROCEDURE IF EXISTS merge_products;
DELIMITER $$
CREATE PROCEDURE merge_products( IN _main int, IN _merge int ) 
MODIFIES SQL DATA
BEGIN
	DECLARE _value decimal(10,4);
	DECLARE _count int;
	DECLARE _inventory_threshold int;
	
	-- Update purchases
	INSERT INTO transaction_contents (transaction_id, product_id, count, amount, stock_usage)
		(SELECT transaction_id, _main, count, amount, stock_usage FROM transaction_contents AS tc WHERE tc.product_id = _merge)
		ON DUPLICATE KEY UPDATE
			count = transaction_contents.count + tc.count,
			stock_usage = transaction_contents.stock_usage + tc.stock_usage,
			amount = transaction_contents.amount + tc.amount;
	DELETE FROM transaction_contents WHERE product_id = _merge;

	-- Remove product log, there is no good way to keep it.
	DELETE FROM product_log WHERE product_id = _merge;
	
	-- update deliveries (and stock takings)
	INSERT INTO delivery_contents (delivery_id, product_id, count, cost)
		(SELECT delivery_id, _main, count, cost FROM delivery_contents AS dc WHERE dc.product_id = _merge)
		ON DUPLICATE KEY UPDATE
			cost =  COALESCE((delivery_contents.cost * delivery_contents.count + dc.cost * dc.count) / (delivery_contents.count + dc.count), delivery_contents.cost),
			count = delivery_contents.count + dc.count;
	DELETE FROM delivery_contents WHERE product_id = _merge;

	-- Update product packages
	UPDATE product_package SET product_id = _main WHERE product_id = _merge;
	UPDATE product_package SET package = _main WHERE package = _merge;

	-- Update _main product
	SELECT count, value, inventory_threshold INTO _count, _value, _inventory_threshold FROM products WHERE product_id = _merge;
	UPDATE products SET
		value = COALESCE((value * count + _count * _value) / (count + _count), value),
		count = count + _count,
		inventory_threshold = COALESCE(inventory_threshold, _inventory_threshold)
		WHERE products.product_id = _main;
	
	-- Delete _merge product
	DELETE FROM products WHERE product_id = _merge;
END $$

DELIMITER ;

CALL merge_products(377, 117); -- 7-up
CALL merge_products(348, 110); -- champis
CALL merge_products(378, 112); -- coca cola
CALL merge_products(474, 111); -- dr pepper
CALL merge_products(379, 103); -- extrem cola
CALL merge_products(375, 223); -- fanta exotic
CALL merge_products(448, 225); -- fanta lemon
CALL merge_products(447, 127); -- loka nat
CALL merge_products(405, 113); -- pepsi max
CALL merge_products(406, 321); -- premier cola
CALL merge_products(380, 109); -- vimto
CALL merge_products(244, 162); -- Champis glas
CALL merge_products(323, 458); -- delicato mazarin
CALL merge_products(408, 102); -- festis cactus lime
CALL merge_products(388, 250); -- Festis orange
CALL merge_products(227, 387); -- Festis strawberry
CALL merge_products(244, 234); -- Grape tonic
CALL merge_products(153, 247); -- Lakritsbitar
CALL merge_products(244, 163); -- loka nat glas
CALL merge_products(244, 168); -- loka päron glas
CALL merge_products(244, 238); -- fruktsoda
CALL merge_products(244, 237); -- hallonsoda
CALL merge_products(244, 164); -- päronsoda
CALL merge_products(244, 239); -- sockerdricka
CALL merge_products(244, 169); -- ramlösa
CALL merge_products(244, 161); -- trocadero
CALL merge_products(151, 426); -- twix 58g
