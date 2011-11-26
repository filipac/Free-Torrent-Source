<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * random()
 *
 * @url http://freetosu.berlios.de/wiki/function/random
 * @param integer $length
 * @return
 */
function random( $length = 20 )
{
    $set = array("A","B",'C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    $str ;
    for ( $i = 1; $i <= $length; $i++ )
    {
        $ch = rand( 0, count($set) - 1 ) ;
        $str .= $set[$ch] ;
    }
    return apply_filters("random",$str) ;
}
/**
 * create_captcha()
 *
 * @url http://freetosu.berlios.de/wiki/function/create_captcha
 * @return
 */
function create_captcha()
{
    $randomstr = random( 5 ) ;
    $imagehash = md5( $randomstr ) ;
    mysql_query( "INSERT INTO regimages SET imagehash = " . sqlesc($imagehash) .
        ", imagestring = " . sqlesc($randomstr) . ", dateline = " . sqlesc(time()) ) or
        sqlerr( __file__, __line__ ) ;
    return $imagehash ;
}

/**
 * my_strlen()
 *
 * @url http://freetosu.berlios.de/wiki/function/my_strlen
 * @param mixed $string
 * @return
 */
function my_strlen( $string )
{
    $string = preg_replace( "#&\#(0-9]+);#", "-", $string ) ;
    if ( function_exists("mb_strlen") )
    {
        $string_length = mb_strlen( $string ) ;
    }
    else
    {
        $string_length = strlen( $string ) ;
    }

    return $string_length ;
}


/**
 * my_substr()
 *
 * @url http://freetosu.berlios.de/wiki/function/my_substr
 * @param mixed $string
 * @param mixed $start
 * @param string $length
 * @return
 */
function my_substr( $string, $start, $length = "" )
{
    if ( function_exists("mb_substr") )
    {
        if ( $length != "" )
        {
            $cut_string = mb_substr( $string, $start, $length ) ;
        }
        else
        {
            $cut_string = mb_substr( $string, $start ) ;
        }
    }
    else
    {
        if ( $length != "" )
        {
            $cut_string = substr( $string, $start, $length ) ;
        }
        else
        {
            $cut_string = substr( $string, $start ) ;
        }
    }

    return $cut_string ;
}

?>