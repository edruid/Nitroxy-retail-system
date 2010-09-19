<?php
/**
 * En klass som inkapslar en MySQL-anslutning.
 *
 * användning:
 */
class DatabaseConnectioni extends MySQLi
{
	private $host;
	private $port;
	private $db;
	private $charset;
	private $connection;
	private $counter;
	private $timer_sum;
	private $timer_start;
	private $do_debug;

	/**
	 * Hämta debugging-inställning
	 * @return boolean true om debug, false annars
	 */
	function get_debug()
	{ return $this->do_debug; }

	/**
	 * Sätt debug-inställning
	 * @param boolean $dbg värde på debug-flaggan.
	 * @return boolean föregående värde på debug-flaggan
	 */
	function set_debug($dbg)
	{
		$ret = $this->do_debug;
		$this->do_debug = $dbg;
		return $ret;
	}

	/**
	 * Hämta total tid spenderad i SQL-frågor
	 * @return float total tid spenderad i SQL
	 */
	function get_timer()
	{ return $this->timer_sum; }

	/**
	 * Hämta totalt antal queries
	 * @return int antal sql-anrop plus ett (=uppkoppling mot databasen)
	 */
	function get_counter()
	{ return $this->counter; }

	/**
	 * Hämta namnet på databasen den här anslutningen använder.
	 * @return string databasnamnet
	 */
	function get_database_name()
	{ return $this->db; }

	/**
	 * Hämta värddatornamn som den här databas-anslutningen kopplar till.
	 * @return string värddatornamnet som databasen ligger på
	 */
	function get_hostname()
	{ return $this->host; }

	/**
	 * Hämta charset som används för denna anslutning.
	 * @return string charset
	 */
	function get_charset()
	{ return $this->charset; }

	/**
	 * @see mysqli::real_escape_string
	 * @return string data, escapat
	 */
	function escape($string)
	{ return $this->real_escape_string($string); }

	/**
	 * Starta intern timer
	 * @return void
	 */
	private function start_timer()
	{ $this->timer_start = microtime(true); }

	/**
	 * stoppa intern timer
	 * @return float tid sedan timern startade.
	 */
	private function stop_timer()
	{
		$this->counter++;
		 return ($this->timer_sum += microtime(true) - $this->timer_start);
	}

	/**
	 * Skapa en databas-anslutning.
	 *
	 * Laddar inställningar från global $repo_root/db_settings/$db_alias.php.
	 * $host, $user, $passwd och $dbname förväntas vara satta.
	 *
	 * @param string $db_alias namnet på anslutningen du vill skapa. (dvs db_settings/$db_alias.php).
	 */
	function __construct($db_alias, $debug = false)
	{
		global $repo_root,$notify;
		$this->do_debug = $debug;
		
		$settings_basefile = $repo_root.'/db_settings/'.$db_alias;
		$settings_file = $settings_basefile . '.php';
		$settings_localfile = $settings_basefile . '.local.php';
		
		require $settings_file;
		if ( file_exists($settings_localfile) ){
			require $settings_localfile;
		}
		
		$this->db = $dbname;
		$this->host = $host;
		$this->port = isset($port) ? $port : 3306;
		$this->charset = $charset;

		$this->start_timer();
		parent::__construct($host,$user,$passwd,$dbname,$this->port);
		$this->stop_timer();

		if(mysqli_connect_errno())
		{
			if($this->do_debug)
			{
				$debuginfo = debug_backtrace();
				$debuginfo = $debuginfo[0];
				print("<p>MySQLi connection problem: ".mysqli_connect_errno()." (".mysqli_connect_error().").<br />".
					"Problem encountered when trying to connect to ".$this->host.":".$this->port.", database ".$this->db.".<br />".
					"The error was encountered in ".$debuginfo['file'].":".$debuginfo['line']."<br /></p>");
				debug_print_backtrace();
			}
			else {
				$notify->admin_alert("Could not connect to SQL database: ".mysqli_connect_errno()." (".mysqli_connect_error().").", false);
			}
			die('<p>Kunde inte ansluta till databasen. Var god kontakta oss.</p>');
		}

		$this->start_timer();
		if(!(parent::set_charset($charset))) {
			if($this->do_debug)
			{
				$debuginfo = debug_backtrace();
				$debuginfo = $debuginfo[0];
				print("<p>MySQLi connection problem trying to set charset to \"$charset\": ".mysqli_errno()." (".mysqli_error().").<br />".
					"The error was encountered in ".$debuginfo['file'].":".$debuginfo['line']."<br /></p>");
				debug_print_backtrace();
			} else {
				$notify->admin_alert("Could not set charset for database connection: ".mysqli_errno()." (".mysqli_error().").", false);
			}
			die('<p>Ett problem uppstod med databasanslutningen. Var god kontakta oss.</p>');
		}
		$this->stop_timer();

		unset($user);
		unset($host);
		unset($passwd);
		unset($dbname);
		unset($charset);
	}

