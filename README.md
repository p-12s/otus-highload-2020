# otus-highload-2020  
Практика по курсу "Архитектор высоких нагрузок"  
  
Требования:  
PHP-FPM >= 7.2  
NGINX  
MYSQL  
  
Установка:  
https://www.digitalocean.com/community/tutorials/how-to-install-nginx-on-ubuntu-18-04  
https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-16-04  
  
БД и пользователь:  
CREATE DATABASE your-db-name CHARACTER SET utf8 COLLATE utf8_general_ci;  
CREATE USER 'your-user-name'@'localhost' IDENTIFIED BY 'your-password';  
GRANT ALL PRIVILEGES ON * . * TO 'your-user-name'@'localhost'; 
для создания таблиц нужно выполнить миграции  
  
