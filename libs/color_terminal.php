<?
/**
 * Helper functions for outputing color in terminals
 */
class ColorTerminal {
	//Color codes:
	private static $colors = array(
		"black" => "0;30",
		"darkgrey" => "1;30",
		"blue" => "0;34",
		"lightblue" => "1;34",
		"green" => "0;32",
		"lightgreen" => "1;32",
		"cyan" => "0;36",
		"lightcyan" => "1;36",
		"red" => "0;31",
		"lightred" => "1;31",
		"purple" => "0;35",
		"lightpurple" => "1;35",
		"brown" => "0;33",
		"yellow" => "1;33",
		"normal" => "0;37",
		"bold" => "1;37"
	);

	/**
	 * Sets the color of the terminal output
	 */
	public static function set($color) {
		echo "\033[".self::$colors[$color]."m";
	}
}
