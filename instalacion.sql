CREATE USER 'tutorial'@'localhost' IDENTIFIED BY 'test1234';
CREATE DATABASE tutorial_db CHARACTER SET = 'utf8';
GRANT ALL PRIVILEGES ON tutorial_db.* TO 'tutorial'@'localhost';
FLUSH PRIVILEGES;

