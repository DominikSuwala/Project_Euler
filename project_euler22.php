<?php
/**
  * Project #22 - Names Scores
  * Spec: https://projecteuler.net/problem=22
*/

class Names_Scores {
	function __construct($filename="p022_names.txt") {
		$this->filename = $filename;
		$this->names = $this->readFromFile();
	}
	function getNames() {
		return $this->names;
	}
	function readFromFile() {
		if( !file_exists( $this->filename ) ) {
			die( "File $this->filename does not exist." );
		}
		return str_getcsv( file_get_contents($this->filename), ",", '"' );
	}
	function score($nameIndex) {
		$score = 0;
		$A = ord('A') - 1;
		for( $i = 0; $i < strlen( $this->names[$nameIndex] ); $i++ ) {
			$score += ord( $this->names[$nameIndex]{$i} ) - $A;
		}
		return $score;
	}
}

$inst1 = new Names_Scores($_GET['filename']);
sort($inst1->names);

$totalScore = 0;
for( $i=0; $i < count($inst1->names); $i++ ) {
	$cur = ($inst1->score($i) * ($i+1));
	#$outputLine = $inst1->names[$i] . ':' . $cur . '<br>';
	$totalScore = gmp_add( gmp_strval($totalScore), strval($cur) );
}

echo gmp_strval($totalScore);

?>