<?php

/**
 * 
 * ConnectMySQL
 * ver : 1.0
 *
*/

class ConnectMySQL{

	public $id = null;

	function __construct(){

		//環境設定
		$db_name = 'sql_test';
		$host = '127.0.0.1';
		$user_name = "root";
		$password = "root";

		//DSN socket,portは無しでも
		$dsn = "mysql:dbname={$db_name};";
		$dsn .= "host={$host};";
		$dsn .= 'unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;';
		$dsn .= 'port=8889';

		try {
			$this->pdo = new PDO($dsn,$user_name,$password,array(PDO::ATTR_EMULATE_PREPARES => false));
		} catch (PDOException $e) {
			exit('データベース接続失敗。'.$e->getMessage());
		}
	}

	/**
	 * 全件表示
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
	 * 追加、編集、検索共通処理
	*/
	public function Common($table = null, $action = null, $post_data = array()){

		//Field設定
		$field = array();
		foreach ($post_data as $k => $v) {
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
					$edit_filed .= $filed_v . ' =:' . $filed_v;
				}else{
					$edit_filed .= ',' . $filed_v . ' =:' . $filed_v;
				}
				
			}

			$sth = $this->pdo->prepare("UPDATE {$table} SET {$edit_filed} WHERE id = :id");
			$sth->execute(array(':id' => $this->id));

		}elseif($action == "find"){

			//sql用に成型
			$search_filed = null;
			foreach ($field as $filed_v) {
				//最初
				if($filed_v === reset($field)){
					$search_filed .= $filed_v . ' =:' . $filed_v;
				}else{
					$search_filed .= ' AND' . $filed_v . ' =:' . $filed_v;
				}
				
			}

			$sql = "SELECT * FROM {$table} WHERE {$search_filed} ORDER BY id DESC";
			$sth = $this->pdo->prepare($sql);

		}


		if($action == "find"){

			/*
			* find
			*/

			foreach ($post_data as $key => $value) {
				$sth->execute(array(':' . $key => $post_data[$key]));
			}

			return $sth->fetchAll();

		}else{

			/*
			* add,edit
			*/

			//data set
			if(!empty($post_data)){
				foreach ($post_data as $key => $value) {
					if(is_int($value)){
						$sth->bindParam(':' . $key, $post_data[$key], PDO::PARAM_INT);
					}else{
						$sth->bindParam(':' . $key, $post_data[$key], PDO::PARAM_STR);
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
	*/
	public function add($table = null, $post_data = array()){

		if(!empty($table)){
			if($this->Common($table,__FUNCTION__,$post_data)){
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
	*/
	public function edit($table = null, $post_data = array()){

		if(!empty($table) && !empty($this->id)){
			if($this->Common($table,__FUNCTION__,$post_data)){
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
	*/
	public function find($table = null, $post_data = array()){

		$data = array();

		if(!empty($table)){
			$data = $this->Common($table,__FUNCTION__,$post_data);
		}

		return $data;
	}

}
?>