<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * @url http://freetosu.berlios.de
 * */  
/**
 * benc()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $obj
 * @return
 */
function benc($obj)
{
    if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
        return;
    $c = $obj["value"];
    switch ($obj["type"])
    {
        case "string":
            return benc_str($c);
        case "integer":
            return benc_int($c);
        case "list":
            return benc_list($c);
        case "dictionary":
            return benc_dict($c);
        default:
            return;
    }
}
/**
 * benc_str()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $s
 * @return
 */
function benc_str($s)
{
    return strlen($s) . ":$s";
}
/**
 * benc_int()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $i
 * @return
 */
function benc_int($i)
{
    return "i" . $i . "e";
}
/**
 * benc_list()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $a
 * @return
 */
function benc_list($a)
{
    $s = "l";
    foreach ($a as $e)
    {
        $s .= benc($e);
    }
    $s .= "e";
    return $s;
}
/**
 * benc_dict()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $d
 * @return
 */
function benc_dict($d)
{
    $s = "d";
    $keys = array_keys($d);
    sort($keys);
    foreach ($keys as $k)
    {
        $v = $d[$k];
        $s .= benc_str($k);
        $s .= benc($v);
    }
    $s .= "e";
    return $s;
}
/**
 * bdec_file()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $f
 * @param mixed $ms
 * @return
 */
function bdec_file($f, $ms)
{
    $fp = fopen($f, "rb");
    if (!$fp)
        return;
    $e = fread($fp, $ms);
    fclose($fp);
    return bdec($e);
}
/**
 * bdec()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $s
 * @return
 */
function bdec($s)
{
    if (preg_match('/^(\d+):/', $s, $m))
    {
        $l = $m[1];
        $pl = strlen($l) + 1;
        $v = substr($s, $pl, $l);
        $ss = substr($s, 0, $pl + $l);
        if (strlen($v) != $l)
            return;
        return array('type' => "string", 'value' => $v, 'strlen' => strlen($ss),
            'string' => $ss);
    }
    if (preg_match('/^i(\d+)e/', $s, $m))
    {
        $v = $m[1];
        $ss = "i" . $v . "e";
        if ($v === "-0")
            return;
        if ($v[0] == "0" && strlen($v) != 1)
            return;
        return array('type' => "integer", 'value' => $v, 'strlen' => strlen($ss),
            'string' => $ss);
    }
    switch ($s[0])
    {
        case "l":
            return bdec_list($s);
        case "d":
            return bdec_dict($s);
        default:
            return;
    }
}
/**
 * bdec_list()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $s
 * @return
 */
function bdec_list($s)
{
    if ($s[0] != "l")
        return;
    $sl = strlen($s);
    $i = 1;
    $v = array();
    $ss = "l";
    for (;; )
    {
        if ($i >= $sl)
            return;
        if ($s[$i] == "e")
            break;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret))
            return;
        $v[] = $ret;
        $i += $ret["strlen"];
        $ss .= $ret["string"];
    }
    $ss .= "e";
    return array('type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' =>
        $ss);
}
/**
 * bdec_dict()
 * 
 * @url http://freetosu.berlios.de/wiki/function/benc
 * @since 1.0.0
 * @param mixed $s
 * @return
 */
function bdec_dict($s)
{
    if ($s[0] != "d")
        return;
    $sl = strlen($s);
    $i = 1;
    $v = array();
    $ss = "d";
    for (;; )
    {
        if ($i >= $sl)
            return;
        if ($s[$i] == "e")
            break;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
            return;
        $k = $ret["value"];
        $i += $ret["strlen"];
        $ss .= $ret["string"];
        if ($i >= $sl)
            return;
        $ret = bdec(substr($s, $i));
        if (!isset($ret) || !is_array($ret))
            return;
        $v[$k] = $ret;
        $i += $ret["strlen"];
        $ss .= $ret["string"];
    }
    $ss .= "e";
    return array('type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss),
        'string' => $ss);
}
?>