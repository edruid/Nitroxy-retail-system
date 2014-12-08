CREATE TABLE `product_log` (
  `product_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` text COLLATE utf8_swedish_ci,
  PRIMARY KEY (`product_log_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci
