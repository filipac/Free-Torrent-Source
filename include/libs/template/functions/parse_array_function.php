<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * parse_array()
 *
 * @param mixed $element
 * @return
 */
function parse_array($element) {
    global $logdata, $logindex;
    foreach ($element as $header => $value) {
        if (is_array($value)) {
            parse_array($value);
            $logindex++;
        } else {
            $logdata[$logindex][$header] = $value;
        }
    }
}
?>