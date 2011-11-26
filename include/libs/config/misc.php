<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

$payment_paypal_address = FFactory::configoption(@dbv('payment_paypal_email'),'torentc@yahoo.com');
$payment_paypal_enable = FFactory::configoption(@dbv('payment_paypal_enable'),'yes');
$payment_paypal_amounts = FFactory::configoption(@dbv('payment_paypal_amounts'),'5:10:15:20:25:30:50:100');
$payment_paypal_curency = FFactory::configoption(@dbv('payment_paypal_curency'),'USD');
$payement_wire_enable = FFactory::configoption(@dbv('payment_wire_enable'),'yes');

$_pg_enable = FFactory::configoption(@dbv('pg_enable'),'yes');
$_pg_server = FFactory::configoption(@dbv('pg_server'),'http://freetosu.sourceforge.net/pg.txt');
$_ads = FFactory::configoption(@dbv('ads'),'---');
?>