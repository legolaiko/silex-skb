-- User schema

CREATE TABLE user__role (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  role varchar(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX UK_user__role_name (role)
)
ENGINE = INNODB
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 16384
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Table containing list of all available user roles';

CREATE TABLE user__role_to_user (
  user_id int(10) UNSIGNED NOT NULL,
  role_id int(10) UNSIGNED NOT NULL,
  CONSTRAINT FK_user_to_user_role_user_id FOREIGN KEY (user_id)
  REFERENCES user__user (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_user_to_user_role_user_role_id FOREIGN KEY (role_id)
  REFERENCES user__role (id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = INNODB
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Adjacency table for ''user'' and ''role''';

CREATE TABLE user__user (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  username varchar(128) NOT NULL,
  nickname varchar(255) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  enabled tinyint(1) DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE INDEX UK_user__user_id (id),
  UNIQUE INDEX UK_user__user_username (username)
)
ENGINE = INNODB
AUTO_INCREMENT = 12
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Table containing user entities';

INSERT INTO  user__role (name) VALUES ('ROLE_USER');