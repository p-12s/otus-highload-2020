CREATE TABLE `profile` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `first_name` varchar(255) NOT NULL,
    `last_name` varchar(255) NOT NULL,
    `birthday` date DEFAULT NULL,
    `user_id` int(11) NOT NULL,
    `gender` varchar(255) DEFAULT NULL,
    `profile_pic` varchar(255) NOT NULL,
    `cover_pic` varchar(255) NOT NULL,
    FOREIGN KEY profile_user_fk(user_id) REFERENCES user(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
