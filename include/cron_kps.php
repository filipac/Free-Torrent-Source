<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
    //=== Update karma seeding bonus
    /******************************************************
    * Use ONLY one of the two options below...
    * the first is per torrents seeded, the second will only give the bonus for ONE torrent no matter how many are seeded.
    * 
    * also you will have to play with how much bonus you want to give...
    * ie: seedbonus+0.0225 = 0.25 bonus points per hour
    * seedbonus+0.125 = 0.5 bonus points per hour
    * seedbonus+0.225 = 1 bonus point per hour
    *****************************************************/

    //======use this part to give seeding bonus per torrent
    /*
    * $res = mysql_query("SELECT DISTINCT userid FROM peers WHERE seeder = 'yes'") or sqlerr(__FILE__, __LINE__);
    * 
    * if (mysql_num_rows($res) > 0)
    * {
    * while ($arr = mysql_fetch_assoc($res))
    * {
    * $work = mysql_query("select count(*) from peers WHERE seeder ='yes' AND userid = $arr[userid]");
    * $row_count = mysql_result($work,0,"count(*)");
    * mysql_query("UPDATE users SET seedbonus = seedbonus+0.250*$row_count WHERE id = $arr[userid]") or sqlerr(__FILE__, __LINE__);
    * }
    * }   */

    //==use this part to only give seeding bonus for 1 torrent no matter how many are being seeded
   /* $kpsbonus = mysql_query( "SELECT DISTINCT userid FROM peers WHERE seeder = 'yes'" ) or
     *   sqlerr( __file__, __line__ ) ;
    *if ( mysql_num_rows($kpsbonus) > 0 )
    *{
     *   while ( $kps = mysql_fetch_assoc($kpsbonus) )
      *  {
       *     UserHandle::KPS( "+", "0.225", $kps["userid"] ) ;
       * }
    *}
    * //===end */
    
     $res = mysql_query("SELECT DISTINCT userid FROM peers WHERE seeder = 'yes'") or sqlerr(__FILE__, __LINE__);
     
     if (mysql_num_rows($res) > 0)
     {
     while ($arr = mysql_fetch_assoc($res))
     {
     $work = mysql_query("select count(*) from peers WHERE seeder ='yes' AND userid = $arr[userid]");
     $row_count = mysql_result($work,0,"count(*)");
     mysql_query("UPDATE users SET seedbonus = seedbonus+0.250*$row_count WHERE id = $arr[userid]") or sqlerr(__FILE__, __LINE__);
     }
     }  
     ?>