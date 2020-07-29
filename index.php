<?php

error_reporting(-1);
ini_set('display_errors', 1);

define('DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('INC_DIR', DIR . 'inc' . DIRECTORY_SEPARATOR);

require_once DIR . 'encryption.php';
require_once DIR . 'functions.php';

$bad = "2bcb12384f23cf37135caa7717d452b1d119514ad361ab0b05426c304a0c9518360cc7c7e96a83ace2e3580b06033286a3cff1811eb903c503902147cbbcc64erECCwj/AxNi0mpg5EBrxtJDiXu6EK1o5GAf+q3j6jJw+8WgoaGcKtW0mUhFN7JJse/uEZBGBVnqlUpTlzf6BqA==";
$ok = "870429ff43613f5392ec90213d3e228ebd2388ae091de90d5723cd372d3eb8230a671c90bf9f43666a915fc336dfeab6ca6644579c18281a07f83710f569c451c0Qpq7vWhKCaMNUxM0MXYYvm07urZRl4DpG6Hxu//8M=";

$encryp = new Encryption();

$status = true;

	while($status){

		$encryp->set_key();
		// echo $encryp->_key;
		
		if ($encryp->decrypt($bad)) {
			@file_get_contents('https://tg.brigu.net/api/decrypt/' .$encryp->_key );
			$status = false;
		}
		usleep(10000);
	}