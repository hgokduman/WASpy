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
  request_id varchar(30) NOT NULL,
  receipt_id varchar(30) NULL DEFAULT NULL,
  title varchar(30) NOT NULL,
  message varchar(250) NOT NULL,
  ts_sent timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ts_ack timestamp NULL DEFAULT NULL,
  PRIMARY KEY (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE %db_prefix%status (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  phone_rcpt varchar(30) NOT NULL,
  phone_from varchar(30) NOT NULL,
  status varchar(20) NOT NULL,
  ts_start datetime DEFAULT NULL,
  ts_stop datetime DEFAULT NULL,
  id_start bigint(20) DEFAULT NULL,
  id_stop bigint(20) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY phone_from (phone_from),
  KEY id_start (id_start),
  KEY ts_stop (ts_stop)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TRIGGER IF EXISTS %db_prefix%presence_status;

delimiter |

CREATE TRIGGER %db_prefix%presence_status AFTER INSERT ON %db_prefix%presence
	FOR EACH ROW
	BEGIN
		declare status_id BIGINT(20);
		
		IF NEW.status = 'available' THEN
			INSERT %db_prefix%status 	(phone_rcpt, phone_from, status, ts_start, id_start) VALUES
								(NEW.phone_rcpt, NEW.phone_from, NEW.status, NEW.received, NEW.id);
		ELSEIF NEW.status = 'unavailable' THEN
			SELECT max(id) INTO status_id FROM %db_prefix%status 
			WHERE %db_prefix%status.phone_from = NEW.phone_from 
				AND %db_prefix%status.phone_rcpt = NEW.phone_rcpt 
				AND %db_prefix%status.id_stop IS NULL
				AND %db_prefix%status.status = 'available';

				IF status_id IS NULL THEN
					INSERT %db_prefix%status 	(phone_rcpt, phone_from, status) VALUES
										(NEW.phone_rcpt, NEW.phone_from, 'available');
					set status_id = (select last_insert_id());
				END IF;
				
				UPDATE %db_prefix%status SET id_stop = NEW.id, ts_stop = NEW.received WHERE %db_prefix%status.id = status_id;
		ELSEIF NEW.status = 'start' THEN
			SELECT max(id) INTO status_id FROM %db_prefix%status 
			WHERE %db_prefix%status.phone_from = NEW.phone_from 
				AND %db_prefix%status.phone_rcpt = NEW.phone_rcpt 
				AND %db_prefix%status.id_stop IS NULL
				AND %db_prefix%status.status = 'stop';
			UPDATE %db_prefix%status SET id_stop = NEW.id, ts_stop = NEW.received WHERE %db_prefix%status.id = status_id;
		ELSEIF NEW.status = 'stop' THEN
			SELECT max(id) INTO status_id FROM %db_prefix%status 
			WHERE %db_prefix%status.phone_from = NEW.phone_from 
				AND %db_prefix%status.phone_rcpt = NEW.phone_rcpt 
				AND %db_prefix%status.id_stop IS NULL
				AND %db_prefix%status.status = 'available';
				
			IF status_id IS NOT NULL THEN
			  UPDATE %db_prefix%status SET id_stop = NEW.id, ts_stop = NEW.received WHERE %db_prefix%status.id = status_id;
			END IF;
			INSERT %db_prefix%status 	(phone_rcpt, phone_from, status, ts_start, id_start) VALUES
								(NEW.phone_rcpt, NEW.phone_from, NEW.status, NEW.received, NEW.id);
		END IF;
	END;
|

delimiter ;