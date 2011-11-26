<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */   
/**
 * collapses()
 *
 * @param mixed $id
 * @param mixed $title
 * @param string $width
 * @param integer $manual
 * @param string $trclass
 * @param string $tdclass
 * @param string $fontcolor
 * @param string $cellpadding
 * @param integer $noborder
 * @return
 */
function collapses( $id, $title, $width = '100', $manual = 0, $trclass = '',
$tdclass = 'class=collapse', $fontcolor = '#000000', $cellpadding = '5', $noborder =
0 )
{
    global $BASEURL ;
    global $CURUSER ;
    $user = ( $CURUSER ? $CURUSER['username'] : 'guest' ) ;
    if ( $manual )
        $title = str_replace( '{icon}', "<span id=\"ftscol\" class=\"iconspan\" style=\"float:right;\"><a href=\"javascript:;\" onclick=\"javascript:toggleItem('$id-$user')\" class=eg-bar style=\"border:none\"><img src=\"$BASEURL/pic/minus.gif\" style=\"border:none\" id='colicon'></a></span>",
            $title ) ;
?>
 <table border="0" width="100%">
 <tr <?= $trclass ?>>
 <td align="center" class="<?= $tdclass ?>" style="padding:<?= $cellpadding ?>px;">
 <?php if ( ! $manual )
    { ?><span id="ftscol" class="iconspan" style="float:right;"><a href="javascript:;" onclick="javascript:toggleItem('<?= $id .
'-' . $user ; ?>')" class=eg-bar style="border:none"><img src="<?= $BASEURL ?>/pic/minus.gif" style="border:none" id="<?= $id .
'-' . $user ; ?>-pic"></a></span><?php } ?><center>
				<span style="color:<?= $fontcolor ?>;"><?= $title ; ?></span></center>
 </td>
 </tr>
 <tbody id="<?= $id ; ?>-<?= $user ; ?>">
 
 <tr style="border:none">
<td <?= $noborder ? 'style="border:none"' : '' ; ?>>						

			

			
			
<?php }
/**
 * collapsee()
 *
 * @return
 */
function collapsee()
{
    echo "

 </td>
 </tr>
 </tbody>
 </table>" ;
}

/**
 * _start_collapse()
 *
 * @param mixed $id
 * @param mixed $title
 * @param string $width
 * @param integer $manual
 * @param string $trclass
 * @param string $tdclass
 * @param string $fontcolor
 * @param string $cellpadding
 * @param integer $noborder
 * @return void
 */
function _start_collapse( $id, $title, $width = '100', $manual = 0, $trclass = '',
$tdclass = 'class=collapse', $fontcolor = '#000000', $cellpadding = '5', $noborder =
0 )
{
    global $BASEURL ;
    global $CURUSER ;
    $user = ( $CURUSER ? $CURUSER['username'] : 'guest' ) ;
    if ( $manual )
        $title = str_replace( '{icon}', "<span id=\"ftscol\" class=\"iconspan\" style=\"float:right;\"><a href=\"javascript:;\" onclick=\"javascript:toggleItem('$id-$user')\" class=eg-bar style=\"border:none\"><img src=\"$BASEURL/pic/minus.gif\" style=\"border:none\" id='colicon'></a></span>",
            $title ) ;
?>
 <table border="0" width="100%">
 <tr <?= $trclass ?>>
 <td align="center" class="<?= $tdclass ?>" style="padding:<?= $cellpadding ?>px;">
 <?php if ( ! $manual )
    { ?><span id="ftscol" class="iconspan" style="float:right;"><a href="javascript:;" onclick="javascript:toggleItem('<?= $id .
'-' . $user ; ?>')" class=eg-bar style="border:none"><img src="<?= $BASEURL ?>/pic/minus.gif" style="border:none" id="<?= $id .
'-' . $user ; ?>-pic"></a></span><? } ?><center>
				<span style="color:<?= $fontcolor ?>;"><?= $title ; ?></span></center>
 </td>
 </tr>
 <tbody id="<?= $id ; ?>-<?= $user ; ?>">
 
 <tr style="border:none">
<td <?= $noborder ? 'style="border:none"' : '' ; ?>>						

			

			
			
<?php }

/**
 * _end_collapse()
 *
 * @return void
 */
function _end_collapse()
{
    echo "

 </td>
 </tr>
 </tbody>
 </table>" ;
}

?>