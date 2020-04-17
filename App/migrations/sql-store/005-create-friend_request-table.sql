CREATE TABLE `friend_request` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_initiator` int(11) NOT NULL,
    `id_recipient` int(11) NOT NULL,
    `status` char(1) NOT NULL,
    `request_date` date NOT NULL,
    CONSTRAINT FK_InitiatorId FOREIGN KEY (id_initiator) REFERENCES user(id),
    CONSTRAINT FK_RecipientId FOREIGN KEY (id_recipient) REFERENCES user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
