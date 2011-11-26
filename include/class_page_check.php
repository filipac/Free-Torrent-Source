<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
// The following class will allow specific sender/receiver scripts to be protected.
// Usage:
// $var = new page_verify(); //to initiate.
// $var->create('task_name'); // to create session - task name should reflect task in question.
// $var->check('task_name'); // to verify session - will die() if wrong. This will also reset the
//              session so that repeated access of this page cannot happen without the calling script.
//
// You use the create function with the sending script, and the check function with the
// receiving script...

/**
 * page_verify
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class page_verify
{

  /**
   * page_verify::page_verify()
   *
   * @return
   */
    function page_verify()
    {
        if ( session_id() == '' )
            session_start() ;
    }

  /**
   * page_verify::create()
   *
   * @param string $task_name
   * @return
   */
    function create( $task_name = 'Default' )
    {
        global $CURUSER ;
        $_SESSION['Task_Time'] = mktime() ;
        $_SESSION['Task'] = md5( 'User_ID:' . $CURUSER['id'] . '::TName-' . $task_name .
            '::' . $_SESSION['Task_Time'] ) ;
        $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ;
    }

  /**
   * page_verify::check()
   *
   * @param string $task_name
   * @return
   */
    function check( $task_name = 'Default' )
    {
        global $CURUSER ;
        if ( $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'] )
            die() ;
        if ( $_SESSION['Task'] != md5('User_ID:' . $CURUSER['id'] . '::TName-' . $task_name .
            '::' . $_SESSION['Task_Time']) )
            die( 'Hacking attempt!' ) ;
        $this->create() ;
    }
}
?>