<?php

/**
 * 
 * ConnectMySQL
 * ver : 1.1
 *
*/

class ConnectMySQL {

	public $id = null;

	private $operators = array('>', '<', '>=', '<=');

	function __construct(){

		$host = getenv('DB_HOST');
		$db_name = getenv('DB_NAME');
		$user_name = getenv('DB_USER');
		$password = getenv('DB_PASS');
		$location = getenv('LOCATION');

		//DSN socket,portは無しでも
		$dsn = "mysql:dbname={$db_name};";
		$dsn .= "host={$host};";

		if ($location == 'local') {
			$dsn .= 'unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;';
			$dsn .= 'port=8889';
		}

		try {
			$this->pdo = new PDO($dsn,$user_name,$password,array(PDO::ATTR_EMULATE_PREPARES => false));
		} catch (PDOException $e) {
			exit('Error：'.$e->getMessage());
		}
	}

/**
 * 全件表示
 *
 * @param string $table
 * @param order $table
*/
	public function all($table = null, $order = "id DESC"){

		$data = array();

		if(!empty($table)){

			$sql = "SELECT * FROM {$table} ORDER BY {$order}";

			$c = 0;
			foreach ($this->pdo->query($sql) as $key => $value) {
				$data[$c][$key] = $value;
				$c++;
			}
		}

		return $data;
	}

/**
 * 詳細表示
 *
 * @param string $table
*/
	public function view($table = null){

		$data = array();

		if(!empty($table) && !empty($this->id)){

			$sql = "SELECT * FROM {$table} WHERE id = :id";
			$sth = $this->pdo->prepare($sql);
			$sth->execute(array(':id' => $this->id));
			$data = $sth->fetch();

		}

		return $data;

	}

/**
 * remove_operator
 *　演算子除去
 *
 * @param string $field
 * @return string
 */
	public function remove_operator($field = null) {

		if (!empty($field)) {
			if (strrpos($field, ' ') !== false) {
				$field_ar = explode(' ', $field);
				$field = $field_ar[0];
			}
		}

		return $field;
	}


/**
 * 追加、編集、検索共通処理
 *
 * @param string $table
 * @param string $action
 * @param array $values
 * @param string $order
*/
	public function Common($table = null, $action = null, $values = array(), $order = "id DESC"){

		//Field設定
		$field = array();
		foreach ($values as $k => $v) {
			$field[] = $k;
		}

		//sql setteing
		if($action == "add"){

			//sql用に成型
			$insert_field = implode(", ", $field);
			$value_field = implode(", :", $field);
			$sth = $this->pdo->prepare("INSERT INTO {$table} ({$insert_field}) VALUES (:{$value_field})");

		}elseif($action == "edit"){

			//sql用に成型
			$edit_filed = null;
			foreach ($field as $filed_v) {
				//最初
				if($filed_v === reset($field)){
					$edit_filed .= $filed_v . ' = :' . $filed_v;
				}else{
					$edit_filed .= ',' . $filed_v . ' = :' . $filed_v;
				}
				
			}

			$sth = $this->pdo->prepare("UPDATE {$table} SET {$edit_filed} WHERE id = :id");
			$sth->execute(array(':id' => $this->id));

		}elseif($action == "find"){

			//sql用に成型
			$search_filed = null;
			foreach ($field as $key => $filed_v) {

				//演算子
				$operator = '=';
				if (strrpos($filed_v, ' ') !== false) {
					$field_ar = explode(' ', $filed_v);
					if (in_array($field_ar[1], $this->operators)) {
						$operator = $field_ar[1];
					}
				}

				//演算子除去
				$filed_v = self::remove_operator($filed_v);

				//最初
				if($key == 0){
					$search_filed .= $filed_v . " {$operator} :" . $filed_v;
				}else{
					$search_filed .= ' AND ' . $filed_v . " {$operator} :" . $filed_v;
				}
				
			}

			$sql = "SELECT * FROM {$table} WHERE {$search_filed} ORDER BY " . $order;

			$sth = $this->pdo->prepare($sql);

		}


		if($action == "find"){

			/*
			* find
			*/
			$ex_data = array();
			foreach ($values as $key => $value) {
				$key = self::remove_operator($key);
				$sth->bindValue(':' . $key, $value);
			}

			$sth->execute();

			return $sth->fetchAll();

		}else{

			/*
			* add,edit
			*/

			//data set
			if(!empty($values)){
				foreach ($values as $key => $value) {
					if(is_int($value)){
						$sth->bindParam(':' . $key, $values[$key], PDO::PARAM_INT);
					}else{
						$sth->bindParam(':' . $key, $values[$key], PDO::PARAM_STR);
					}
				}
			}

			if(!empty($sth) && $sth->execute()){
				return true;
			}else{
				return false;
			}
		}


	}

/**
 * 追加
 *
 * @param string $table
 * @param array $values
*/
	public function add($table = null, $values = array()){

		if(!empty($table)){
			if($this->Common($table,__FUNCTION__,$values)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}

	}

/**
 * 編集
 *
 * @param string $table
 * @param array $values
*/
	public function edit($table = null, $values = array()){

		if(!empty($table) && !empty($this->id)){
			if($this->Common($table,__FUNCTION__,$values)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}

	}

/**
 * 削除
 *
 * @param string $table
*/
	public function delete($table = null){

		if(!empty($table)){

			//削除データ確認
			$view_sql = "SELECT * FROM {$table} WHERE id = :id";
			$view_sth = $this->pdo->prepare($view_sql);
			$view_sth->execute(array(':id' => $this->id));
			$data = $view_sth->fetch();

			if(empty($data)){
				return false;
			}

			//削除処理
			$delete_sql = "DELETE FROM {$table} WHERE id = :id";
			$delete_sth = $this->pdo->prepare($delete_sql);
			$delete_sth->bindValue(':id', $this->id, PDO::PARAM_INT);

			if($delete_sth->execute()){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}

	}

/**
 * 検索
 *
 * @param string $table
 * @param array $values
 * @param string $order
*/
	public function find($table = null, $values = array(), $order = "id DESC"){

		$data = array();

		if(!empty($table)){
			$data = $this->Common($table ,__FUNCTION__, $values, $order);
		}

		return $data;
	}

/**
 * queryFind
 * クエリ検索
 *
 * @param string $query
 * @return array
 */
	public function queryFind($query = null){

		if (empty($query)) {
			return array();
		}

		$sth = $this->pdo->prepare($query);
		$sth->execute();

		return $sth->fetchAll();

	}
}
?>