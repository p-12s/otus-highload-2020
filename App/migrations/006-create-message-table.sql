CREATE TABLE `message` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_user_sender` int(11) NOT NULL,
    `id_user_recipient` int(11) NOT NULL,
    `text` varchar(255) NOT NULL,
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY id_user_sender_fk(id_user_sender) REFERENCES user(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY id_user_recipient_fk(id_user_recipient) REFERENCES user(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
