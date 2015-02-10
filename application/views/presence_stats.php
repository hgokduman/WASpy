            <table class="ui-responsive" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>#</th>
                        <th>Duration</th>
                        <th>% Utilization</th>
                    </tr>
                </thead>
                <tbody>
<?php
$currentDay = null;
$newDay = false;
$this->load->helper('date');
foreach($stats as $row) {
    $unix_ts = human_to_unix($row['timestamp']);
    $rowDay = date('d-m-Y', $unix_ts);
    $newDay = false;
    if($currentDay != $rowDay) {
        $currentDay = date('d-m-Y', $unix_ts);
        $newDay = true;
    }
    if($detail == 'hourly' && $newDay) {
?>
                    <tr>
                        <td colspan=4 class="presence-stats-date"><?=$currentDay;?></td>
                    </tr>
<?php
    }
?>
                    <tr>
<?php
if($detail == 'hourly') {
?>
                    <td class="presence-stats-time"><?=date('H:i:s', $unix_ts);?></td>
<?php
} else {
?>
                    <td><?=date('d-m-Y', $unix_ts);?></td>
<?php    
}
?>
                        <td><?=$row['qty'];?></td>
                        <td><?=timespan(0, $row['duration']);?></td>
                        <td><?=round($row['duration']/$periodInSeconds*100,1);?>%</td>
                    </tr>
<?php
}
?>
                </tbody>
            </table>
