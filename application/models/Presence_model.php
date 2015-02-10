<?php
class Presence_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getPresenceData($records = null, $days = null, $phone = null)
    {
        $this->db->select('id, phone_from phone_number, status, ts_start, ts_stop, TIMESTAMPDIFF(SECOND,ts_start,IFNULL(ts_stop, NOW())) duration');
        $this->db->where('id_start IS NOT NULL');
        if(!is_null($phone)) {
            $this->db->where('phone_from', $phone);
        }
        $this->db->order_by('ts_start', 'DESC');
        if(!is_null($records)) {
            $this->db->limit($records);
        }
        if(!is_null($days)) {
            $this->db->where('ts_start >', '(NOW() - INTERVAL ' . (int) $days . ' DAY)');
        }
        $query = $this->db->get('status');
 
        return $query->result_array();
    }

    public function agetPresenceData($records = 250, $days = null)
    {
        return $this->_getPresenceData($records, $days);
    }

    public function agetPresenceDataByPhoneNumber($phone, $records = 250, $days = 7)
    {
        return $this->_getPresenceData($records, $days, $phone);
    }

    public function getDailyStats($phone) {
        $this->db->select('(DATE(NOW()) - INTERVAL t2.seq DAY) + INTERVAL 0 HOUR timestamp');
        $this->db->select('count(distinct t1.id) qty');
        $this->db->select('sum(timestampdiff(	second, 
        				greatest((DATE(NOW()) - INTERVAL t2.seq DAY), t1.ts_start),
        				LEAST((DATE(NOW()) - INTERVAL t2.seq-1 DAY), IFNULL(t1.ts_stop, NOW())))) duration', false);
        $this->db->from('status as t1');
        $this->db->join('seq_table as t2', '1=1');
        $this->db->where('t1.phone_from =', $phone);
        $this->db->where('(DATE(NOW()) - INTERVAL t2.seq DAY)  IN (DATE(t1.ts_start), DATE(IFNULL(t1.ts_stop, NOW() )))');
        $this->db->where('t2.seq <=', 21);
        $this->db->group_by('DATE(NOW() - INTERVAL t2.seq DAY)');
        $this->db->order_by('DATE(NOW() - INTERVAL t2.seq DAY) DESC');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getHourlyStats($phone) {
        $this->db->select('(DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq HOUR) timestamp');
        $this->db->select('count(distinct t1.id) qty');
        $this->db->select('sum(timestampdiff(	second, 
        				greatest((DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq HOUR), t1.ts_start),
        				LEAST((DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq-1 HOUR), IFNULL(t1.ts_stop, NOW())))) duration', false);
        $this->db->from('seq_table as t2');
        $this->db->join('status as t1', '1=1');
        $this->db->where('t1.phone_from =', $phone);
        $this->db->where('(DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq HOUR) IN((DATE(t1.ts_start) + INTERVAL HOUR(t1.ts_start) HOUR), (DATE(IFNULL(t1.ts_stop, NOW())) + INTERVAL HOUR(IFNULL(t1.ts_stop, NOW())) HOUR))');
        $this->db->where('t2.seq <=', 7*24);
        $this->db->group_by('(DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq HOUR)');
        $this->db->order_by('(DATE(NOW()) + INTERVAL HOUR(NOW()) HOUR - INTERVAL t2.seq HOUR) DESC');
        $query = $this->db->get();

        return $query->result_array();
    }
}
?>
