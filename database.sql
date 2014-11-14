CREATE TABLE %db_prefix%events (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  event_name varchar(50) NOT NULL,
  workload text NOT NULL,
  inserted datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE %db_prefix%lastseen (
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  msgid varchar(100) NOT NULL,
  lastseen datetime NOT NULL,
  received datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE %db_prefix%messages (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  msgid varchar(50) NOT NULL,
  msgtype varchar(20) NOT NULL,
  msgtime datetime NOT NULL,
  sender varchar(50) NOT NULL,
  body varchar(5000) DEFAULT NULL,
  received datetime NOT NULL,
  receipt datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE %db_prefix%presence (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  status varchar(20) NOT NULL,
  received datetime NOT NULL,
  PRIMARY KEY (id),
  KEY presence_ix1 (phone_from,received)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE %db_prefix%seq_table (
  seq int(5) NOT NULL,
  PRIMARY KEY (seq)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE %db_prefix%subscriptions_active (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  activated datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY phone_rcpt (phone_rcpt,phone_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE %db_prefix%subscriptions_history (
  id bigint(20) NOT NULL,
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  activated datetime NOT NULL,
  deactivated datetime NOT NULL,
  reason varchar(200) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE %db_prefix%notifications (
  phone_from varchar(20) NOT NULL,
  eventName varchar(30) NOT NULL,
  eventValue varchar(100) NOT NULL,
  PRIMARY KEY (phone_from,eventName,eventValue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE %db_prefix%pushmessages (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title varchar(30) NOT NULL,
  message varchar(250) NOT NULL,
  ts_sent timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ts_ack timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;