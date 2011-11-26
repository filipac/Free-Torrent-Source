<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * Cache - Output & Object caching
 * @package Cache
 * @author Dragan Bosnjak
 * @copyright 2007 Dragan Bosnjak
 */
class Cache
{

	/**
	 * Cache Constructor
	 * @param string $path path to cache directory
	 * @param boolean $disable disable caching for current request
	 * @param boolean $disable_output disable outputing of cached data
	 */
	function Cache($path, $disable=false, $disable_output=false)
	{
		if(!is_dir($path))		trigger_error("Path is not directory!", E_USER_ERROR);
		if(!is_writable($path))	trigger_error("Path is not writable!", E_USER_ERROR);

		if(!in_array(substr($path,-1),array("\\","/"))) $path.="/";
		
		$this->path = $path;
		$this->disable = (bool)$disable;
		$this->disable_output = (bool)$disable_output;
		$this->stack = array();
		
		$this->output = null;
	}
	
	function _output($output)
	{
		$this->output = $output;
		if(!$this->disable_output) echo $output;
	}

	/**
	 * Get current cached file or returns false
	 * @access private
	 * @param string $file filename cache will be stores, used as cache id
	 * @param integer $time time of expiration in seconds
	 */
	function _load($file,$time)
	{
		$filename = $this->path.$file;
		
		if($this->disable) return false;
		if(!file_exists($filename)) return false;
		
		$f = fopen($filename, "r");
		$fstat = fstat($f);
		fclose($f);

		if(time()-$fstat['mtime']>$time) return false;
		
		return file_get_contents($filename);
		
	}

	/**
	 * Starts caching or returns cached data
	 * @access private
	 * @param string $file filename cache will be stores, used as cache id
	 * @param integer $time time of expiration in seconds
	 */
	function _start($file,$time)
	{

		$data = $this->_load($file,$time);
		if($data===false)
		{
			$this->stack[count($this->stack)] = $file;
			ob_start();
			return count($this->stack);
		}
		
		$data = $this->_unpack($data);
		
		$this->_output($data['__output__']);
		
		return $data;
	}

	/**
	 * Unpack data
	 * @access private
	 * @param string $data serialized data
	 * @return array
	 */
	function _unpack($data)
	{
		return unserialize($data);
	}

	/**
	 * Pack data to string
	 * @access private
	 * @param array $data data in array
	 * @return string
	 */
	function _pack($data)
	{
		return serialize($data);
	}
	

	/**
	 * Main function for caching
	 * @param string $file filename cache will be stores, used as cache id
	 * @param integer $time time of expiration in seconds
	 * @param array $data array containig references to objects that need to be cached
	 * @return boolean
	 */
	function save($file,$time,$data=array())
	{

		if($this->disable) return false;

		$time = (int)$time;
		if(!$file) trigger_error("File needs to be specified!", E_USER_ERROR);
		if($time<1) trigger_error("Time needs to be ineger greater than 0!", E_USER_ERROR);
		
		if(count($this->stack) && $file == $this->stack[count($this->stack)-1])
		{
			$filename = $this->path.$file;
			
			$data['__output__'] = ob_get_contents();
			ob_end_clean();
	
			if(file_exists($filename) && !is_writable($filename)) trigger_error("Cache file not writeable!", E_USER_ERROR);
	
			$f = fopen($filename, 'w');
			fwrite($f, $this->_pack($data));
			fclose($f);
			
			$this->_output($data['__output__']);
			
			unset( $this->stack[count($this->stack)-1]);
			
			return false;
		}
		elseif( count($this->stack) &&  in_array($file,$this->stack) )
		{
			trigger_error("Cache stack problem: ".$this->stack[count($this->stack)-1]." not properly finished!", E_USER_ERROR);
			exit;
		}
		else
		{
			$r = $this->_start($file,$time);
			if(is_int($r))
			{
				// $r is position of current cache on stack (+1)
				return $r;
			}
			else
			{
				// $r contains cached data
				for($i = 0;$i<count($data); $i++)
				{
					$data[$i] = $r[$i];
				}
				return false;
			}
		}
	
	}

}

?>