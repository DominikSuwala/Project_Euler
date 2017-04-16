<?php
/**
  * Project #19 - Names Scores
  * Spec: https://projecteuler.net/problem=19
  * How many Sundays fell on the first of the month during the
  * twentieth century (1 Jan 1901 to 31 Dec 2000)?
  * This is generalizable to a (14 cases)* number of years
  * (14 cases = 7 days of the week * combinations(leapYearOrNot))
  * So basically if you precompute for Jan 1st Leap Year, Jan 1st NonLeap
  * all you would need to do is precompute 14 possibilities (1st of Month =
  * input), check the day Jan 1st falls on, and add the look-up value in
  * the table
  * J  | F  | M  | A  | M  | Jn  | Jl  | A  | S  | O  | N  | D  |
  * 31   28   31   30   31   30    31    31   30   31   30   31
  * 
*/

class Count_Days {
	function __construct($start_year=1900, $first_day=0) {
		$this->start_year = $start_year;
		$this->first_day = $first_day;
		$this->days = array (
			0 => 'Monday',
			1 => 'Tuesday',
			2 => 'Wednesday',
			3 => 'Thursday',
			4 => 'Friday',
			5 => 'Saturday',
			6 => 'Sunday'
		);
		$this->nonLeapYearCalendar = array (
			'January' => 31,
			'February' => 28,
			'March' => 31,
			'April' => 30,
			'May' => 31,
			'June' => 30,
			'July' => 31,
			'August' => 31,
			'September' => 30,
			'October' => 31,
			'November' => 30,
			'December' => 31
		);
		$this->leapYearCalendar = $this->nonLeapYearCalendar;
		$this->leapYearCalendar[ 'February' ] = 29;
		$this->smartTable = null;
	}
	/*
	 * $year must be int
	*/
	function isLeapYear($year) {
		return  (int)(
					(($year % 4 == 0) && !($year % 100 == 0)) || 
					($year % 400 == 0)
				);
		
	}
	function thisRotation($curYear) {
		if( $this->isLeapYear( $curYear ) ) {
			return $this->leapYearCalendar;
		}
		else {
			return $this->nonLeapYearCalendar;
		}
	}
	/*
	 * Returns the first day of the next year
	*/
	function firstDayNextFirst($year, $firstDayIndex) {
		$mod = 1;
		if( $this->isLeapYear( $year ) ) {
			$mod = 2;
		}
		return ( $firstDayIndex + $mod ) % 7;
	}
	function populateSmartTable() {
		# Set a fake start day for an arbitrary year,
		# construct a matrix
		# 14 possibilities (1st of month is {0,1...6}, leapYearOrNot {0,1})
		# Associative array is index as such:
		#   (leapYearOrNot * 10) + day
		#   0,1...,6 and 10,11,...,16
		$lookupTable = array();
		$calendars = array(
			$this->nonLeapYearCalendar,
			$this->leapYearCalendar
		);
		
		# Nests on nests
		# For every leap year situation
		# 	For every possible starting day
		# 		For every month
		#			Construct an array of first day of month
		for( $leapOrNot = 0; $leapOrNot < 2; $leapOrNot++ ) {
			$curCalendar = $calendars[ $leapOrNot ];
			
			foreach( $this->days as $dayEnum => $dayName ) {
				
				$dayNumbersFirstOfMonth = array( $dayEnum );
				$dayNum = 1;
				$curConfig = array();
				foreach( $curCalendar as $month => $daysInMonth ) {
					$tempx = ( $dayEnum + $dayNum - 1 ) % 7;
					$curFirstOfMonth = $tempx;
					#$curFirstOfMonth = $this->days[ $tempx ];
					array_push( $curConfig, $curFirstOfMonth );
					
					$dayNum += $daysInMonth;
					
				}
				array_push( $lookupTable, $curConfig );
			}
		}
		
		#var_dump( $lookupTable );
		return $lookupTable;
	}
	function getTargetID( $target ) {
		foreach( $this->days as $dayID => $dayName ) {
			if( $target == $dayName ) {
				return $dayID;
			}
		}
	}
	/*
	 * $target - "Count how many times $target falls on 1st of month within
	 *   [$startYear, $endYear)"
	 */
	function smartLoop( $target, $startYear, $endYear ) {
		# Assumes that the 14-case table has been constructed
		# Counts to $startYear, and executes "smart loop" which is a partially
		# optimized look-up table adder. Optimized for CPU time, not RAM
		
		$curYearFirstDay = $this->first_day;
		$targetsHit = 0;
		for( $thisYear = $startYear; $thisYear < $endYear; $thisYear++ ) {
			
			$configIndex = $curYearFirstDay;
			
			if( $this->isLeapYear( $thisYear ) ) {
				$configIndex += 7;
			}
			
			$mycounts = array_count_values( $this->smartTable[$configIndex] );
			#var_dump( $mycounts );
			
			$targetsHit += $mycounts[$target];
			$curYearFirstDay = $this->firstDayNextFirst($thisYear, $curYearFirstDay);
			
		}
		
		return $targetsHit;
	}
}

$startYear = null;
$endYear = null;
$target = null;

if( isset( $_GET[ 'start_year' ] ) ) {
	$startYear = $_GET[ 'start_year' ];
}
if( isset( $_GET[ 'end_year' ] ) ) {
	$endYear = $_GET[ 'end_year' ];
}
$mycal = new Count_Days($start_year, $target);
$mycal->smartTable = $mycal->populateSmartTable();

if( isset( $_GET[ 'target' ] ) ) {
	$mycal->target = $mycal->getTargetID( $_GET[ 'target' ] );
	$target = $mycal->target;
}
if( isset( $_GET[ 'first_day' ] ) ) {
	$firstDay = $mycal->getTargetID( $_GET[ 'first_day' ] );
	$mycal->first_day = $firstDay;
}


echo $mycal->days[$target] . "s in range [$startYear, $endYear): "
	 . $mycal->smartLoop($target, $startYear, $endYear );
# 1 Jan 1900 was a Monday.

# The day number of month x's first day; 1-based indexing


?>