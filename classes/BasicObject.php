<?php

/**
 * {@example BasicObjectExample.php}
 */ 
abstract class BasicObject {

	protected $_data;
	protected $_exists;

	/**
	 * Returns the table name associated with this class.
	 * @return The name of the table this class is associated with.
	 */
//	abstract protected static function table_name();

	/**
	 * Returns a list of Objects of this class where the conditions
	 * specified in $params are true on all objects.
	 * @param $params Array An array of conditions.
	 * If $params is empty, all objects will be returned.
	 * $params is structured as:
	 *   array(
	 *     '<<column>>:<<operator>>' => <<value>>,
	 *     array(
	 *       'column' => <<column>>,
	 *       'value' => <<value>>
	 *     ),
	 *     '@manual_query' => <<valid where clause>>,
	 *     [...,]
	 *     // special clauses
	 *     '@or' => array([<params>]),
	 *     '@and' => array([<params>]),
	 *     '@order' => array(<<order-column>> [, <<order-column>> ...]) | <<order-column>>,
	 *     '@limit' => array(<<limit>> [, <<limit>>]),
	 *   )
	 * @returns Array An array of Objects.
	 */
//	abstract public static function selection($params = array());

	/**
	 * Returns the number of items matching the conditions.
	 * @param $params Array Se selection for structure of $params.
	 * @returns Int the number of items matching the conditions.
	 */
//	abstract public static function count($params = array());

	/**
	 * Returns the Object with object_id = $id.
	 * @param $id Integer The ID of the Object requested.
	 * @return Object The Object specified by $id.
	 */
//	abstract public static function from_id($id);

	/**
	 * Returns the table name associated with this class.
	 * @return The name of the table this class is associated with.
	 */
	private static function id_name($class_name = null){
		$pk = static::primary_key($class_name);
		if(count($pk) < 1) {
			return null;
		}
		if(count($pk) > 1) {
			return $pk;
		}
		return $pk[0];
	}

	private static function primary_key($class_name = null) {
		global $db;
		static $column_ids = array();
			
		if(class_exists($class_name) && is_subclass_of($class_name, 'BasicObject')){
			$table_name = $class_name::table_name();
		} elseif($class_name == null) {
			$table_name = static::table_name();
		} else {
			$table_name = $class_name;
		}
		if(!array_key_exists($table_name, $column_ids)){
			$stmt = $db->prepare("
				SELECT
					`COLUMN_NAME`
				FROM
					`information_schema`.`key_column_usage` join
					`information_schema`.`table_constraints` USING (`CONSTRAINT_NAME`, `CONSTRAINT_SCHEMA`, `TABLE_NAME`)
				WHERE
					`table_constraints`.`CONSTRAINT_TYPE` = 'PRIMARY KEY' AND
					`table_constraints`.`CONSTRAINT_SCHEMA` = ? AND
					`table_constraints`.`TABLE_NAME` = ?"
			);
			$db_name = self::get_database_name();
			$stmt->bind_param('ss', $db_name, $table_name);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($index);
			
			while($stmt->fetch()) {
				$column_ids[$table_name][] = $index;
			}
			$stmt->close();
		}
		return $column_ids[$table_name];
	}

	private static function unique_identifier($class_name = null) {
		if(class_exists($class_name) && is_subclass_of($class_name, 'BasicObject')){
			$table_name = $class_name::table_name();
		} elseif($class_name == null) {
			$table_name = static::table_name();
		} else {
			$table_name = $class_name;
		}
		$pk = static::primary_key($class_name);
		if(count($pk)==1) {
			return "`$table_name`.`{$pk[0]}`";
		} elseif(empty($pk)) {
			throw new Exception("A table should have a primary key to use BasicObject");
		} else {
			return 'concat(`'.$table_name.'`.`'.implode("`, '¤', `$table_name`.`", $pk).'`)';
		}
	}
	
	public function __construct($array = null) {
		$this->_exists = !empty($array);
		$this->_data = $array;
	}

