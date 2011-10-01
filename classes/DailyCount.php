<?php

class DailyCount extends BasicObject {

	/**
	 * Used by BasicObject to determine the table name.
	 * @returns the table name for the database relation.
	 */
	protected static function table_name() {
		return 'daily_count';
	}

	public static function last() {
		$ret = DailyCount::selection(array(
			'@order' => 'time:desc',
			'@limit' => 1,
		));
		$ret = array_shift($ret);
		if($ret == null) {
			// Create dummy count
			$ret = new DailyCount();
			$ret->time = '000-00-00';
			$ret->amount = 0;
		}
		return $ret;
	}

	public function __toString() {
		return (string)$this->time;
	}
}
?>

