<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
loggedinorreturn();
$dateformat = "m-d-Y";
ADMIN::check();
function _form_open_ ($values = '', $hidden_values = '')
  {
    global $_this_script_;
    global $act;
    echo '<form method="post" action="' . $_this_script_ . '">
	<input type="hidden" name="act" value="' . $act . '">';
    if (is_array ($values))
    {
      foreach ($values as $val)
      {
        echo $val;
      }
    }
    else
    {
      if (!empty ($values))
      {
        echo $values;
      }
    }

    if (is_array ($hidden_values))
    {
      foreach ($hidden_values as $hidden)
      {
        echo $hidden;
      }

      return null;
    }

    if (!empty ($hidden_values))
    {
      echo $hidden_values;
    }

  }

  function _form_close_ ($button = 'save')
  {
    echo '<input type="submit" value="' . $button . '" class="btn"></form>';
  }

  function _form_header_open_ ($text, $colspan = 4)
  {
    echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
    echo '<tbody><tr><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="thead" colspan="' . $colspan . '" align="center">' . $text . '</td></tr>';
  }

  function _form_header_close_ ()
  {
    echo '</table></tbody></td></tr></table></tbody>';
  }

  define ('MR_VERSION', 'v0.1 by xam');
  $do = (isset ($_GET['do']) ? htmlspecialchars ($_GET['do']) : (isset ($_POST['do']) ? htmlspecialchars ($_POST['do']) : ''));
  if ($do == 'request_reseed_final')
  {
    $requestfrom = $_POST['requestfrom'];
    $sender = intval ($_POST['sender']);
    $postedtorrents = $_POST['torrents'];
    if (!empty ($postedtorrents))
    {
      $subject = trim ($_POST['subject']);
      $message = trim ($_POST['message']);
      if ((!empty ($subject) AND !empty ($message)))
      {
        if ($requestfrom == 'owner')
        {
          $query = sql_query ('' . 'SELECT t.owner, t.name, t.id, u.username, u.class FROM torrents t INNER JOIN users u ON (t.owner=u.id) WHERE t.id IN (' . $postedtorrents . ') AND seeders = 0');
		}
        else
        {
          $query = sql_query ('' . 'SELECT s.userid as owner, s.torrent_name as name, s.torrentid as id, u.username, u.class FROM snatched s INNER JOIN users u ON (s.userid = u.id) WHERE s.finished = \'yes\' AND s.torrentid IN (' . $postedtorrents . ')');
        }

        while ($torrent = mysql_fetch_assoc ($query))
        {
          $torrenturl = '[b][url=' . $BASEURL . '/details.php?id=' . $torrent['id'] . ']' . $torrent['name'] . '[/url][/b]';
          $msg = str_replace (array ('{username}', '{torrentname}'), array ($torrent['username'], $torrenturl), $message);
          (sql_query ('' . 'INSERT INTO messages (sender, receiver, subject, msg, added) VALUES (' . $sender . ', ' . $torrent['owner'] . ', ' . sqlesc ($subject) . ', ' . sqlesc ($msg) . ', NOW())') OR sqlerr (__FILE__, 47));
        }

        if ($_POST['doubleupload'] == 'yes')
        {
          sql_query ('' . 'UPDATE torrents set doubleupload = \'yes\' WHERE id IN (' . $postedtorrents . ')');
        }
      }
    }
  }

  if ($do == 'request_reseed')
  {
    $torrents = $_POST['torrents'];
    $implode = @implode (',', $torrents);
    if (0 < sizeof ($torrents))
    {
      stdhead ('Request Reseed for Weak Torrents - Request Message');
      echo '
		<script>
			function TSdoubleupload()
			{
				whatselected = document.forms[\'reseed\'].elements[\'doubleupload\'].value;
				TSnewinput = "\\nPlease Note: Once you start to Re-seed this torrent, you will get Double Upload Credits!";
				if (whatselected == "yes")
				{					
					document.forms[\'reseed\'].elements[\'message\'].focus();
					document.forms[\'reseed\'].elements[\'message\'].value =					
					document.forms[\'reseed\'].elements[\'message\'].value + TSnewinput;
					document.forms[\'reseed\'].elements[\'message\'].focus();
				}
				else
				{
					var str = document.forms[\'reseed\'].elements[\'message\'].value;
					var TSnewtext = str.replace(TSnewinput, "");
					document.forms[\'reseed\'].elements[\'message\'].value = TSnewtext;	
				}
			}
		</script>
		<form method="POST" action="' . $_this_script_ . '" name="reseed">
		<input type="hidden" name="do" value="request_reseed_final">
		<input type="hidden" name="torrents" value="' . $implode . '">
		';
      _form_header_open_ ('Request Reseed for Weak Torrents - Request Message', 2);
      echo '
		<tr>
			<td>Subject</td><td><input type="text" size="40" value="Re-seed Request" name="subject"></td></tr>
		</tr>
		<tr>
			<td>Message</td><td><textarea name="message" cols="70" rows="15">
Hi {username},

Please Re-seed the following torrent as soon as possible: {torrentname}

Have a great day,
' . $SITENAME . ' Team.
</textarea></td>
		</tr>
		<tr>
			<td>Double Upload</td><td><select name="doubleupload" onchange="javascript:TSdoubleupload()"><option value="yes">YES</option><option value="no" selected="selected">NO</option></select> Give Double Uploaded amount users who begin to reseed this torrent!</td>
		</tr>
		<tr>
			<td>Sender</td><td><select name="sender"><option value="0">System</option><option value="' . $CURUSER['id'] . '">' . $CURUSER['username'] . '</option></select> <b>Please Note: </b>Do not change {username} and {torrentname} tags which will be automaticly renamed by system.</td>
		</tr>
		<tr>
			<td>Request from</td><td><select name="requestfrom"><option value="owner">Uploader Only</option><option value="all">All snatched users</option></select></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Request Reseed"> <input type="reset" value="Reset Form"></td></tr>
		</tr>';
      echo '</form>';
      _form_header_close_ ();
      end_frame();
	  stdfoot ();
      exit ();
    }
  }

  $res = sql_query ('SELECT id FROM torrents WHERE seeders = 0');
  $row = mysql_fetch_array ($res);
  $count = $row[0];
  //list ($pagertop, $pagerbottom, $limit) = pager ($ts_perpage, $count, $_this_script_ . '&');
  stdhead ('Request Reseed for Weak Torrents - Show Torrents');
  echo '
	<form method="POST" action="' . $_this_script_ . '" name="reseed">
	<input type="hidden" name="do" value="request_reseed">
	';
  _form_header_open_ ('Request Reseed for Weak Torrents - Show Torrents', 7);
  echo '
<tr>	
	<td class="colhead" align="left" width="45%">Name</td>
	<td class="colhead" align="center" width="20%">Added</td>
	<td class="colhead" align="center" width="10%">Owner</td>
	<td class="colhead" align="center" width="5%">Seeders</td>
	<td class="colhead" align="center" width="5%">Leechers</td>
	<td class="colhead" align="center" width="10%">Snatched</td>
	<td class="colhead" align="center" width="5%"><input type="checkbox" checkall="group" onclick="javascript: return select_deselectAll (\'reseed\', this, \'group\')"></td>
</tr>
';
  $query = sql_query ('' . 'SELECT t.id, t.name, t.seeders, t.leechers, t.times_completed, t.added, t.owner, u.username, u.class FROM torrents t LEFT JOIN users u ON (t.owner=u.id)  WHERE t.seeders = 0 ORDER by t.added DESC ' . $limit);
  if (isset ($postedtorrents))
  {
    $postedtorrents = @explode (',', $postedtorrents);
  }

  if (0 < mysql_num_rows ($query))
  {
    while ($torrent = mysql_fetch_assoc ($query))
    {
      echo '
		<tr>
			<td align="left"><a href="' . $BASEURL . '/details.php?id=' . $torrent['id'] . '"><b>' . $torrent['name'] . '</b></a> [<a href="' . $BASEURL . '/edit.php?id=' . $torrent['id'] . '"><font color=green><b>edit</b></font></a>] [<a href="' . $BASEURL . '/page.php?type=fastdelete&id=' . $torrent['id'] . '"><font color=red><b>delete</b></font></a>]' . (@in_array ($torrent['id'], $postedtorrents, true) ? '<br><font color="red">Re-seed request sent!</font>' : '') . '</td>
			<td align="center">' . my_datee ($dateformat, $torrent['added']) . ' ' . my_datee ($timeformat, $torrent['added']) . '</td>
			<td align="center"><a href="' . $BASEURL . '/userdetails.php?id=' . $torrent['owner'] . '"><b>' . get_style ($torrent['class'],$torrent['username']) . '</b></a></td>
			<td align="center">' . number_format ($torrent['seeders']) . '</td>
			<td align="center">' . number_format ($torrent['leechers']) . '</td>
			<td align="center"><a href="' . $BASEURL . '/viewsnatches.php?id=' . $torrent['id'] . '">' . number_format ($torrent['times_completed']) . '</a></td>
			<td align="center"><input type="checkbox" checkme="group" name="torrents[]" value="' . $torrent['id'] . '"></td>
		</tr>
		';
		unset($_t,$ns);
    }

    echo '<tr><td colspan="7" align="right"><input type="submit" value="Request Re-seed for selected torrents"></td></tr>';
  }
  else
  {
    echo '<tr><td colspan="7">There is no weak torrent found!</td></tr>';
  }

  echo '</form>
' . $pagerbottom;
  _form_header_close_ ();
  end_frame();
  stdfoot ();
?>