	/**
	 * Returns values in this table or Objects of neighboring tables if there is a foreign key.
	 * @param array Only alowed when accessing other tables. Extra paramaters for selection
	 * see selection() for details.
	 * @returns mixed If the function name is the exact name of a neighboring class, an object or
	 * a list of objects is returned depending on the direction of the foreign key.
	 * Oterwise if there exists a value ($object->value) that has the same name as the name called,
	 * then that value is returned.
	 */
	public function __call($name, $arguments){
		if(class_exists($name) && is_subclass_of($name, 'BasicObject')){
			$other_table = $name::table_name();
			$con = $this->connection($this->table_name(), $other_table);
			if($con) {
				if(isset($arguments[0]) && is_array($arguments[0])){
					$params = $arguments[0];
				} else {
					$params = array();
				}
				if($con['TABLE_NAME'] == $this->table_name()){
					// We know them (single value)
					$ref_name = $con['COLUMN_NAME'];
					return $name::from_id($this->$ref_name);
				} else {
					// They know us (multiple values)
					$params[$con['COLUMN_NAME']] = $this->id;
					return $name::selection($params);
				}
			}
		}
		if(count($arguments) == 0){
			try{
				return $this->__get($name);
			} catch(UndefinedMemberException $e) {
			}
		}
		throw new UndefinedFunctionException("Undefined call to function '".__CLASS__."::$name'");
	}

	/**
	 * Returns values in this table or Objects of neighboring tables if there is a foreign key.
	 * Overload this method to define specific behaviours such as denying access and custom
	 * formating.
	 * @returns mixed If there exists a column in the table with the same name the value of the
	 * field is returned.
	 * Otherwise if the property name is the exact name of a neighboring class, an object or
	 * a list of objects is returned depending on the direction of the foreign key.
	 */
	public function __get($name){
		if($this->in_table($name, $this->table_name())){
			if(array_key_exists(strtolower($name), $this->_data)) {
				$ret = $this->_data[strtolower($name)];
				if(HTML_ACCESS) {
					$ret = htmlspecialchars($ret, ENT_QUOTES, 'utf-8');
				}
				return $ret;
			} else {
				return null;
			}
		}
		if(class_exists($name) && is_subclass_of($name, 'BasicObject')){
			return $this->$name(array());
		}
		if($name == 'id'){
			$name = $this->id_name();
			return $this->$name;
		}
		throw new UndefinedMemberException("unknown property '$name'");
	}

	protected function is_protected($name) {
		return false;
	}

	/**
	 * Returns wether a variable in this object is set.
	 * @param string property name
	 * @returns bool Returns True if the value exists an is not null, false otherwise.
	 */
	public function __isset($name) {
		if(isset($this->_data[strtolower($name)])) {
			return true;
		}
		try{
			$data = $this->__get($name);
			return isset($data);
		} catch(Exception $e) {
			return false;
		}
	}

	/**
	 * Set the value of a field. Use commit() to write to database.
	 */
	public function __set($name, $value) {
		if($this->is_protected($name)){
			$trace = debug_backtrace();
			if(!isset($trace[1]) || $trace[1]['object'] != $trace[0]['object']) {
				throw new Exception("Trying to set protected member '$name' from public scope.");
			}
		}
		if($name == 'id'){
			$name = $this->id_name();
			$this->$name = $value;
		}
		if($this->in_table($name, $this->table_name())) {
			$this->_data[$name] = $value;
		} elseif($this->is_table($name) && $this->in_table($this->id_name($name), $this->table_name())) {
			$name = $this->id_name($name);
			$this->$name = $value->id;
		} else {
			throw new Exception("unknown property '$name'");
		}
	}

