CREATE DATABASE IF NOT EXISTS passedout;

USE passedout;

CREATE TABLE IF NOT EXISTS user (
	user_id				bigint(20)	NOT NULL AUTO_INCREMENT,
	user_name			varchar(64) NOT NULL,
	user_password_hash 	varchar(60)	NOT NULL,
	CONSTRAINT PK_USER PRIMARY KEY (user_id),
	CONSTRAINT U_USER_NAME UNIQUE KEY (user_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS marker (
	marker_id			bigint(20)	NOT NULL AUTO_INCREMENT,
	user_id				bigint(20)	NOT NULL,
	guid				varchar(36) NOT NULL,
	lat					varchar(10) NOT NULL,
	lng					varchar(10) NOT NULL,
	title				text		NULL,
	description			text		NULL,
	CONSTRAINT PK_MARKER PRIMARY KEY (marker_id),
	CONSTRAINT FK_MARKER_USER FOREIGN KEY (user_id) REFERENCES user (user_id),
	CONSTRAINT U_MARKER_USER_GUID UNIQUE KEY (user_id, guid)
) ENGINE=InnoDB;