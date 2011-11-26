<?php
require "include/bittorrent.php";
loggedinorreturn();
lang::load( "contactstaff" ) ;
stdhead(str1, false);
        ?>
        <table class=main width=450 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
        <div align=center>
        <h1><?=str2?></h1>

        <form method=post name=message action=takecontact.php>
        <?php if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
        <input type=hidden name=returnto value="<?=htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>">
        <?php } ?>
        <table class=message cellspacing=0 cellpadding=5>
        <tr><td<?=$replyto?" colspan=2":""?>>
        <b>&nbsp;&nbsp;<?=str3?>: </b><br><input type=text size=83 name=subject style='margin-left: 5px'>
        <?php
        textbbcode("message","msg","$body");
        ?></td></tr>

        <tr><td align=center><input type=submit value="<?=str3?>" class=btn></td></tr>

        </table>
        </form>
  </div></td></tr></table>
        
<?php
stdfoot();
?>