	/**
	 * Commits all fields to database. If this object was created with "new Object()" a new row
	 * will be created in the table and this object will atempt to update itself with automagic values.
	 * If the inhereting class wants to do special things on creation, it is best to overload this method
	 * and do them again.
	 */
	public function commit() {
		global $db;
		if(isset($this->_exists) && $this->_exists){
			$query = "UPDATE `".$this->table_name()."` SET\n";
			$old_object = $this->from_id($this->id);
		} else {
			$query = "INSERT INTO `".$this->table_name()."` SET\n";
		}
		$types = '';
		$params = array(&$types);
		$change = false;
		foreach($this->_data as $column => $value){
			if(!isset($old_object) || $old_object->_data[$column] != $value) {
				$params[] = &$this->_data[$column];
				$query .= "	`$column` = ?,\n";
				$types .= 's';
				$change = true;
			}
		}
		if(!$change) {
			// No change to data means no on change hooks in mysql.
			return;
		}
		$query = substr($query, 0, -2);

		if(isset($this->_exists) && $this->_exists){
			if(is_array($this->id_name())) {
				$query .= "\nWHERE ";
				$subquery = '';
				foreach($this->id_name() as $field) {
					$subquery .= "`$field` = ? AND ";
					$dummy[$field] = $this->$field;
					$params[] = &$dummy[$field];
				}
				$query .= substr($subquery, 0, -5);
			} else {
				$query .= "\nWHERE `".$this->id_name()."` = ?";
				$id = $this->id;
			}
			$params[] = &$id;
			$types .= 'i';
		}
		$stmt = $db->prepare($query);
		call_user_func_array(array($stmt, 'bind_param'), $params);
		if(!$stmt->execute()) {
			throw new Exception("Internal error, failed to execute query:\n<pre>$query\n".$stmt->error.'</pre>');
		}
		$stmt->close();
		if(!isset($this->_exists) || !$this->_exists){
			$this->_exists = true;
			if($db->insert_id) {
				$object = $this->from_id($db->insert_id);
			} else {
				$id_name = $this->id_name();
				if(!is_array($id_name)) {
					$object = $this->from_id($this->$id_name);
				} else {
					$params = array();
					foreach($this->id_name() as $field) {
						$params[$field] = $this->$field;
					}
					// no id? try to get the element from what we just set it to..
					$elems = $this->selection($params);
					if(count($elems) != 1) {
						throw new Exception("No id column and non unique data");
					}
					$object = $elems[0];
				}
			}
			$this->_data = $object->_data;
		}
	}

	/**
	 * Deletes this object from the database and calls unset on this object.
	 */
	public function delete() {
		global $db;
		if(isset($this->_exists) && $this->_exists){
			$stmt = $db->prepare("
				DELETE FROM ".$this->table_name()."
				WHERE ".$this->id_name()." = ?"
			);
			$stmt->bind_param('s', $this->id);
			$stmt->execute();
			$stmt->close();
		}
		unset($this);
	}

	public static function from_id($id){
		$id_name = static::id_name(); 
		return static::from_field($id_name, $id);
	}

	protected static function from_field($field, $value, $type='s'){
		global $db;
		$table_name = static::table_name(); 
		if(!self::in_table($field, $table_name)){
			throw new Exception("No such column '$field' in table '$table_name'");
		}
		$stmt = $db->prepare(
			"SELECT *\n".
			"FROM `".$table_name."`\n".
			"WHERE `".$field."` = ?\n".
			"LIMIT 1"
		);
		$stmt->bind_param($type, $value);
		$stmt->execute();
		$stmt->store_result();
		$fields = $stmt->result_metadata();
		while($field = $fields->fetch_field()){
			$bind_results[$field->name] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $bind_results);
		$object = null;
		if($stmt->fetch()) {
			$object = new static($bind_results);
		}
		$stmt->close();
		return $object;
	}

	public static function sum($field, $params = array()) {
		global $db;
		$data = static::build_query($params, '*');
		$query = array_shift($data);
		$allowed_symbols=array('*', '+', '/', '-', );
		if(is_array($field)) {
			$f = array_shift($field);
			if(!self::in_table($f, static::table_name())){
				throw new Exception("No such column '$field' in table '".static::table_name()."'");
			}
			$exp = "`$f`";
			while($f = array_shift($field)) {
				if(!in_array($f, $allowed_symbols)) {
					throw new Exception("Non allowed symbol '$f' in expression");
				}
				$exp .= " $f ";
				if(!($f = array_shift($field))) {
					throw new Exception("Mismatched expression");
				}
				if(!self::in_table($f, static::table_name())){
					throw new Exception("No such column '$f' in table '".static::table_name()."'");
				}
				$exp .= "`$f`";
			}	
			$query = "SELECT SUM($exp) FROM ($query) q";
		} else {
			if(!self::in_table($field, static::table_name())){
				throw new Exception("No such column '$field' in table '".static::table_name()."'");
			}
			$query = "SELECT SUM(`$field`) FROM ($query) q";
		}
		$stmt = $db->prepare($query);
		foreach($data as $key => $value) {
			$data[$key] = &$data[$key];
		}
		if(count($params)!=0) {
			call_user_func_array(array($stmt, 'bind_param'), $data);
		}
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($result);
		$stmt->fetch();
		$stmt->close();
		return $result;
	}


