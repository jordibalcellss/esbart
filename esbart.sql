-- esbart.sql
-- one time tokens database

CREATE DATABASE esbart;
USE esbart;

CREATE TABLE IF NOT EXISTS pw_set_requests (
  id INT NOT NULL AUTO_INCREMENT,
  pass CHAR(16) NOT NULL,
  user_id VARCHAR(20) NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expired TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE (pass),
  PRIMARY KEY (id)
);
