alter table products modify price decimal(10,2) not null;
alter table products modify value decimal(8,4) not null;
alter table delivery_contents modify cost decimal(8,4) not null;
alter table transaction_contents modify amount decimal(10,2) not null;
alter table transactions modify amount decimal(10,2) not null;