	public static function count($params = array()){
		global $db;
		$data = static::build_query($params, 'count');
		$query = array_shift($data);
		$stmt = $db->prepare($query);
		foreach($data as $key => $value) {
			$data[$key] = &$data[$key];
		}
		if(count($params)!=0) {
			call_user_func_array(array($stmt, 'bind_param'), $data);
		}
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($result);
		$stmt->fetch();
		$stmt->close();
		return $result;
	}

	public static function selection($params = array()){
		global $db;
		$data = self::build_query($params, '*');
		$query = array_shift($data);
		$stmt = $db->prepare($query);
		if(!$stmt) {
			throw new Exception("BasicObject: error parcing query: $query\n $db->error");
		}
		foreach($data as $key => $value) {
			$data[$key] = &$data[$key];
		}
		if(count($data)>1) {
			call_user_func_array(array($stmt, 'bind_param'), $data);
		}
		$stmt->execute();
		$stmt->store_result();
		$fields = $stmt->result_metadata();
		while($field = $fields->fetch_field()){
			$result[$field->name] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $result);

		$ret = array();
		while($stmt->fetch()){
			// fix result so they don't all referencde the same stuff.
			$tmp = array();
			foreach($result as $key => $value){
				$tmp[$key] = $value;
			}
			$ret[] = new static($tmp);
		}
		$stmt->close();
		return $ret;
	}

	private static function build_query($params, $select){
		$table_name = static::table_name(); 
		$id_name = static::id_name(); 
		$joins = array();
		$wheres = '';
		$order = array();
		$user_params = array();
		$types = self::handle_params($params, $joins, $wheres, $order, $table_name, $limit, $user_params, 'AND');
		$query = "SELECT ";
		switch($select) {
			case '*':
				$query .= "`".$table_name."`.*\n";
				$group = "\nGROUP BY ".static::unique_identifier();
				break;
			case 'count':
				$query .= "COUNT(DISTINCT(".static::unique_identifier().")) AS `count`\n";
				$group = "";
				break;
		}
		$query .= 
			"FROM\n".
			"	`".$table_name."`";
		foreach($joins as $table => $join){
			$query .= " JOIN\n";
			if(isset($join['using'])){
				$query .= "	`".$table."` USING (`".$join['using']."`)";
			} else {
				$query .= "	`".$table."` ON (".$join['on'].")";
			}
		}
		$query .= "\n";
		$result = array();
		$prepare_full_params = array(&$query, $types); // note the & in &$query making the changes to $query in subsequent lines matter
		if(strlen($wheres) > 0){
			$wheres = substr($wheres, 0, -5);
			$query .= "WHERE\n$wheres";
			foreach($user_params as $user_param){
				$prepare_full_params[] = $user_param;
			}
		}
		$query .= $group;
		if(count($order) > 0){
			$query .= "\nORDER BY\n	";
			$query .= implode(",\n	", $order);
		}
		if(isset($limit)){
			$query .= "\n$limit";
		}
		return $prepare_full_params;
	}

