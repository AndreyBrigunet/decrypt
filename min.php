<?php

echo $r = rand();
echo "<br>";

$min = getrandmax();
$status = true;

	while($status){

        $r = rand();

		if ($r < $min) {
            $min = $r;
            echo $min . "\n";
		}
	}