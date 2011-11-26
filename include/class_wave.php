<?php   
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * createWaveFile()
 *
 * @param mixed $word
 * @return
 */
function createWaveFile( $word )
{
    global $settings, $user_info ;

    if ( file_exists('include/sound/a.english.wav') )
        $sound_language = 'english' ;

    // Guess not...
    else
        return false ;

    // File names are in lower case so lets make sure that we are only using a lower case string
    $word = strtolower( $word ) ;

    // Loop through all letters of the word $word.
    $sound_word = '' ;
    for ( $i = 0; $i < strlen($word); $i++ )
    {
        $sound_letter = implode( '', file('include/sound/' . $word{$i} . '.' . $sound_language .
            '.wav') ) ;
        if ( strpos($sound_letter, 'data') === false )
            return false ;
        $sound_word .= substr( $sound_letter, strpos($sound_letter, 'data') + 8 ) .
            str_repeat( chr(0x80), rand(700, 710) * 8 ) ;
    }

    // The .wav header.
    $sound_header = array( 0x10, 0x00, 0x00, 0x00, 0x01, 0x00, 0x01, 0x00, 0x40,
        0x1F, 0x00, 0x00, 0x40, 0x1F, 0x00, 0x00, 0x01, 0x00, 0x08, 0x00, 0x64, 0x61,
        0x74, 0x61, ) ;


    $data_size = strlen( $sound_word ) ;
    $file_size = $data_size + 0x24 ;

    // Add a little randomness.
    for ( $i = 0; $i < $data_size; $i += rand(1, 10) )
        $sound_word{$i} = chr( ord($sound_word{$i}) + rand(-1, 1) ) ;

    // Output the wav.
    header( 'Content-type: audio/x-wav' ) ;
    header( 'Content-Length: ' . $file_size ) ;
    echo 'RIFF', chr( $file_size & 0xFF ), chr( ($file_size & 0xFF00) >> 8 ), chr( ($file_size &
        0xFF0000) >> 16 ), chr( ($file_size & 0xFF000000) >> 24 ), 'WAVEfmt ' ;
    foreach ( $sound_header as $char )
        echo chr( $char ) ;
    echo chr( $data_size & 0xFF ), chr( ($data_size & 0xFF00) >> 8 ), chr( ($data_size &
        0xFF0000) >> 16 ), chr( ($data_size & 0xFF000000) >> 24 ), $sound_word ;

    // Noting more to add.
    die() ;
}
?>