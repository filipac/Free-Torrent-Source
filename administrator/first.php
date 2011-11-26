<?php
include_once "../forums/functions/ver.php";
include "func.php";
/** Start 
 * @module: Beta
 **/
if(IS_BETA_FTS OR IS_BETA_FF):
admin_table_start("Beta WARNING");
if(IS_BETA_FTS) {
		echo "You are using Free Torrent Source version ".VERSION." which is marked as BETA. If you find a bug, please report it <a href=\"http://freetosu.berlios.de/bug/bug_report_page.php\">here</a>";	
		}
	if(IS_BETA_FF) {
		echo "<BR>You are using Free Forums version ".FFver." which is marked as BETA. If you find a bug, please report it <a href=\"http://freetosu.berlios.de/bug/bug_report_page.php\">here</a>";	
		}
		admin_table_end();
		echo emptyp();
		endif;
/** End 
 * @module: Beta
 **/
 /** Start 
 * @module: Statistics
 **/
		admin_special_start("FTS ".VERSION." Statistics");
	admin_stats();
	admin_special_end();
	echo emptyp();
/** End 
 * @module: Statistics
 **/
/** Start 
 * @module: Importants Links
 **/
	admin_special_start("Important FTS Links");
	$str = '
	<tr>
	<td class="alt1" width="20%"><div align="left"><b>FTS Blog</b></div></td>
	<td class="alt2"><div align="left"><b><a href="http://freetosu.berlios.de/blog" target="_blank">http://freetosu.berlios.de/blog</a></b></div></td>
	</tr>
	<tr>
	<td class="alt1" width="20%"><div align="left"><b>FTS Forums</b></div></td>
	<td class="alt2"><div align="left"><b><a href="http://freetosu.berlios.de/forums" target="_blank">http://freetosu.berlios.de/forums</a></b></div></td>
	</tr>
	<tr>
	<td class="alt1" width="20%"><div align="left"><b>FTS Wiki</b></div></td>
	<td class="alt2"><div align="left"><b><a href="http://freetosu.berlios.de/wiki" target="_blank">http://freetosu.berlios.de/wiki</a></b></div></td>
	</tr>
	<tr>
	<td class="alt1" width="20%"><div align="left"><b>FTS SVN</b></div></td>
	<td class="alt2"><div align="left"><b><a href="http://my-svn.assembla.com/svn/freetosu" target="_blank">http://my-svn.assembla.com/svn/freetosu</a></b></div></td>
	</tr>
	<tr>
	<td class="alt1" width="20%"><div align="left"><b>FTS Trac</b></div></td>
	<td class="alt2"><div align="left"><b><a href="http://my-trac.assembla.com/freetosu" target="_blank">http://my-trac.assembla.com/freetosu</a></b></div></td>
	</tr>
';
	echo $str;
	admin_special_end();
	echo emptyp();
/** End 
 * @module: Importants Links
 **/
/** Start 
 * @module: Usefull Resources
 **/
	admin_special_start("Usefull Resources");
echo <<<usefull
<tr>
<td class="alt1" width="20%"><div align="left"><b>PHP Function Lookup</b></div></td>
<td class="alt2"><div align="left"><b><form action="http://www.php.net/manual-lookup.php" method="get" style="display:inline">
					<input type="text" class="bginput" name="function" size="30" tabindex="1" />

					<input type="submit" value=" Find " class="button" tabindex="1" />
				</form></b></div></td>
</tr>
<tr><td class="alt1" width="20%"><div align="left"><b>Usefull Links</b></div></td>
<td class="alt2"><div align="left"><b><select onchange="if (this.options[this.selectedIndex].value != '') { window.open(this.options[this.selectedIndex].value); } return false;" tabindex="1" class="bginput">

						<option value="">-- Useful Links --</option>		<optgroup label="PHP">
		<option value="http://www.php.net/">Home Page (PHP.net)</option>
		<option value="http://www.php.net/manual/">Reference Manual</option>
		<option value="http://www.php.net/downloads.php">Download Latest Version</option>
		</optgroup>
		<optgroup label="MySQL">

		<option value="http://www.mysql.com/">Home Page (MySQL.com)</option>
		<option value="http://www.mysql.com/documentation/">Reference Manual</option>
		<option value="http://www.mysql.com/downloads/">Download Latest Version</option>
		</optgroup>
		<optgroup label="Apache">
		<option value="http://httpd.apache.org/">Home Page (Apache.org)</option>
		<option value="http://httpd.apache.org/docs/">Reference Manual</option>

		<option value="http://httpd.apache.org/download.cgi">Download Latest Version</option>
		</optgroup>

					</select>
</b></div></td></tr>
usefull;
	admin_special_end();
	echo emptyp();
/** End 
 * @module: Usefull Resources
 **/
/** Start 
 * @module: Version Check
 **/
	version_check();
/** End 
 * @module: Version Check
 **/
/** Start 
 * @module: Quick Notes
 **/
	 admin_table_start("Quick Notes");
form_start("write.php",'post');
$adminnotes = FFactory::configoption(@get('admin_note'),'Welcome to FTS Control Panel');
echo '<textarea cols="120" rows="10" name=content>';
echo $adminnotes;
echo '</textarea><input type=submit value=Write>';
form_end();
admin_table_end(); echo emptyp();
/** End 
 * @module: Quick Notes
 **/
?>