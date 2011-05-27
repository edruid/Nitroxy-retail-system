<?php

class Log {
	private $log_name;

	public function __construct($log_name) {
		$this->log_name=$log_name;
	}
		
	public function insert_event($type,$info,$user_id=NULL) {
		/**
		* Writes a new event to the specified log.
		* @param int $user_id If set to NULL, the current user will be used.
		* @return int insert id
		* @todo ALLT
		*/
		global $u_id;
		global $db;
		if(is_null($user_id)) {
			$user_id=$u_id;
		}

		/*$query="INSERT INTO ".$this->log_name." SET user_id=?, type=?, info=?";
		$stmt=$db->prepare($query, "iss", );
		$stmt->bind_param('iss',$user_id,$type,$info);
		if(!$stmt->execute()) {
			die("Query failed");
		}*/
	}

	public function select_event($log_id) {
		/**
		* selects one row from the table.
		* @param log_id
		* @return array of fields, or NULL if log_id does not exist
		*/
		global $db;
		$query="select timestamp, user_id, type, info from ".$this->log_name." WHERE log_id=?";
		$stmt=$db->prepare($query);
		$stmt->bind_param('i',$log_id);
		if(!$stmt->execute()) {
			die("Query failed");
		}
		$stmt->store_result();
		if($stmt->num_rows>0) {
			$stmt->bind_result($timestamp,$user_id,$type,$info);
			$stmt->fetch();
			$event=array(
				'timestamp' => $timestamp,
				'user_id' => $user_id,
				'type' => $type,
				'info' => $info
				);
		} else {
			$event=NULL;
		}
		$stmt->close();
		return $event;
	}
}

?>
