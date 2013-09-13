CREATE TABLE thread_lists (
	id int NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL,
	maker varchar(255) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE thread_contents (
	id int NOT NULL AUTO_INCREMENT,
	thread_list_id int NOT NULL,
	writer varchar(255) NOT NULL,
	writetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	writetext text NOT NULL,
	viewflg boolean NOT NULL DEFAULT TRUE,
	PRIMARY KEY(id),
	FOREIGN KEY(thread_lists_id) REFERENCES thread_lists(id)
);