	/**
	 * Same semantics as prepare_full, but does not return anything and calls $stmt->fetch();$stmt->close().
	 * @return boolean @see MySQLi_STMT->fetch()
	 */
	function prepare_fetch($query, &$bind_params=null, $types=null)
	{
		$array = func_get_args();
		$array[1] = &$bind_params;
		$stmt = call_user_func_array(array($this, 'prepare_full'), $array);
		$ret = $stmt->fetch();
		$stmt->close();
		return $ret;
	}

	/**
	 * Perform a mysqli query.
	 *
	 * This method will
	 * *prepare query
	 * *bind parameters (optional)
	 * *bind result (optional)
	 * *execute query
	 * *store the result (optional).
	 * An instance of MySQLi_STMT is returned for iteration over the (optional) query results.
	 *
	 * Example:
	 * $stmt = $db->prepare_full(
	 * 	'select id, tidpunkt, vem, typ, info from adminlog where id=?',
	 * 	array(&$id,&$tidpunkt,&$vem,&$typ,&$info),
	 * 	'i', 1772);
	 *
	 * while($stmt->fetch())
	 * 	print("<p>Row: ".$id."</p>\n");
	 * 
	 * $stmt->close();
	 *
	 * Example:
	 * $ar = array();
	 * $stmt = $db-prepare_full
	 * 	'select * from adminlog where id=?',
	 * 	&$ar,
	 * 	'i', 1772);
	 *
	 * while($stmt->fetch())
	 * 	print("<p>Row: ".$ar['id']."</p>\n");
	 * 
	 * $stmt->close();
	 * 
	 * @param string $query the query to prepare and perform
	 * @param array(any) $bind_params an array of references to variables (in order) to fetch the results to
	 * @param string $types mysqli type definition of parameters
	 * All arguments after $types are considered parameters to the SQL query, of the types given in $types.
	 *
	 * @return MySQLi_STMT statement som representerar den genomförda queryn.
	 */
	function prepare_full($query, &$bind_results=null, $types=null)
	{
		global $notify;
		$this->start_timer();
		$stmt = $this->prepare($query);
		if(!$stmt)
		{
			if($this->do_debug)
			{
				$debuginfo = debug_backtrace();
				$debuginfo = $debuginfo[0];
				print("<p>MySQLi error: ".$this->errno." (".$this->error.").<br />".
					"The attempted query was <pre>".$query."</pre><br />".
					"The error was encountered in ".$debuginfo['file'].":".$debuginfo['line']."<br /></p>");
				print("<pre>");
				debug_print_backtrace();
				print("</pre>");
			}
			else
				$notify->admin_alert("Could not prepare SQL statement: ".$this->errno." (".$this->error.").");
			die('<p>Kunde inte förbereda databas-förfrågan. Administratörerna känner till problemet, men '.
				'<a href="?main=contact">kontakta oss</a> gärna om du vill!</p>');
		}

		if($types != null)
		{
			$array = func_get_args();
			array_shift($array); // throw away query
			array_shift($array); // throw away result bindings

			/* Since PHP 5.3.0, mysqli_stmt_bind_param requires parameters to be passed by reference.
			 * Therefore, we need a loop to create references.
			 * First I tried using foreach here, but PHP lets you change the value of the parameter
			 *   after calling bind_param. I guess that's why they want references instead.
			 * Anyhow, the result is that if foreach is used, all parameters get the value of the
			 *   last parameter.
			 */
			$ref_array=array();
			$ref_array[]=array_shift($array); // parameter type list (string), passed by value.
			for($i=0;$i<count($array);$i++) {
				$ref_array[]=&$array[$i];
			}
			call_user_func_array(array($stmt, 'bind_param'), $ref_array);
		}

		if(count($bind_results) > 0){
			call_user_func_array(array($stmt, 'bind_result'), $bind_results);
		} else if(is_array($bind_results)) {
			$fields = $stmt->result_metadata();
			while($field = $fields->fetch_field()){
				$bind_results[$field->name] = &$row[$field->name];
			}
			call_user_func_array(array($stmt, 'bind_result'), $bind_results);
		}
			
		if(!$stmt->execute())
		{
			if($this->do_debug)
			{
				$debuginfo = debug_backtrace();
				$debuginfo = $debuginfo[0];
				print('<p>MySQLi_STMT->execute() error: '.$stmt->errno." (".$stmt->error.").<br />".
					"The attempted query was <pre>".$query."</pre>.<br />".
					"The error was encountered in ".$debuginfo['file'].":".$debuginfo['line']."<br /></p>");
				print("<pre>");
				debug_print_backtrace();
				print("</pre>");
			}
			else
				$notify->admin_alert("SQL query execute() failed: ".$query.": ".$stmt->errno." (".$stmt->error.").\n");
			die("<p>En databasförfrågan misslyckades. Administratörerna känner till problemet, men ".
				'<a href="?main=contact">kontakta oss</a> gärna om du vill!</p>');
		}

		if(count($bind_results) > 0)
			$stmt->store_result();
		$this->stop_timer();
		return $stmt;
	}
}
?>
