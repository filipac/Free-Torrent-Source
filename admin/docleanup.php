<?php

$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;



ADMIN::check();

docleanup();
global $CURUSER;
write_log("Cleanup has been called by $CURUSER[username] - ".date( "d.m.Y H:i:s" ));
stdhead('DONE');
fancy('Do Cleanup');
print("Done");
stdfoot();
?>