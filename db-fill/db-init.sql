CREATE TABLE Users
(
    id       int unsigned AUTO_INCREMENT NOT NULL ,
    mail     varchar(45) NOT NULL UNIQUE ,
    password varchar(45) NOT NULL ,
    salt     varchar(15) NOT NULL ,
    is_admin tinyint NOT NULL DEFAULT 0 ,
    name     varchar(45) NULL ,
    phone    varchar(20) NULL ,
    birth    date NULL ,

    PRIMARY KEY (id)
);

CREATE TABLE Brands
(
    id   int unsigned AUTO_INCREMENT NOT NULL ,
    name varchar(45) NOT NULL UNIQUE ,

    PRIMARY KEY (id)
);

CREATE TABLE Sections
(
    id   tinyint unsigned AUTO_INCREMENT NOT NULL ,
    name varchar(45) NOT NULL UNIQUE ,

    PRIMARY KEY (id)
);

CREATE TABLE Categories
(
    id         int unsigned AUTO_INCREMENT NOT NULL ,
    name       varchar(45) NOT NULL UNIQUE ,
    section_id tinyint unsigned NOT NULL ,

    PRIMARY KEY (id),
    KEY `FK_2` (`section_id`),
    CONSTRAINT `FK_1` FOREIGN KEY `FK_2` (`section_id`) REFERENCES `Sections` (`id`)
);

CREATE TABLE Properties
(
    name        varchar(45) NOT NULL ,
    category_id int unsigned NOT NULL ,
    type        varchar(15) NOT NULL ,
    designation varchar(10) NULL ,

    PRIMARY KEY (name, category_id),
    KEY `FK_2` (`category_id`),
    CONSTRAINT `FK_18` FOREIGN KEY `FK_2` (`category_id`) REFERENCES `Categories` (`id`)
);

CREATE TABLE Promocodes
(
    code       varchar(45) NOT NULL ,
    discount   tinyint unsigned NOT NULL ,

    PRIMARY KEY (code)
);

CREATE TABLE Products
(
    code        int unsigned NOT NULL UNIQUE ,
    name        text NOT NULL ,
    category_id int unsigned NOT NULL ,
    brand_id    int unsigned NOT NULL ,
    price       decimal(15, 2) NOT NULL ,
    count       int unsigned NOT NULL ,
    description text NULL ,
    photo_link  text NULL ,
    discount    tinyint unsigned NULL ,

    PRIMARY KEY (code),
    KEY `FK_2` (`brand_id`),
    CONSTRAINT `FK_2` FOREIGN KEY `FK_2` (`brand_id`) REFERENCES `Brands` (`id`),
    KEY `FK_3` (`category_id`),
    CONSTRAINT `FK_3` FOREIGN KEY `FK_3` (`category_id`) REFERENCES `Categories` (`id`)
);

CREATE TABLE Product_property_values
(
    product_code  int unsigned NOT NULL ,
    property_name varchar(45) NOT NULL ,
    value       varchar(45) NOT NULL ,

    PRIMARY KEY (product_code, property_name),
    KEY `FK_2` (`product_code`),
    CONSTRAINT `FK_7` FOREIGN KEY `FK_2` (`product_code`) REFERENCES `Products` (`code`),
    KEY `FK_3` (`property_name`),
    CONSTRAINT `FK_8` FOREIGN KEY `FK_3` (`property_name`) REFERENCES `Properties` (`name`)
);

CREATE TABLE Favourite_products
(
    id           int unsigned AUTO_INCREMENT NOT NULL ,
    user_id      int unsigned NOT NULL ,
    product_code int unsigned NOT NULL ,

    PRIMARY KEY (id),
    KEY `FK_2` (`user_id`),
    CONSTRAINT `FK_9` FOREIGN KEY `FK_2` (`user_id`) REFERENCES `Users` (`id`),
    KEY `FK_3` (`product_code`),
    CONSTRAINT `FK_10` FOREIGN KEY `FK_3` (`product_code`) REFERENCES `Products` (`code`)
);

CREATE TABLE Visited_products
(
    id           int unsigned AUTO_INCREMENT NOT NULL ,
    user_id      int unsigned NOT NULL ,
    product_code int unsigned NOT NULL ,
    visit_date   datetime NOT NULL ,

    PRIMARY KEY (id),
    KEY `FK_2` (`user_id`),
    CONSTRAINT `FK_11` FOREIGN KEY `FK_2` (`user_id`) REFERENCES `Users` (`id`),
    KEY `FK_3` (`product_code`),
    CONSTRAINT `FK_12` FOREIGN KEY `FK_3` (`product_code`) REFERENCES `Products` (`code`)
);

-- CREATE TABLE Cart
-- (
--     id         int unsigned AUTO_INCREMENT NOT NULL ,
--     user_id    int unsigned NOT NULL ,
--     product_id int unsigned NOT NULL ,
--     count      int NOT NULL ,
--
--     PRIMARY KEY (id),
--     KEY FK_2 (user_id),
--     CONSTRAINT FK_15 FOREIGN KEY FK_2 (user_id) REFERENCES Users (id),
--     KEY FK_3 (product_id),
--     CONSTRAINT FK_16 FOREIGN KEY FK_3 (product_id) REFERENCES Products (id)
-- );

CREATE TABLE Orders
(
    id           int unsigned NOT NULL ,
    user_id      int unsigned NOT NULL ,
    product_code int unsigned NOT NULL ,
    created      datetime NOT NULL ,
    closed       tinyint NOT NULL ,
    count        int NOT NULL ,
    promocode    varchar(45) NULL ,

    PRIMARY KEY (id, product_code),
    KEY `FK_2` (`user_id`),
    CONSTRAINT `FK_17` FOREIGN KEY `FK_2` (`user_id`) REFERENCES `Users` (`id`),
    KEY `FK_4` (`promocode`),
    CONSTRAINT `FK_19` FOREIGN KEY `FK_4` (`promocode`) REFERENCES `Promocodes` (`code`),
    KEY `FK_4_1` (`product_code`),
    CONSTRAINT `FK_19_1` FOREIGN KEY `FK_4_1` (`product_code`) REFERENCES `Products` (`code`)
);

CREATE TABLE Product_feedback
(
    id               int unsigned AUTO_INCREMENT NOT NULL ,
    user_id          int unsigned NOT NULL ,
    product_code     int unsigned NOT NULL ,
    created          date NOT NULL ,
    comment          text NULL ,
    rating           tinyint unsigned NULL ,
    reply_comment_id int NULL ,

    PRIMARY KEY (id),
    KEY `FK_2` (`user_id`),
    CONSTRAINT `FK_13` FOREIGN KEY `FK_2` (`user_id`) REFERENCES `Users` (`id`),
    KEY `FK_3` (`product_code`),
    CONSTRAINT `FK_14` FOREIGN KEY `FK_3` (`product_code`) REFERENCES `Products` (`code`)
);


-- SELECT * FROM (SELECT * FROM Products WHERE Products.id = 1) as Product
--     JOIN Categories on Product.category_id = Categories.id
--     JOIN Category_properties Cp on Categories.id = Cp.category_id
--     JOIN Properties P on Cp.property_id = P.id
--     JOIN Product_property_values Ppv on P.id = Ppv.property_id;