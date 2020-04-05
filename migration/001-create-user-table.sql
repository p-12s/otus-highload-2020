CREATE TABLE `user` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `screen_name` varchar(255) NOT NULL,
  `user_link` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
