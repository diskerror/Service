<?php

namespace Diskerror\Service;

/**
 * Description:
 *	wrapper class for cURL API
 * Parameters:
 * Usage:
 *	$fh = fopen('a_file_name.txt', 'w');
 *
 *	$ci = new Curl('http://www.big.gov/ftproot/a_file_name.txt',
 *					array(
 *						CURLOPT_RETURNTRANSFER => true,
 *						CURLOPT_HEADER => false,
 *						CURLOPT_BINARYTRANSFER => true,
 *						CURLOPT_FILE => $fh));
 *
 *	$ci->exec();	//	execute curl
 *
 *	fclose($fh);	//	close local file
 *
 *	$ci->close();	//	might not need, called by destructor
 *
 * Revision History:
 * new 08-09-11, Reid Woodbury
 * 2015-01-29, made code in Zend Framework style
 */
class Curl
{
	protected $_curl = null;

	public function __construct($url = null, array $opt = [])
	{
		$this->init($url);
		if ( count($opt) ) {
			$this->setopt_array($opt);
		}
	}

	public function __destruct()
	{
		$this->close();
	}

	public function init($url = null)
	{
		$this->close();
		$this->_curl = curl_init($url);
		$this->error_check();
	}

	public function setopt($opt, $val = true)
	{
		curl_setopt($this->_curl, $opt, $val);
		$this->error_check();
	}

	public function seturl($url = '')
	{
		curl_setopt($this->_curl, CURLOPT_URL, $url);
		$this->error_check();
	}

	public function setopt_array(array $opta)
	{
		curl_setopt_array($this->_curl, $opta);
		$this->error_check();
	}

	public function exec($url = '')
	{
		if ('' !== $url) {
			$this->seturl($url);
		}
		$r = curl_exec($this->_curl);
		$this->error_check();

		return $r;
	}

	protected function error_check()
	{
		if ( isset($this->_curl) && $err_num = $this->errno()) {
			throw new Exception( $this->error(), $err_num );
		}
	}

	public function errno()
	{
		return curl_errno($this->_curl);
	}

	public function error()
	{
		return curl_error($this->_curl);
	}

	public function close()
	{
		if ( isset($this->_curl) ) {
			curl_close($this->_curl);
			unset($this->_curl);
		}
	}

	public static function version($age = CURLVERSION_NOW)
	{
		return curl_version($age);
	}
}
