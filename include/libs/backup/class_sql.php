<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * db_backup
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class db_backup
{

    var $filename ;
    /**
     * db_backup::db_backup()
     *
     * @param mixed $filename
     * @return
     */
    function db_backup( $filename )
    {
        $this->filename = $filename ;
    }
    /**
     * db_backup::Backup()
     *
     * @param mixed $host
     * @param mixed $port
     * @param mixed $user
     * @param mixed $pwd
     * @param mixed $dbname
     * @return
     */
    function Backup( $host, $port, $user, $pwd, $dbname )
    {

        $i = 0 ;

        $crlf = "\r\n" ;

        $schema_insert = "" ;

        $host = ( ! $host ) ? "localhost" : $host ;

        $port = ( ! $port ) ? "3306" : $port ;

        $user = ( ! $user ) ? "root" : $user ;

        $dbname = ( ! $dbname ) ? die( "No Database specified" ) : $dbname ;

        $password = ( $pwd == "" ) ? "NO" : "YES" ;

        @mysql_pconnect( $host . ":" . $port, $user, $pwd ) or die( "Can't connect to " .
            $host . ":" . $port . " (using password: " . $password . ") " ) ;

        @mysql_select_db( $dbname ) or die( "Unable to select database" ) ;

        $tables = mysql_list_tables( $dbname ) ;

        $num_tables = @mysql_numrows( $tables ) ;

        $Year = date( "Y" ) ;

        $Month = date( "m" ) ;

        $Day = date( "d" ) ;

        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ;

        $country = StrToUpper( $lang ) ;

        $loc = array( "" . $lang . "_" . $country . "@euro", "" . $lang . "_" . $country .
            "", "" . $lang . "", "" . $country . "" ) ;

        foreach ( $loc as $GetRightLocale )
        {

            if ( setlocale(LC_TIME, $GetRightLocale) )
            { //	Found a Valid SetLocale

                break ;

            }

        }

        ###	Search and Replace \n \r in the INSERT Statement	###

        ###	for each records									###

        $search = array( "\x00", "\x0a", "\x0d", "\x1a" ) ; //\x08\\x09, not required

        $replace = array( '\0', '\n', '\r', '\Z' ) ;

        $DataCreation = strftime( "%d %B %Y", mktime(0, 0, 0, $Month, $Day, $Year) ) ;

        $Time = date( "H:i:s" ) ;

        ###	Save to Server	###

        if ( $this->dbHeaders == 0 )
        {

            $content = "" ;

            $content .= $crlf ;

            $content .= "# --------------------------------------------------------" . $crlf .
                "" ;

            $content .= "#" . $crlf . "" ;

            $content .= "# Dump Script for '" . $dbname . "' " . $crlf . "" ;

            $content .= "#" . $crlf . "" ;

            $content .= "# Host: " . $host . "" . $crlf . "" ;

            $content .= "#" . $crlf . "" ;

            $content .= "# created on " . $DataCreation . " at " . $Time . "" . $crlf . "" ;

            $content .= "#" . $crlf . "" ;

            $content .= "# Created by Fts Dump Exporter(using the FTS staff tool)" . $crlf ;

            $content .= "# --------------------------------------------------------" . $crlf .
                "" ;

            ##	Open file for writing	##
            #echo $this->filename.' filename <br>';
            global $rootpath ;
            if ( file_exists($rootpath . '/include/backups_sql/' . $this->filename) )
                die( "Flood Error! You already done a backup this minute." ) ;
            $fp = fopen( $rootpath . '/include/backups_sql/' . $this->filename, 'w+' ) ;

            while ( $i < $num_tables )
            {

                $table = mysql_tablename( $tables, $i ) ;

                $content .= $crlf ;

                $content .= "# --------------------------------------------------------" . $crlf .
                    "" ;

                $content .= "#" . $crlf . "" ;

                $content .= "# Table Structure for '" . $table . "' " . $crlf . "" ;

                $content .= "#" . $crlf . "" ;

                $content .= $crlf ;

                #################################################

                #	Build Table Structure			#

                #################################################

                /*	Table Structure	*/

                $schema_create = "" ;

                $schema_create .= "DROP TABLE IF EXISTS `" . $table . "`;" . $crlf ;

                $schema_create .= "CREATE TABLE `" . $table . "` (" . $crlf ;

                $result = mysql_db_query( $dbname, "SHOW FIELDS FROM `" . $table . "`" ) or die( "error select database" ) ;

                while ( $row = mysql_fetch_array($result) )
                {

                    $schema_create .= "   `$row[Field]` $row[Type]" ;

                    $schema_create .= ( $row["Null"] != "YES" ) ? " NOT NULL" : "" ;

                    $schema_create .= ( isset($row["Default"]) && (! empty($row["Default"]) || $row["Default"] ==
                        "0") ) ? " default '$row[Default]'" : "" ;

                    $schema_create .= ( $row["Extra"] != "" ) ? " " . $row["Extra"] : "" ;

                    $schema_create .= "," . $crlf ;

                }

                $schema_create = ereg_replace( "," . $crlf . "$", "", $schema_create ) ;

                /*	Table Keys	*/

                $index = array() ;

                $result = mysql_db_query( $dbname, "SHOW KEYS FROM `" . $table . "`" ) or die() ;

                while ( $row = mysql_fetch_array($result) )
                {

                    if ( $row['Key_name'] == "PRIMARY" )
                        $kname = "PRIMARY KEY" ;

                    elseif ( $row['Non_unique'] == 0 )
                        $kname = "UNIQUE `" . $row['Key_name'] . "`" ;

                    else
                        $kname = "KEY `" . $row['Key_name'] . "`" ;

                    if ( ! isset($index[$kname]) )
                        $index[$kname] = array() ;

                    $index[$kname][] = "`" . $row['Column_name'] . "`" . ( isset($row['Sub_part']) ?
                        "(" . $row['Sub_part'] . ")" : "" ) ;

                }

                foreach ( $index as $x => $columns )
                {

                    $schema_create .= "," . $crlf ;

                    $schema_create .= "   " . $x . " (" . implode( $columns, ", " ) . ")" ;

                }

                $schema_create .= $crlf . ") " ;


                #	DataBase Type								#

                $result = mysql_db_query( $dbname, "SHOW TABLE STATUS FROM " . $dbname .
                    " LIKE '" . $table . "'" ) or die() ;

                $row = mysql_fetch_array( $result ) ;

                $schema_create .= "Type=" . $row['Type'] ;

                $schema_create .= ( ! empty($row['Auto_increment']) ? " AUTO_INCREMENT=" . $row['Auto_increment'] :
                    "" ) ;

                $schema_create .= ";" . $crlf . $crlf ;

                $content .= $schema_create ;

                $schema_create = "" ;

                #################################################

                #	Build Table Content (INSERT)		#

                #################################################

                $content .= "#" . $crlf . "" ;

                $content .= "# Dumping data for table '" . $table . "'" . $crlf . "" ;

                $content .= "#$crlf" ;

                $content .= $crlf ;

                $result = mysql_db_query( $dbname, "SELECT * FROM `$table`" ) or die() ;

                $a = 0 ;

                while ( $row = mysql_fetch_row($result) )
                {

                    $table_list = "(" ;

                    for ( $j = 0; $j < mysql_num_fields($result); $j++ )
                        $table_list .= "`" . mysql_field_name( $result, $j ) . "`, " ;

                    $table_list = substr( $table_list, 0, -2 ) ;

                    $table_list .= ")" ;

                    if ( isset($GLOBALS["showcolumns"]) )
                        $schema_insert .= "INSERT INTO `" . $table . "` " . $table_list . " VALUES (" ;

                    else
                        $schema_insert .= "INSERT INTO `" . $table . "` VALUES (" ;

                    for ( $j = 0; $j < mysql_num_fields($result); $j++ )
                    {

                        if ( ! isset($row[$j]) )
                            $schema_insert .= " NULL," ;

                        elseif ( $row[$j] != "" )
                            $schema_insert .= " '" . Str_Replace( $search, $replace, addslashes($row[$j]) ) .
                                "'," ;

                        else
                            $schema_insert .= " ''," ;

                    }

                    $schema_insert = ereg_replace( ",$", "", $schema_insert ) ;

                    $schema_insert .= ");" . $crlf ;

                    //$handler(trim($schema_insert));

                    $a++ ;

                }

                $content .= $schema_insert . "" . $crlf . "" ;

                $schema_insert = "" ;

                $i++ ;

            }

            ##	Write to file	##

            fwrite( $fp, $content ) ;

            fclose( $fp ) ;
            $this->download( $this->filename ) ;
        }

    }

    /**
     * db_backup::download()
     *
     * @param mixed $filename
     * @return
     */
    function download( $filename )
    {
        header( "Content-disposition: filename=" . $filename . "" ) ;

        header( "Content-type: application/octetstream" ) ;

        header( "Pragma: no-cache" ) ;

        header( "Expires: 0" ) ;

    }
}

?>