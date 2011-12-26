insert into categories  set category_id = 0, name = 'Special';
update categories set category_id = 0 where name = 'Special';
insert into products set product_id = 0, name = 'Öresavrundning', price = 0, ean = 'Öresavrundning', category_id = '0', value = 0, count = 0;
update products set product_id = 0 where name = 'Öresavrundning';
