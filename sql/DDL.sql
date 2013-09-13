CREATE DATABASE db01 CHARACTER SET utf8

GRANT SELECT ,INSERT ,UPDATE ,DELETE ON db01.* TO user01@"localhost" IDENTIFIED BY "user01";

CREATE TABLE db01.thread_lists (
	id int NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL
	PRIMARY KEY (id)
);

CREATE TABLE db01.thread_contents (
	id int NOT NULL AUTO_INCREMENT,
	thread_list_id int NOT NULL,
	writer varchar(255) NOT NULL,
	writetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	writetext text NOT NULL,
	viewflg boolean NOT NULL DEFAULT TRUE,
	PRIMARY KEY(id),
	FOREIGN KEY(thread_list_id) REFERENCES thread_lists(id)
);
