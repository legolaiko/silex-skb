CREATE TABLE user (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  username varchar(128) NOT NULL,
  password varchar(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX UK_user_id (id)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Table containing user entities';

CREATE TABLE user_role (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Table containing list of all available user roles';

CREATE TABLE user_to_user_role (
  user_id int(10) UNSIGNED NOT NULL,
  role_id int(10) UNSIGNED NOT NULL,
  CONSTRAINT FK_user_to_user_role_user_id FOREIGN KEY (user_id)
  REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FK_user_to_user_role_user_role_id FOREIGN KEY (role_id)
  REFERENCES user_role (id) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_unicode_ci
COMMENT = 'Adjacency table for ''user'' and ''user_role''';