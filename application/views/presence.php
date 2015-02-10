            <table class="ui-responsive" style="width: 100%;">
                <thead>
                    <tr>
<?php
if(is_null($phone_number)) {
?>
                        <th>Phone</th>
<?php
}
?>
                        <th>Online</th>
                        <th>Offline</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
<?php
$currentDay = null;
$newDay = false;
$this->load->helper('date');
foreach($presence as $row) {
    $unix_ts = human_to_unix($row['ts_start']);
    $rowDay = date('d-m-Y', $unix_ts);
    $newDay = false;
    if($currentDay != $rowDay) {
        $currentDay = date('d-m-Y', $unix_ts);
        $newDay = true;
    }
    
    if($newDay) {
?>
                    <tr>
                        <td colspan=<?=is_null($phone_number) ? 4 : 3;?> class="presence-stats-date"><?=$currentDay;?></td>
                    </tr>
<?php
    }
?>
                    <tr>
<?php
if(is_null($phone_number)) {
$presence_detail = $this->session->userdata('presence_detail') != false ? $this->session->userdata('presence_detail') : 'full';
?>
                        <td><a class="phone_number ui-mobile" href="/presence/<?=$row['phone_number'];?>/<?=$presence_detail;?>"><?=$row['phone_number'];?></a></td>
<?php
}
?>
                        <td <?=is_null($phone_number) ? '' : 'class="presence-stats-time"'; ?>><?=date('H:i:s', $unix_ts);?></td>
                        <td><?=!is_null($row['ts_stop']) ? date('H:i:s', human_to_unix($row['ts_stop'])) : '';?></td>
                        <td><?=timespan(0, $row['duration']);?></td>
                    </tr>
<?php
}
?>
                </tbody>
            </table>