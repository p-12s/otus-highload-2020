CREATE TABLE `user` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(100) NOT NULL,
    `token` varchar(255) NOT NULL,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `birthday` date DEFAULT NULL,
    `country` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `interests` varchar(255) DEFAULT NULL,
    `gender` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
