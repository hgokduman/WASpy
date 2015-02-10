<!DOCTYPE html> 
<html>
<head>
    <title>WASpy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <link rel="stylesheet" href="/assets/style.css" />
    <script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="/assets/script.js"></script>
    <link rel="apple-touch-icon" href="/assets/shortcut-icon.png" />
    <link rel="shortcut icon" href="/assets/shortcut-icon.png" type="image/png" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
</head>
<body>
    <div data-role="page">
        <div id="toolbar-header" data-role="header" data-position="fixed" data-tap-toggle="false" class="jqm-header">
	   <a href="/" data-icon="home" data-role="button" data-mini="true" data-iconpos="notext" rel="external" class="ui-btn-left">Home</a>
            <a href="#panel_options" data-role="button" data-icon="bars" data-mini="true" data-iconpos="notext" class="ui-btn-right">Options</a>
            <span id="ui-title" class="ui-title"><?=isset($phone_number) ? '+' . $phone_number : '';?></span>
        </div>
        <div data-role="panel" id="panel_options" data-position="left" data-display="push">
            <ul data-role="listview"  data-theme="a" data-dividertheme="a" class="ui-listview-outer">
                <li data-role="collapsible" data-iconpos="right" data-corners="false" data-shadow="false" data-theme="a">
                        <h2>People</h2>
                        <ul data-role="listview" data-corners="false" data-shadow="false">
<?php $presence_detail = $this->session->userdata('presence_detail') != false ? $this->session->userdata('presence_detail') : 'full'; ?>
                                <li><a href="/presence/31633365922/<?=$presence_detail;?>">Burçin</a></li>
                                <li><a href="/presence/31681506917/<?=$presence_detail;?>">Esra</a></li>
                                <li><a href="/presence/31629543440/<?=$presence_detail;?>">Halim</a></li>
                                <li><a href="/presence/905439266520/<?=$presence_detail;?>">Merve</a></li>
                                <li><a href="/presence/31648988351/<?=$presence_detail;?>">Semra</a></li>
                                <li><a href="/presence/31624240487/<?=$presence_detail;?>">Tuğçe</a></li>
                        </ul>
                </li>
<?php
$phone_number = $this->uri->segment(2);
if($this->router->fetch_class() == 'presence' && isset($phone_number)) {
?>
                <li data-role="collapsible" data-iconpos="right" data-corners="false" data-shadow="false" data-theme="a">
    <h2>View</h2>
    <form id="choose-view">
      <fieldset data-role="controlgroup" data-type="horizontal">
        <label>Full<input type="radio" id="choose-view-full" value="full" name="choose-view" <?=($detail=='full') ? 'checked="checked"' : ''; ?>></label>
        <label>Hourly<input type="radio" id="choose-view-hourly" value="hourly" name="choose-view" <?=($detail=='hourly') ? 'checked="checked"' : ''; ?>></label>
        <label>Daily<input type="radio" id="choose-view-daily" value="daily" name="choose-view" <?=($detail=='daily') ? 'checked="checked"' : ''; ?>></label>
      </fieldset>
    </form></li>                
<?php
}
?>
                <li data-role="list-divider">OAuth</li>
                <li data-icon="info"><a href="/OAuth">Info</a></li>
<?php
if($this->auth->isLoggedIn()) {
?>
                <li data-icon="delete"><a href="/OAuth/logout" rel="external">Logout</a></li>
<?php
} else {
?>
                <li data-icon="star"><a href="/OAuth/login" rel="external">Login</a></li>
<?php
}
?>

            </ul>
        </div>
