CREATE TABLE session (
  sess_id varchar(255) NOT NULL,
  sess_data text NOT NULL,
  sess_time int(11) NOT NULL,
  PRIMARY KEY (sess_id)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;