	private static function handle_params($params, &$joins, &$wheres, &$order, &$table_name, &$limit, &$user_params, $glue = 'AND') {
		$columns = self::columns($table_name);
		$types = '';
		foreach($params as $column => $value){
			// give a possibility to have multiple params with the same column.
			if(is_int($column) && is_array($value) && isset($value['column']) && isset($value['value'])){
				$column = $value['column'];
				$value = $value['value'];
			}
			if($column[0] == '@'){
				$column = explode(':', $column);
				$column = $column[0];
				// special parameter
				switch($column){
					case '@custom_order':
						$order[] = $value;
						break;
					case '@order':
						if(!is_array($value)){
							$value = array($value);
						}
						foreach($value as $o){
							$desc = false;
							if(substr($o,-5) == ':desc'){
								$desc = true;
								$o = substr($o, 0,-5);
							}
							$path = explode('.', $o);
							if(count($path)>1){
								$o = '`'.self::fix_join($path, $joins, $columns, $table_name).'`';
							} elseif(self::in_table($o, $table_name)){
								$o = "`$table_name`.`$o`";
							} else {
								throw new Exception("No such column '$o' in table '$table_name' (value '$value')");
							}
							if($desc){
								$o .= ' DESC';
							}
							$order[] = $o;
						}
						break;
					case '@limit':
						if(is_numeric($value)){
							$value = array($value);
						}
						if(!is_array($value) || count($value) > 2){
							throw new Exception("Expected array or number for limit clause");
						}
						foreach($value as $v){
							if(!is_numeric($v) && $v>=0){
								throw new Exception("Limit must be numeric clauses only");
							}
						}
						$limit = "LIMIT ".$value[0];
						if(count($value) == 2){
							$limit .= ', '.$value[1];
						}
						break;
					case '@manual_query':
						if(is_array($value)){
							$wheres .= "	({$value['where']}) $glue\n";
							$types .= $value['types'];
							$user_params = array_merge($user_params, $value['params']);
						} else {
							$wheres .= "	($value) $glue\n";
						}
						break;
					case '@or':
						$where = '';
						$types .= self::handle_params($value, $joins, $where, $order, $table_name, $limit, $user_params, 'OR');
						$wheres .= "(\n".substr($where, 0, -4)."\n) $glue\n";
						break;
					case '@and':
						$where = '';
						$types .= self::handle_params($value, $joins, $where, $order, $table_name, $limit, $user_params, 'AND');
						$wheres = "(\n".substr($where, 0, -5)."\n) $glue\n";
						break;
					default:
						throw new Exception("No such operator '".substr($column,1)."' (value '$value')");
				}
			} else {
				$where=array();
				// handle operator
				$column = explode(':', $column);
				if(count($column) > 1) {
					// Has operator
					$where['operator'] = self::operator($column[1]);
				} else {
					// default operator
					$where['operator'] = '=';
				}
				$column = $column[0];

				// handle column
				$path = explode('.', $column);
				if(count($path)>1){
					$where['column'] = self::fix_join($path, $joins, $columns, $table_name);
				} else {
					if(!self::in_table($column, $table_name)){
						throw new Exception("No such column '$column' in table '$table_name' (value '$value')");
					}
					$where['column'] = $table_name.'`.`'.$column;
				}
				if($where['operator'] == 'in') {
					$wheres .= "	`{$where['column']}` IN (";
					if(!is_array($value)){
						throw new Exception("Operator 'in' should be coupled with an array of values.");
					}
					foreach($value as $v){
						$types .= 's';
						$wheres .= '?, ';
						$user_params[] = $v;
					}
					$wheres = substr($wheres, 0, -2);
					$wheres .= ") $glue\n";
				} elseif($where['operator'] == 'null') {
					$wheres .= "	`".$where["column"]."` IS NULL $glue\n";
				} elseif($where['operator'] == 'not_null') {
					$wheres .= "	`".$where["column"]."` IS NOT NULL $glue\n";
				} else {
					$user_params[] = $value;
					$wheres .= "	`".$where["column"]."` ".$where['operator']." ? ".$glue."\n";
					$types.='s';
				}
			}
		}
		return $types;
	}

	private static function columns($table){
		global $db;
		static $columns = array();
		if(!isset($columns[$table])){
			if(!self::is_table($table)){
				throw new Exception("No such table '$table'");
			}
			$column[$table] = array();
			$stmt = $db->prepare(
				"SELECT `COLUMN_NAME`\n".
				"FROM `information_schema`.`COLUMNS`\n".
				"WHERE\n".
				"	`TABLE_SCHEMA` = ? AND\n".
				"	`table_name` = ?"
			);
			$db_name = self::get_database_name();
			$stmt->bind_param('ss', $db_name, $table);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($column);
			while($stmt->fetch()){
				$columns[$table][] = $column;
			}
			$stmt->close();
		}
		return $columns[$table];
	}

	private static function operator($expr){
		switch($expr){
			case "=":
			case "!=":
			case "<=":
			case ">=":
			case "<":
			case ">":
			case "regexp":
			case "like":
			case "in":
			case "null":
			case "not_null":
				return $expr;
			default:
				throw new Exception("No such operator '$expr'");
		}
	}

