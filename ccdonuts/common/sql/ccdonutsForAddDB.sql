create table purchase (
	id int not null primary key, 
	customer_id int NOT NULL, 
	card_id int NOT NULL,
	purchase_date int(8) NOT NULL,

	foreign key(customer_id) references customers(id),
	foreign key(card_id) references cards(id)
);

create table purchase_detail (
	purchase_id int not null, 
	product_id int not null, 
	count int not null, 
	primary key(purchase_id, product_id), 
	foreign key(purchase_id) references purchase(id), 
	foreign key(product_id) references products(id)
);

create table favorite (
	customer_id int not null, 
	product_id int not null, 
	primary key(customer_id, product_id), 
	foreign key(customer_id) references customers(id), 
	foreign key(product_id) references products(id)
);

CREATE TABLE cards (
	id int NOT NULL AUTO_INCREMENT,
    `customer_id` int NOT NULL,
	`name` varchar(100) NOT NULL,
	`card_number` int NOT NULL, 
    `card_company` int NOT NULL,
    `limit_year` int(2) NOT NULL,
    `limit_month` int(2) NOT NULL,
    `code` varchar(6) NOT NULL,

	PRIMARY KEY(`id`), 
	FOREIGN KEY(`customer_id`) REFERENCES customers(`id`)
);