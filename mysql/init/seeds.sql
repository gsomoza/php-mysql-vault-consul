USE myapp;

CREATE TABLE IF NOT EXISTS config (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`key` VARCHAR(255) NOT NULL,
`value` VARCHAR(255)  DEFAULT NULL
)ENGINE=innodb;

DELETE FROM config WHERE `key` = "hello_world";
INSERT INTO config (`key`, `value`) VALUES ("hello_world", "Hello World!");