	private static function is_table($table){
		global $db;
		static $tables;
		if(!isset($tables)){
			$db_name = static::get_database_name();
			$stmt = $db->prepare("
				SELECT `table_name`
				FROM `information_schema`.`tables`
				WHERE `table_schema` = ?
			");
			$stmt->bind_param('s', $db_name);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($table_);
			while($stmt->fetch()){
				$tables[] = strtolower($table_);
			}
			$stmt->close();
		}
		return in_array(strtolower($table), $tables);
	}

	private static function fix_join($path, &$joins, $parent_columns, $parent){
		$first = array_shift($path);
		if(class_exists($first) && is_subclass_of($first, 'BasicObject')){
			$first = $first::table_name();
		}
		$parent_id = self::id_name($parent);
		$first_id = self::id_name($first);
		if(!self::is_table($first)){
			throw new Exception("No such table '$first'");
		}
		$connection = self::connection($first, $parent);
		$columns = self::columns($first);
		if($connection){
			$joins[$first] = array(
				'to' => $parent,
				'on' => "`{$connection['TABLE_NAME']}`.`{$connection['COLUMN_NAME']}` = `{$connection['REFERENCED_TABLE_NAME']}`.`{$connection['REFERENCED_COLUMN_NAME']}`"
			);
		} else {
			if(in_array($first_id, $parent_columns)){
				$joins[$first] = array(
					"to" => $parent, 
					"on" => "`$parent`.`$first_id` = `$first`.`$first_id`");
			} elseif(in_array($parent_id, $columns)) {
				$joins[$first] = array(
					"to" => $parent,
					"on" => "`$parent`.`$parent_id` = `$first`.`$parent_id`");
			} else {
				throw new Exception("No connection from '$parent' to table '$first'");
			}
		}
		if(count($path) == 1) {
			$key = array_shift($path);
			if(!in_array($key, $columns)){
				throw new Exception("No such column '$key' in table '$first'");
			} 
			return $first.'`.`'.$key;
		} else {
			return self::fix_join($path, $joins, $columns, $first);
		}
	}

	private static function in_table($column, $table){
		static $tables = array();
		if(!isset($tables[$table])){
			$tables[$table] = self::columns($table);
		}
		return in_array(strtolower($column), $tables[$table]);
	}

	private static function connection($table1, $table2) {
		global $db;
		static $data;
		if(strcmp($table1, $table2) < 0){
			$tmp = $table1;
			$table1 = $table2;
			$table2 = $tmp;
		}
		if(!isset($data[$table1]) || !isset($data[$table1][$table2])){
			$data[$table1][$table2] = array();
			$stmt = $db->prepare("
				SELECT
					`key_column_usage`.`TABLE_NAME`,
					`COLUMN_NAME`,
					`REFERENCED_TABLE_NAME`,
					`REFERENCED_COLUMN_NAME`
				FROM 
					`information_schema`.`table_constraints` join
					`information_schema`.`key_column_usage` using (`CONSTRAINT_NAME`, `CONSTRAINT_SCHEMA`)
				WHERE
					`constraint_type` = 'FOREIGN KEY' and
					`table_constraints`.`table_schema` = ? AND
					(
						`key_column_usage`.`TABLE_NAME` = ? AND
						`REFERENCED_TABLE_NAME` = ?
					) OR (
						`key_column_usage`.`TABLE_NAME` = ? AND
						`REFERENCED_TABLE_NAME` = ?
					)");
			$db_name = self::get_database_name();
			$stmt->bind_param('sssss', $db_name, $table1, $table2, $table2, $table1);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result(
				$data[$table1][$table2]['TABLE_NAME'],
				$data[$table1][$table2]['COLUMN_NAME'],
				$data[$table1][$table2]['REFERENCED_TABLE_NAME'],
				$data[$table1][$table2]['REFERENCED_COLUMN_NAME']
			);
			if(!$stmt->fetch()){
				$data[$table1][$table2] = false;
			}
			$stmt->close();
		}
		return $data[$table1][$table2];
	}

	private static function get_database_name() {
		global $db;
		static $db_name = null;
		if($db_name === null) {
			$stmt = $db->prepare("SELECT DATABASE()");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($db_name);
			$stmt->fetch();
			$stmt->close();
		}
		return $db_name;
	}
}
class UndefinedMemberException extends Exception{}
class UndefinedFunctionException extends Exception{}
?>
