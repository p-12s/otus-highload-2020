CREATE TABLE `city` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_country` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    FOREIGN KEY id_country_fk(id_country) REFERENCES country(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
