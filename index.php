<?php

error_reporting(-1);
ini_set('display_errors', 1);

define('DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('INC_DIR', DIR . 'inc' . DIRECTORY_SEPARATOR);

require_once DIR . 'encryption.php';
require_once DIR . 'functions.php';

// $bad = "2bcb12384f23cf37135caa7717d452b1d119514ad361ab0b05426c304a0c9518360cc7c7e96a83ace2e3580b06033286a3cff1811eb903c503902147cbbcc64erECCwj/AxNi0mpg5EBrxtJDiXu6EK1o5GAf+q3j6jJw+8WgoaGcKtW0mUhFN7JJse/uEZBGBVnqlUpTlzf6BqA==";
// $ok = "870429ff43613f5392ec90213d3e228ebd2388ae091de90d5723cd372d3eb8230a671c90bf9f43666a915fc336dfeab6ca6644579c18281a07f83710f569c451c0Qpq7vWhKCaMNUxM0MXYYvm07urZRl4DpG6Hxu//8M=";
$bad = urldecode("9c313713cb96e81c96a4c02f804acefee2a816c1d202e15db2b528dad820fbb9bae77269621651922ac59f55b0da2d5c001bcea3cdce844c3a2bdeae1ebe8833Q1ZKHHUfpt0eDgHGt4Tsy%2BZrPXLqtrGVr%2FWQHmoeIBLI7YfXnH9X2o5eqKxL1EcnoCjd3FzysZRy0TOeGkEQnA%3D%3D");

$encryp = new Encryption();

$getrandmax = 2147483647;
//             227000001
$status = true;
$number = 18000010;
$max = $getrandmax;
$step = 1000000;

    $time_start = microtime(true);

	
	while($status){
		
		$key = md5($number);
		
		$encryp->set_key($key);
		// echo $encryp->_key;
		
		if ($encryp->decrypt($bad)) {
            sleep(2);
			@file_get_contents('https://tg.brigu.net/api/decrypt/' .$encryp->_key );
			$status = false;
		}

        if ($number > $step) {
			$time_end1 = microtime(true);
			$time1 = ($time_end1 - $time_start)/60;
			echo "Number: {$number} \n";
            echo "Time: {$time1} min \n\n";
            $step = $step + 1000000;
        }
		// usleep(1);
		
		if ($number > $max) {

			$time_end = microtime(true);
			$time = ($time_end - $time_start)/60;
			echo "Process Time: {$time} min \n";

			$status = false;
		}
		
		$number++;
	}