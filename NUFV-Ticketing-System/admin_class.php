<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   		include 'db_connect.php';
    	$this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function generateRandomTicketID($length = 12) {
		return strtoupper(bin2hex(random_bytes($length / 2)));
	}

	function login(){
		extract($_POST);
		
		$qry = $this->db->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM users WHERE email = '".$email."' AND password = '".md5($password)."'");
	
		if($qry->num_rows > 0){
			$user_data = $qry->fetch_array();
			foreach ($user_data as $key => $value) {
				if($key != 'password' && !is_numeric($key)) {
					$_SESSION['login_'.$key] = $value;
				}
			}
			
		
			$_SESSION['login_type'] = $user_data['role'];
			
			return 1;
		}
		
		return 3;
	}	

	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user(){
		extract($_POST);
	
		$data = "id = '$id_number', firstname = '$firstname', middlename = '$middlename', lastname = '$lastname', role = '$role', email = '$email'";
		if (!empty($password)) {
			$data .= ", password = '" . md5($password) . "'";
		}
	
		$check_email = $this->db->query("SELECT * FROM users WHERE email = '$email' AND id != '$id_number'");
		if ($check_email->num_rows > 0) {
			return 2;
		}
	
		$check_id = $this->db->query("SELECT * FROM users WHERE id = '$id_number' AND id != '$id'");
		if ($check_id->num_rows > 0) {
			return 3;
		}
	
		$check_name = $this->db->query("SELECT * FROM users WHERE firstname = '$firstname' AND middlename = '$middlename' AND lastname = '$lastname' AND id != '$id_number'");
		if ($check_name->num_rows > 0) {
			return 4;
		}
	
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET $data");
		} else {
			$save = $this->db->query("UPDATE users SET $data WHERE id = '$id_number'");
		}
	
		return $save ? 1 : 0;
	}			

	function delete_user(){
		extract($_POST);
		$id = $this->db->real_escape_string($id);
		$delete = $this->db->query("DELETE FROM users WHERE id = '$id'");
		return $delete ? 1 : 0;
	}	
	
	function save_page_img(){
		extract($_POST);
		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			if($move){
				$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
				$hostName = $_SERVER['HTTP_HOST'];
				$path = explode('/',$_SERVER['PHP_SELF']);
				$currentPath = '/'.$path[1]; 
				return json_encode(array('link'=>$protocol.'://'.$hostName.$currentPath.'/admin/assets/uploads/'.$fname));
			}
		}
	}

	function save_ticket() {
		extract($_POST);
	
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if ($k == 'description' || $k == 'room_id') {
					$v = strip_tags($v);
					$v = html_entity_decode($v);
					$v = trim($v);
	
					if ($k == 'description' && empty($v)) {
						return 0;
					}
				}
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		if ($_SESSION['login_type'] == 3) {
			$data .= ", user_id='{$_SESSION['login_id']}' ";
		} else {
			return 0;
		}
	
		$random_ticket_id = $this->generateRandomTicketID();

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO tickets SET id='$random_ticket_id', $data");
		} else {
			$save = $this->db->query("UPDATE tickets SET $data WHERE id = '$id'");
		}
	
		if ($save) {

			$message = "A new ticket has been created.";
	
			$this->db->query("INSERT INTO notifications (user_id, message, is_read, created_at, ticket_id) VALUES (NULL, '$message', 0, NOW(), '$random_ticket_id')");
		}
	
		return $save ? 1 : 0;
	}		
	
	function update_ticket() {
		extract($_POST);
	
		if ($status == 2) {
			$stmt = $this->db->prepare("SELECT * FROM comments WHERE ticket_id = ? AND LOWER(comment) = 'done' AND user_type IN (1, 2)");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
	
			if ($result->num_rows == 0) {
				return 0;
			}
		}
	
		$data = "status = ?, priority = ?";
		if ($_SESSION['login_type'] == 2) {
			$data .= ", staff_id = ?";
		}
	
		$stmt = $this->db->prepare("UPDATE tickets SET $data WHERE id = ?");
		
		if ($_SESSION['login_type'] == 2) {
			$stmt->bind_param("iiii", $status, $priority, $_SESSION['login_id'], $id);
		} else {
			$stmt->bind_param("iii", $status, $priority, $id);
		}
	
		$stmt->execute();
		return 1;
	}

	function delete_ticket(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM tickets where id = ".$id);
		if($delete){
			return 1;
		}
	}

	function save_comment(){
		extract($_POST);
		$data = "";
	
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'comment'){
					$v = strip_tags($v);
					$v = trim($v);
				}
				if(empty($data)){
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
		
		if (isset($_SESSION['login_type']) && isset($_SESSION['login_id'])) {
			$data .= ", user_type={$_SESSION['login_type']} ";
			$data .= ", user_id='" . $this->db->real_escape_string($_SESSION['login_id']) . "' ";
		} else {
			return 0;
		}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO comments SET $data");
		} else {
			$save = $this->db->query("UPDATE comments SET $data WHERE id = $id");
		}
	
		if($save)
			return 1;
		return 0;
	}	

	function delete_comment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM comments where id = ".$id);
		if($delete){
			return 1;
		}
	}

	function save_category(){
		extract($_POST);
		$data = " name='$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO categories SET $data");
		}else{
			$save = $this->db->query("UPDATE categories SET $data WHERE id = $id");
		}
	
		if($save)
			return 1;
	}

	function save_room(){
		extract($_POST);
		$data = " name='$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO rooms SET $data");
		}else{
			$save = $this->db->query("UPDATE rooms SET $data WHERE id = $id");
		}
	
		if($save)
			return 1;
	}
	function check_done_comment($ticket_id) {
		$stmt = $this->db->prepare("SELECT * FROM comments WHERE ticket_id = ? AND LOWER(comment) = 'done' AND user_type IN (1, 2)");
		$stmt->bind_param("s", $ticket_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows;
	}
	
}
?>