<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";

ADMIN::check();

stdhead("MySQL Query Editor");



if (isset($_POST['submitquery'])) {
       if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
       echo('<p><b>Query:</b><br />'.nl2br($_POST['query']).'</p>');
mysql_select_db($_POST['db']);
       $result = mysql_query($_POST['query']);
       if ($result) {
               if (@mysql_num_rows($result)) {
                       ?>
                       <p><b>Result Set:</b></p>
                       <table border="1">
                       <thead>
                       <tr>
                       <?php
                       for ($i=0;$i<mysql_num_fields($result);$i++) {
                               echo('<th>'.mysql_field_name($result,$i).'</th>');
                       }
                       ?>
                       </tr>
                       </thead>
                       <tbody>
                       <?php
                       while ($row = mysql_fetch_row($result)) {
                               echo('<tr>');
                               for ($i=0;$i<mysql_num_fields($result);$i++) {
                                       echo('<td>'.$row[$i].'</td>');
                               }
                               echo('</td></tr>');
                       }
                       ?>
                       </tbody>
                       </table>
                       <?php
               } else {
                       echo('<p><b>Query OK:</b> '.mysql_affected_rows().' rows affected.</p>');
               }
       } else {
               echo('<p><b>Query Failed:</b> '.mysql_error().'</p>');
       }
       echo('<hr />');
}
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">

<p>SQL Query:<br />
<textarea onFocus="this.select()" cols="60" rows="5" name="query">
<?=htmlspecialchars($_POST['query'])?>
</textarea>
</p>
<p><input type="submit" name="submitquery" value="Submit Query (Alt-S)" accesskey="S" /></p>
</form>

<?php
cpfooter();
end_main_frame();

stdfoot();
