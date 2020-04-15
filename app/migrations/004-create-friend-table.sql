CREATE TABLE `friend` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_friend` int(11) NOT NULL,
    CONSTRAINT FK_UserId FOREIGN KEY (id_user) REFERENCES user(id),
    CONSTRAINT FK_FriendId FOREIGN KEY (id_friend) REFERENCES user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
