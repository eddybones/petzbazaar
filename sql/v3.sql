alter table shop_stock add column quantity int not null default 1;
alter table boutique_stock add column quantity int not null default 1;
alter table ledger add column itemID int unsigned;
alter table ledger ADD CONSTRAINT itemID FOREIGN KEY (itemID) REFERENCES shop_stock (id) on delete set null;

CREATE Table boutique_ledger (
id int unsigned primary key auto_increment,
buyerid int unsigned,
sellerid int unsigned,
price int,
purchasedate datetime,
itemID int unsigned,
FOREIGN KEY (buyerid) REFERENCES users (id),
FOREIGN KEY (sellerid) REFERENCES users (id),
FOREIGN KEY (itemID) REFERENCES boutique_stock (id) on delete set null
);
alter table boutique_stock add column itemtype int;