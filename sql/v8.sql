CREATE TABLE auctions (
    id int unsigned primary key auto_increment,
    sellerid INT UNSIGNED NOT NULL,
    description text, 
    purchased bool,
    uniqueimage varchar(255),
    uniquefilename varchar(255),
    originalfilename varchar(255),
    itemname text,
    startingbid int,  
    buyoutprice int,
    reserveprice int, 
    standardauction bool,
    creationdate DATETIME NOT NULL,
    auctionend datetime NOT NULL,
    FOREIGN KEY (sellerid) REFERENCES users (id)
);

alter table users add column auction_slots int NOT NULL DEFAULT 10;



CREATE TABLE bid_history (
id int unsigned primary key auto_increment,
auctionid INT UNSIGNED NOT NULL,
bidder INT UNSIGNED NOT NULL,
bid_amount int not null,
bid_time DATETIME not null,
FOREIGN KEY (auctionid) REFERENCES auctions (id),
FOREIGN KEY (bidder) REFERENCES users (id)
);
  
  
CREATE TABLE auction_stats ( 
userID INT UNSIGNED NOT NULL,
listed_auctions int not null default 0,
sold_auctions int not null default 0,
expired_auctions int not null default 0,
unique_bids int not null default 0,
auction_wins int not null default 0, 
FOREIGN KEY (userID) REFERENCES users (id)
);
 
