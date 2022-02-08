<?php
class DB {
	private $host;
	private $user;
	private $pass;
	private $db;
	private $port;
	private $socket;

	private $conn=null;

	private $last_query=null;

  private $last_insert=null;

	function __construct($host=null, $user=null, $pass=null, $db=null, $port=null, $socket=null){
		// init with usual stuff
		$this->host = $host;
		if($this->host == null)  	$this->host = ini_get("mysqli.default_host");

		$this->user = $user;
		if($this->user == null) $this->user = ini_get("mysqli.default_user");

    $this->pass = $pass;
		if($this->pass == null) $this->pass = ini_get("mysqli.default_pw");

		$this->db = $db;
		if($this->db == null) $this->db = "";

		$this->port	= $port;
		if($this->port == null) $this->port = ini_get("mysqli.default_port");

		$this->socket = $socket;
		if($this->socket == null) ini_get("mysqli.default_socket");
  }

	function establish_connection(){
		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port, $this->socket);

		if ($this->conn->connect_errno) {
			throw new Exception("Failed to connect to MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);
		}

		return true;
	}

	function close_connection(){
		if($this->conn->ping()){
			$this->conn->close();
			$this->conn = null;
		}

		return true;
	}

	function query($query, $data = array()){
		// if connection is not established, then do so
		if(is_null($this->conn) || !$this->conn->ping()){
			$this->establish_connection();
		}

		$to_replace = substr_count($query, '?');

		if($to_replace != count($data)){
			throw new Exception("Parameter count does not match!<br>".$query."<br>".print_r($data, TRUE));
		}

		foreach($data as $v){
			switch(gettype($v)){
				case 'string':
					$replace = '"'.$this->conn->escape_string($v).'"';
					break;
				default:
				$replace = $v;
					break;
			}
			$query = preg_replace('/\?/', $replace, $query, 1);
		}

		$this->last_query = $query;

		$res = $this->conn->query($query);

		if(is_bool($res)){
			$return = $this->conn->affected_rows;
		}else{
			$return = array();
			while ($row = $res->fetch_assoc()) {
				$return[] = $row;
			}
		}

		return $return;
  }

  function multiInsert($tab, $data){
    if(!is_array($data)) return;
    if(!isset($data[0])) return $this->insert($tab, $data);

    $keys = implode(', ', array_keys($data[0]));
    $values = array();
    foreach($data as $row){
      $rowValues = [];
      foreach($row as $v){
        switch(gettype($v)){
          case 'object':
          case 'array':
            $v = json_encode($v);
          case 'string':
            $rowValues[] = '"'.$this->conn->escape_string($v).'"';
            break;
          case 'NULL':
            $rowValues[] = 'NULL';
            break;
          default:
            $rowValues[] = $v;
            break;
        }
      }
      $rowValues = implode(', ', $rowValues);
      $values[] = $rowValues;
    }
    $values = implode('), (', $values);
    $query = "INSERT INTO ".$tab." (".$keys.") VALUES (".$values.")";

    $this->last_query = $query;

    $res = $this->conn->query($query);

    $affected_rows = $this->conn->affected_rows;

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
      if($tab != 'changelog'){
        $this->insert("changelog", array('userId'=>$_SESSION['userdata']['userId'], 'query'=>$query));
      }
    }

    return $affected_rows;
  }

  function insert($tab, $data){
    $keys = implode(', ', array_keys($data));
    $values = array();
    foreach($data as $v){
			switch(gettype($v)){
        case 'object':
        case 'array':
          $v = json_encode($v);
				case 'string':
					$values[] = '"'.$this->conn->escape_string($v).'"';
          break;
        case 'NULL':
          $values[] = 'NULL';
          break;
				default:
				  $values[] = $v;
					break;
			}
    }
    $values = implode(', ', $values);
    $query = "INSERT INTO ".$tab." (".$keys.") VALUES (".$values.")";

    $this->last_query = $query;

    $res = $this->conn->query($query);

    $affected_rows = $this->conn->affected_rows;

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
      if($tab != 'changelog'){ 
        $this->last_insert = $this->insert_id();
        $this->insert("changelog", array('userId'=>$_SESSION['userdata']['userId'], 'query'=>$query));
      }else{
        $this->last_insert = $this->insert_id();
      }
    }

    return $affected_rows;
  }

  function update($tab, $data, $where=""){
    $query = "UPDATE ".$tab;
    $set_san = $this->sanatizeToString($data);
    $query .= ' SET '.implode(', ', $set_san);

    if(!empty($where)){
      $query .= ' WHERE ';
      if(is_array($where)){
        $where_san = $this->sanatizeToString($where);
        $query .= implode(' AND ', $where_san);
      }else{
        $query .=  $where;
      }
    }

    $this->last_query = $query;

    $res = $this->conn->query($query);

    $affected_rows = $this->conn->affected_rows;

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $affected_rows > 0){
      $this->insert("changelog", array('userId'=>$_SESSION['userdata']['userId'], 'query'=>$query));
    }

    return $affected_rows;
  }

  function delete($tab, $where=""){
    $query = "DELETE FROM ".$tab;
    if(!empty($where)){
      $query .= ' WHERE ';
      if(is_array($where)){
        $where_san = $this->sanatizeToString($where);
        $query .= implode(' AND ', $where_san);
      }else{
        $query .= $where;
      }
    }

    $this->last_query = $query;

    $res = $this->conn->query($query);

    $affected_rows = $this->conn->affected_rows;

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $affected_rows > 0){
      $this->insert("changelog", array('userId'=>$_SESSION['userdata']['userId'], 'query'=>$query));
    }

    return $affected_rows;
  }

  function select($tab, $cols='*', $where="", $order_by=""){
    $query = "SELECT ";
    if(is_array($cols)){
      $query .= implode($cols);
    }else{
      $query .= $cols;
    }
    $query .= " FROM ".$tab;
    if(!empty($where)){
      $query .= " WHERE ";
      if(is_array($where)){
        $where_san = $this->sanatizeToString($where);
        $query .= implode(' AND ', $where_san);
      }else{
        $query .= $where;
      }
    }
    if(!empty($order_by)){
      $query .= " ORDER BY ";
      if(is_array($order_by)){
        $query .= implode(', ', $order_by);
      }else{
        $query .= $order_by;
      }
    }

    $this->last_query = $query;

    $res = $this->conn->query($query);

    $return = array();
    if(!is_bool($res)){
      while ($row = $res->fetch_assoc()) {
        $return[] = $row;
      }
    }

    return $return;
  }

  private function sanatizeToString($arr, $concat = '=', $equalToKey = true){
    $ret = array();
    foreach($arr as $k=>$v){
      if($equalToKey){
        $curr = $k.$concat;
      }else{
        $curr = '';
      }
      switch(gettype($v)){
        case 'object':
        case 'array':
          $v = json_encode($v);
				case 'string':
					$curr .= '"'.$this->conn->escape_string($v).'"';
          break;
        case 'NULL':
          $curr .= 'NULL';
          break;
				default:
				  $curr .= $v;
					break;
      }
      $ret[] = $curr;
    }
    return $ret;
  }

	function last_query(){
		return $this->last_query;
	}

  function insert_id(){
    return $this->last_insert ?? $this->conn->insert_id;
  }
}
?>
