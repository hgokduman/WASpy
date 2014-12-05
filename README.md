# WASpy #

WhatsApp Spy

----------

### Intro ###
WASpy uses venmous0x' [WhatsApi](https://github.com/venomous0x/WhatsAPI) which is freely available at Github. WASpy allows you to collect online/offline data for specific numbers (e.g. number XYZ came online at 13.03 and went offline at 13.08). You can also receive a push notification whenever a certain number has come online (and/or has gone offline).

Is someone hiding the 'last seen' timestamp? No problem. Tell WASpy to collect the data for that number, and you'll be able to calculate your own 'last seen' timestamp for that number. ;)

### Features ###

* Collect online/offline data for specific numbers
* Receive messages via WhatsApp
* Send notifications using Pushover

### TODO ###
* (G)UI

### Requirements ###

* PHP5 with CLI (tested with: PHP 5.4.4-14+deb7u14 (cli) )
* MySQL Database
* Gearman
* Gearman PHP Extension
* Cellular number exclusively for WASpy

### Installation ###

* Make sure you've got your cellular number registered with WhatsApp. You may use [yowsup](https://github.com/tgalal/yowsup) to do so and to get get the necessary ident and password. You also may use http://coderus.openrepos.net/whitesoft/whatsapp_sms to receive the confirmation code.
* Create the database tables using the DDL in database.sql (please replace %db_prefix% with prefix you want to use).
* Edit config.inc.php.dist and save as config.inc.php
* Go into the cli folder and start using ./startAll.sh
