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
		return array_shift($ret);
	}

	public function __toString() {
		return (string)$this->time;
	}
}
?>

