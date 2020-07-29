<?php

class Encryption {

	/**
	 * Encryption cipher
	 *
	 * @var	string
	 */
	protected $_cipher = 'aes-128';

	/**
	 * Cipher mode
	 *
	 * @var	string
	 */
	protected $_mode = 'cbc';

	/**
	 * Cipher handle
	 *
	 * @var	mixed
	 */
	protected $_handle;

	/**
	 * Encryption key
	 *
	 * @var	string
	 */
	public $_key;

	/**
	 * PHP extension to be used
	 *
	 * @var	string
	 */
	protected $_driver;

	/**
	 * List of usable drivers (PHP extensions)
	 *
	 * @var	array
	 */
	protected $_drivers = array();

	/**
	 * List of available modes
	 *
	 * @var	array
	 */
	protected $_modes = array(
		'mcrypt' => array(
			'cbc' => 'cbc',
			'ecb' => 'ecb',
			'ofb' => 'nofb',
			'ofb8' => 'ofb',
			'cfb' => 'ncfb',
			'cfb8' => 'cfb',
			'ctr' => 'ctr',
			'stream' => 'stream'
		),
		'openssl' => array(
			'cbc' => 'cbc',
			'ecb' => 'ecb',
			'ofb' => 'ofb',
			'cfb' => 'cfb',
			'cfb8' => 'cfb8',
			'ctr' => 'ctr',
			'stream' => '',
			'xts' => 'xts'
		)
	);

	/**
	 * List of supported HMAC algorithms
	 *
	 * name => digest size pairs
	 *
	 * @var	array
	 */
	protected $_digests = array(
		'sha224' => 28,
		'sha256' => 32,
		'sha384' => 48,
		'sha512' => 64
	);

	/**
	 * mbstring.func_overload flag
	 *
	 * @var	bool
	 */
	protected static $func_overload;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(array $params = array())
	{
		// $this->_key = "8c2e2b07afd09eaf24c983cb306ad9f7";
	}
	
	public function set_key(){
		$this->_key = md5(rand());
	}

	/**
	 * Decrypt
	 *
	 * @param	string	$data	Encrypted data
	 * @param	array	$params	Input parameters
	 * @return	string
	 */
	public function decrypt($data, array $params = NULL)
	{
		$params = array(
			'handle' => 'aes-128-cbc',
			'cipher' => 'aes-128',
			'mode' => 'cbc',
			'key' => NULL,
			'base64' => TRUE,
			'hmac_digest' => 'sha512',
			'hmac_key' => NULL
		);

		$digest_size = $this->_digests[$params['hmac_digest']] * 2;
			

		if (self::strlen($data) <= $digest_size)
		{
			return FALSE;
		}

		$hmac_input = self::substr($data, 0, $digest_size);
		$data = self::substr($data, $digest_size);

		isset($params['hmac_key']) OR $params['hmac_key'] = $this->hkdf($this->_key, 'sha512', NULL, NULL, 'authentication');
		$hmac_check = hash_hmac($params['hmac_digest'], $data, $params['hmac_key'], ! $params['base64']);

		// Time-attack-safe comparison
		$diff = 0;
		for ($i = 0; $i < $digest_size; $i++)
		{
			$diff |= ord($hmac_input[$i]) ^ ord($hmac_check[$i]);
		}
		
		if ($diff !== 0)
		{
			return FALSE;
		}
	
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * HKDF
	 *
	 * @link	https://tools.ietf.org/rfc/rfc5869.txt
	 * @param	$key	Input key
	 * @param	$digest	A SHA-2 hashing algorithm
	 * @param	$salt	Optional salt
	 * @param	$length	Output length (defaults to the selected digest size)
	 * @param	$info	Optional context/application-specific info
	 * @return	string	A pseudo-random key
	 */
	public function hkdf($key, $digest = 'sha512', $salt = NULL, $length = NULL, $info = '')
	{
		if ( ! isset($this->_digests[$digest]))
		{
			return FALSE;
		}

		if (empty($length) OR ! is_int($length))
		{
			$length = $this->_digests[$digest];
		}
		elseif ($length > (255 * $this->_digests[$digest]))
		{
			return FALSE;
		}

		self::strlen($salt) OR $salt = str_repeat("\0", $this->_digests[$digest]);

		$prk = hash_hmac($digest, $key, $salt, TRUE);
		$key = '';
		for ($key_block = '', $block_index = 1; self::strlen($key) < $length; $block_index++)
		{
			$key_block = hash_hmac($digest, $key_block.$info.chr($block_index), $prk, TRUE);
			$key .= $key_block;
		}

		return self::substr($key, 0, $length);
	}


	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return (self::$func_overload)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_overload)
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}
