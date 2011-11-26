<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

/**
 * format
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class format {
  /**
   * format::htmlspecialchars2()
   *
   * @param mixed $s
   * @return
   */
	public function htmlspecialchars2( $s ) 
{ 
    static $patterns, $replaces; 
     
    if( !$patterns ){ 

        $patterns = array( '#&lt;#', '#&gt;#', '#&amp;#', '#&quot;#' );
        $replaces = array( '<', '>', '&', '"' ); 
    } 

    return preg_replace( $patterns, $replaces, $s ); 
}

  /**
   * format::php()
   *
   * @param mixed $test
   * @return
   */
	public function php( $test )
{
    $bTags = true;
    if (   ( strpos( $test, "<?" ) === false )
        && ( strpos( $test, "?>" ) === false ) ) {
        $test = "<?php".$test."?>";
        $bTags = false;
    }
    ob_start();
    highlight_string($test);
    $sRetVal = ob_get_contents();
    ob_end_clean();

    if ( $bTags == false ) {
        $sRetVal = str_replace( "&lt;?php", "", $sRetVal );
        $sRetVal = str_replace( "?&gt;", "", $sRetVal );
    }

    $sRetVal = str_replace( "\n", "", $sRetVal );
    $sRetVal = str_replace( "<br />", "", $sRetVal );

    return $sRetVal;
}

  /**
   * format::highlight_html()
   *
   * @param mixed $code
   * @return
   */
		public function highlight_html($code) { //HTML highlighting. Standard colors, or editable via Admin CP? Going standard at the moment 
     
        //coloring is a steady horse 
         
        // fikser highlight p? vanlige tagger 
        $code = preg_replace("#&lt;(.+?)&gt;#is", "<span style=\"color:#000099;\">&lt;\\1&gt;</span>", $code);  
         
        // <a>-taggen 
        $code = preg_replace("#&lt;a(.+?)&gt;(.+?)&lt;/a&gt;#is", "<span style=\"color:#006600;\">&lt;a\\1&gt;</span>\\2<span style=\"color:#006600;\">&lt;/a&gt;</span>\n\r", $code);  
         
        // <img>-taggen 
        $code = preg_replace("#&lt;img(.+?)&gt;#is", "<span style=\"color:#990099;\">&lt;img\\1&gt;</span>", $code); 
         
        // <input>-taggen 
        $code = preg_replace("#&lt;input(.+?)&gt;#is", "<span style=\"color:#FF9900;\">&lt;input\\1&gt;</span>", $code); 
         
        // <style> 
        $code = preg_replace("#&lt;style&gt;(.+?)&lt;/style&gt;#is", "<span style=\"color:#990099;\">&lt;style&gt;</span>\\1<span style=\"color:#990099\">&lt;/style&gt;</span>", $code); 
         
        // <!-- og --> 
        $code = preg_replace("#&lt;!--(.+?)--&gt;#is", "<span style=\"color:#999999;\">&lt;!--\\1--&gt;</span>", $code); 
         
        // <script> med lukking 
        $code = preg_replace("#&lt;script(.+?)&gt;(.+?)&lt;/script&gt;#is", "<span style=\"color:#990000;\">&lt;script\\1&gt;</span>\\2<span style=\"color:#990000;\">&lt;/script&gt</span>", $code); 
         
        // <script> uten lukking 
        $code = preg_replace("#&lt;script(.+?)&gt;#is", "<span style=\"color:#990000;\">&lt;script\\1&gt;</span>", $code); 
         
        // <form> 
        $code = preg_replace("#&lt;form(.+?)&gt;#is", "<span style=\"color:#FF9900;\">&lt;form\\1&gt;</span>", $code); 
         
        // </form> 
        $code = preg_replace("#&lt;/form(.+?)&gt;#is", "<span style=\"color:#FF9900;\">&lt;/form\\1&gt;</span>", $code); 
         
        // atributter p? vanlige tagger 
        function attr($match) { 
            return htmlspecialchars("<" . $match[1] . " ") 
            . preg_replace("#([a-z\-]+)=(&quot;.*?&quot;)($| |\n)#", "\\1=<span style=\"color:#0000FF;\">\\2</span>\\3", $match[2]) 
            . htmlspecialchars(">"); 
        } 
         
        $input = preg_replace_callback("#&lt;([a-z0-9]+) (([a-z\-]+=&quot;(.*?)&quot; *)*.*?)&gt;#is", 'attr', $input);  
         
        //indeed. 
        return $code; 
}

  /**
   * format::highlight_sql()
   *
   * @param mixed $string
   * @return
   */
		public function highlight_sql($string)
{
        $aKeywords = array(); 

        // SQL syntax
        $aKeywords[] = array('and', true); // keyword name (any string [a-zA-Z0-9_], or any character), keyword to next line (true or false, default: false), css class (default: 'keyword')
        $aKeywords[] = array('asc', false);
        $aKeywords[] = array('binary', false);
        $aKeywords[] = array('by', false);
        $aKeywords[] = array('delete', true);
        $aKeywords[] = array('desc', false);
        $aKeywords[] = array('having', true);
        $aKeywords[] = array('group', true);
        $aKeywords[] = array('insert', true);
        $aKeywords[] = array('in', true);
        $aKeywords[] = array('into', false);
        $aKeywords[] = array('left', false);
        $aKeywords[] = array('like', false);
        $aKeywords[] = array('limit', true);
        $aKeywords[] = array('order', true);
        $aKeywords[] = array('or', true);
        $aKeywords[] = array('right', false);
        $aKeywords[] = array('select', true);
        $aKeywords[] = array('table', true);
        $aKeywords[] = array('alter', true);
        $aKeywords[] = array('set', true);
        $aKeywords[] = array('values', true);
        $aKeywords[] = array('where', true);
        $aKeywords[] = array('xor', true);

        // Operators
        $aKeywords[] = array('+', false, 'operator');
        $aKeywords[] = array('-', false, 'operator');
        $aKeywords[] = array('*', false, 'operator');
        $aKeywords[] = array('/', false, 'operator');
        $aKeywords[] = array('%', false, 'operator');
        $aKeywords[] = array('.', false, 'operator');
        $aKeywords[] = array(',', false, 'operator');

        $aKeywords[] = array('true', false, 'red');
        $aKeywords[] = array('false', false, 'red');
        $aKeywords[] = array('null', false, 'red');
        $aKeywords[] = array('unkown', false, 'red');
        $aKeywords[] = array(';', false, 'red');
        
        $aKeywords[] = array('distinct', true, 'green');
        $aKeywords[] = array('as', false, 'green');
        $aKeywords[] = array('from', false, 'green');
        $aKeywords[] = array('join', false, 'green');
        
        $aKeywords[] = array('<', false, 'orange');
        $aKeywords[] = array('>', false, 'orange');
        $aKeywords[] = array('=', false, 'orange');
        $aKeywords[] = array('on', false, 'orange');
        $aKeywords[] = array('group', false, 'orange');

        // Split query into pieces (quoted values, ticked values, string and/or numeric values, and all others).
        $expr = '/(\'((\\\\.)|[^\\\\\\\'])*\')|(\`((\\\\.)|[^\\\\\\\`])*\`)|([a-z0-9_]+)|([\s\n]+)|(.)/i';
        preg_match_all($expr, $string, $matches);

        // Use a buffer to build up lines.
        $buffer = '';
        
        // Keep track of brackets to indent/outdent
        $iTab = 0;

        for($i = 0; $i < sizeof($matches[0]); $i++)
        {
            if(strcasecmp($match = $matches[0][$i], "") !== 0)
            {
                if(in_array($match, array("(", ")"))) // Bracket found
                {
                    $buffer = trim($buffer);

                    if(strlen($buffer) > 0)
                    {
                        $result .= $buffer . '<br>';
                    }

                    $buffer = '';

                    if(strcasecmp($match, ")") === 0)
                    {
                        $iTab--;

                        if($iTab < 0)
                        {
                            $iTab = 0;
                        }

                        $result .= str_repeat('&nbsp;', 4 * $iTab) . '<span class="bracket">' . htmlentities($match) . '</span><br>';
                    }
                    else // if(strcasecmp($match, "(") === 0)
                    {
                        $result .= str_repeat('&nbsp;', 4 * $iTab) . '<span class="bracket">' . htmlentities($match) . '</span><br>';
                        $iTab++;
                    }
                }
                elseif(preg_match('/^[\s\n]+$/', $match)) // Space character(s)
                {
                    if(strlen($buffer) === 0)
                    {
                        // Ignore space character(s)!
                    }
                    else
                    {
                        $buffer .= ' ';
                    }
                }
                else
                {
                    $aKeyword = false;

                    for($j = 0; $j < sizeof($aKeywords); $j++)
                    {
                        if(strcasecmp($match, $aKeywords[$j][0]) === 0)
                        {
                            $aKeyword = $aKeywords[$j];
                            break;
                        }
                    }

                    if($aKeyword) // Keyword found
                    {
                        if(isset($aKeyword[1]) && $aKeyword[1] === true) // Keyword to next line
                        {
                            $buffer = trim($buffer);

                            if(strlen($buffer) > 0)
                            {
                                $result .= $buffer . '<br>';
                            }

                            $buffer = ''; 
                        }

                        if(strlen($buffer) === 0) // Indent
                        {
                            $buffer .= str_repeat('&nbsp;', 4 * $iTab); 
                        }

                        $buffer .= '<span class="' . (isset($aKeyword[2]) ? $aKeyword[2] : 'keyword') . '">' . htmlentities(strtoupper($match)) . '</span>';
                    }
                    else
                    {
                        if(strlen($buffer) === 0) // Indent
                        {
                            $buffer = str_repeat('&nbsp;', 4 * $iTab);
                        }

                        if((strcasecmp(substr($match, 0, 1), "'") === 0) || is_numeric($match)) // Quoted value or number
                        {
                            $buffer .= '<span class="red">' . htmlentities($match) . '</span>';
                        }
                        elseif((strcasecmp(substr($match, 0, 1), "`") === 0) || preg_match('/[a-z0-9_]+/i', $match)) // Ticked value or unquoted string (table/column name?!)
                        {
                            $buffer .= '<span class="ticked">' . htmlentities($match) . '</span>';
                        }
                        else // All other chars
                        {
                            $buffer .= htmlentities($match);
                        }
                    }
                }
            }
        }

        $buffer = trim($buffer);

        if(strlen($buffer) > 0)
        {
            $result .= $buffer;
        }

        return '<div class="codetop">SQL</div><code class="sql">' . $result . '</code>';
}
}