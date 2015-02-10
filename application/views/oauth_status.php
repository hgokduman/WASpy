<h2>OAuth Status</h2>
<?php
if(isset($redirect)) {
?>
<script type="text/javascript">
    //setTimeout(function(){ window.location.replace('/'); }, <?=$redirect;?>);
</script>
<?php
}
?>
<table class="status-list">
    <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach($this->auth->getUserDetails() as $key=>$value) {
?>
    <tr>
        <td><?=$key;?></td>
        <td><?=$value;?></td>
    </tr>
<?php
}
?>
    </tbody>
</table>
<pre></pre>
