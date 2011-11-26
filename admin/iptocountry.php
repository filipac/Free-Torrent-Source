<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
loggedinorreturn();
parked();
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
    echo '</table></tbody></td></tr></table>';
  }
  function i2c_realip ()
  {
    $ip = FALSE;
    if (!empty ($_SERVER['HTTP_CLIENT_IP']))
    {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty ($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
      if ($ip)
      {
        array_unshift ($ips, $ip);
        $ip = FALSE;
      }

      $i = 0;
      while ($i < count ($ips))
      {
        if (!preg_match ('/^(?:10|172\\.(?:1[6-9]|2\\d|3[01])|192\\.168)\\./', $ips[$i]))
        {
          if (version_compare (phpversion (), '5.0.0', '>='))
          {
            if (ip2long ($ips[$i]) != false)
            {
              $ip = $ips[$i];
              break;
            }
          }

          if (ip2long ($ips[$i]) != 0 - 1)
          {
            $ip = $ips[$i];
            break;
          }
        }

        ++$i;
      }
    }

    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
  }

  define ('ITC_VERSION', '0.2 by xam');
  $do = (isset ($_POST['do']) ? htmlspecialchars ($_POST['do']) : (isset ($_GET['do']) ? htmlspecialchars ($_GET['do']) : 1));
  stdhead ('Ip to Country');
  $errormessage = '';
  if ($do == 2)
  {
    $ip = ((isset ($_POST['ip_address']) AND !empty ($_POST['ip_address'])) ? $_POST['ip_address'] : ((isset ($_GET['ip_address']) AND !empty ($_GET['ip_address'])) ? $_GET['ip_address'] : i2c_realip ()));
    $post_data = array ();
    $post_data['ip_address'] = $ip;
    if ((function_exists ('curl_init') AND $ch = curl_init ()))
    {
      curl_setopt ($ch, CURLOPT_URL, 'http://ip-to-country.webhosting.info/node/view/36');
      curl_setopt ($ch, CURLOPT_POST, 1);
      curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
      $postResult = curl_exec ($ch);
      if (curl_errno ($ch))
      {
        exit (curl_error ($ch));
      }

      curl_close ($ch);
    }
    else
    {
      $errormessage = 'CURL Library required to run this tool.';
    }

    _form_header_open_ ('Search Result');
    if (empty ($errormessage))
    {
      $regex = '' . '#<b>' . $ip . '</b>(.*).<br><br><img src=(.*)>#U';
      preg_match_all ($regex, $postResult, $result, PREG_SET_ORDER);
      echo '<tr><td>IP Address <b>' . htmlspecialchars_uni ($ip) . '</b>' . $result[0][1] . '.<!--<br><br><img src="http://ip-to-country.webhosting.info/' . $result[0][2] . '">!--></td></tr>';
    }
    else
    {
      echo '<tr><td>' . $errormessage . '</td></tr>';
    }

    _form_header_close_ ();
    echo '<br>';
  }

  $externalpreview = '<div id="loading-layer" name="loading-layer" style="position: absolute; display:none; left:300px; top:110px;width:200px;height:60px;background:#FFF;padding:10px;text-align:center;border:1px solid #000"><div style="font-weight:bold" id="loading-layer-text" class="small">Searching...<br />Please wait!</div><img src="pic/loadAnim.gif" border="0" /></div>';
  _form_header_open_ ('Ip to Country');
  echo '
<tr><td>
<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '">
<input type="hidden" name="act" value="iptocountry">
<input type="hidden" name="do" value="2">
IP Address: <input name="ip_address" type="text" value="' . htmlspecialchars_uni ($ip) . '"> 
<input value="Find Country" name="submit" type="submit" onclick="$(\'#loading-layer\').show();"> 
' . $externalpreview . '
</td></tr></form>
';
  _form_header_close_ ();
  stdfoot ();
?>