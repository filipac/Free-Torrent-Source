<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
ReadConfig( 'WORDCENSOR' ) ;
$wordlist = FFactory::configoption($WORDCENSOR['words'],"ass|assface|assfuck|asshole|bastard|beaner|bitch|blowjob|boner|chink|clit|cock|cocksucker|cooter|
cracker|damn|dick|dickhead|dickhole|dickwod|dildo|douche|douchebag|dumass|fag|fagtard|fuck|
fucker|fuckface|fuckhole|fucking|fucktard|fuckwit|gay|goddamnit|gringo|hell|motherfucker|motherfucking|
whore|twat|slut|shit|fuck") ;
?>