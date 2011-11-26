<?php
function add( $name, $value, $serialize = false )
{
	if($serialize)
	$value = serialize($value);
    $row = @mysql_query( "SELECT option_value FROM options WHERE option_name = '$name' LIMIT 1" ) ;
    if ( @mysql_num_rows($row) == 0 ) @mysql_query( "INSERT INTO options (option_name, option_value) VALUES ('$name', '$value')" ) ;
    return ;
}
function get( $name, $serialize = false )
{
    $row = mysql_query( "SELECT option_value FROM options WHERE option_name = '$name' LIMIT 1" ) or die(mysql_error()) ;
    if ( ! $row ) return false ;
    $content = @mysql_fetch_assoc( $row ) ;
    if($serialize)
    $c = unserialize($content['option_value']);
    else
    $c = $content['option_value'];
    return $c ;
}
function update( $name, $value, $serialize = false )
{
	if(empty($value))
	return del($name);
	if($serialize)
	$value = serialize($value);
    $row = @mysql_query( "SELECT option_value FROM options WHERE option_name = '$name' LIMIT 1" ) ;
    if(@mysql_num_rows($row) == '0')  @add( $name, $value ) ;
    elseif ( @mysql_num_rows($row) > '0' ) $row = @mysql_query( "UPDATE options SET option_value = '$value' WHERE option_name = '$name'" ) ;
}
function del( $name )
{
    $row = @mysql_query( "DELETE FROM options WHERE option_name = '$name'" ) ;
    if ( ! $row ) return false ;
}
function dbv($name) {
	global $rootpath;
$file = $rootpath.'fts-contents/cache/value_mysql_'.$name.'.tmp';
$expire = 86400; // 1 day
if (file_exists($file) &&
    filemtime($file) > (time() - $expire) && file_get_contents($file) != '') {
    $records = unserialize(file_get_contents($file));
} else {
    $query = "SELECT option_value FROM options WHERE option_name = '$name' LIMIT 1";
    $result = mysql_query($query)
        or die (mysql_error());
    while ($record = mysql_fetch_array($result) ) {
        $records[] = $record;
    }
    $OUTPUT = serialize($records);
    $fp = fopen($file,"w");
    fputs($fp, $OUTPUT);
    fclose($fp);
} // end else
foreach($records as $a=>$b) {
	foreach ($b as $option => $value)
	return $value;
}
}
?>
