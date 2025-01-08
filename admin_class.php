<?php
session_start();
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function login()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users WHERE username = '" . $username . "' AND password = '" . $password . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 2;
		}
	}
	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_folder()
	{
		extract($_POST);
		$data = " name ='" . $name . "' ";
		$data .= ", parent_id ='" . $parent_id . "' ";
		if (empty($id)) {
			$data .= ", user_id ='" . $_SESSION['login_id'] . "' ";

			$check = $this->db->query("SELECT * FROM folders WHERE user_id ='" . $_SESSION['login_id'] . "' AND name  ='" . $name . "' AND parent_id ='" . $parent_id . "'")->num_rows;
			if ($check > 0) {
				return json_encode(array('status' => 2, 'msg' => 'Le nom du dossier existe déjà'));
			} else {
				$save = $this->db->query("INSERT INTO folders SET " . $data);
				if ($save)
					return json_encode(array('status' => 1));
			}
		} else {
			$check = $this->db->query("SELECT * FROM folders WHERE user_id ='" . $_SESSION['login_id'] . "' AND name  ='" . $name . "' AND id !=" . $id)->num_rows;
			if ($check > 0) {
				return json_encode(array('status' => 2, 'msg' => 'Le nom du dossier existe déjà'));
			} else {
				$save = $this->db->query("UPDATE folders SET " . $data . " WHERE id =" . $id);
				if ($save)
					return json_encode(array('status' => 1));
			}
		}
	}

	function delete_folder()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM folders WHERE id =" . $id);
		if ($delete)
			echo 1;
	}
	function delete_file()
	{
		extract($_POST);
		$path = $this->db->query("SELECT file_path FROM files WHERE id=" . $id)->fetch_array()['file_path'];
		$delete = $this->db->query("DELETE FROM files WHERE id =" . $id);
		if ($delete) {
			unlink('assets/uploads/' . $path);
			return 1;
		}
	}

	function save_files()
	{
		extract($_POST);
		if (empty($id)) {
			if ($_FILES['upload']['tmp_name'] != '') {
				$fname = uniqid(date('YmdHis') . '_') . $_FILES['upload']['name'];
				$move = move_uploaded_file($_FILES['upload']['tmp_name'], 'assets/uploads/' . $fname);
				if ($move) {
					$file = $_FILES['upload']['name'];
					$file = explode('.', $file);
					$chk = $this->db->query("SELECT * FROM files WHERE SUBSTRING_INDEX(name,' ||',1) = '" . $file[0] . "' AND folder_id = '" . $folder_id . "' AND file_type='" . $file[1] . "' ");
					if ($chk->num_rows > 0) {
						$file[0] = $file[0] . ' ||' . ($chk->num_rows);
					}
					$data = " name = '" . $file[0] . "' ";
					$data .= ", folder_id = '" . $folder_id . "' ";
					$data .= ", description = '" . $description . "' ";
					$data .= ", user_id = '" . $_SESSION['login_id'] . "' ";
					$data .= ", file_type = '" . $file[1] . "' ";
					$data .= ", file_path = '" . $fname . "' ";
					if (isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
					else
						$data .= ", is_public = 0 ";

					$save = $this->db->query("INSERT INTO files SET " . $data);
					if ($save)
						return json_encode(array('status' => 1));
				}
			}
		} else {
			$data = " description = '" . $description . "' ";
			if (isset($is_public) && $is_public == 'on')
				$data .= ", is_public = 1 ";
			else
				$data .= ", is_public = 0 ";
			$save = $this->db->query("UPDATE files SET " . $data . " WHERE id=" . $id);
			if ($save)
				return json_encode(array('status' => 1));
		}
	}
	function file_rename()
	{
		extract($_POST);
		$file[0] = $name;
		$file[1] = $type;
		$chk = $this->db->query("SELECT * FROM files WHERE SUBSTRING_INDEX(name,' ||',1) = '" . $file[0] . "' AND folder_id = '" . $folder_id . "' AND file_type='" . $file[1] . "' AND id != " . $id);
		if ($chk->num_rows > 0) {
			$file[0] = $file[0] . ' ||' . ($chk->num_rows);
		}
		$save = $this->db->query("UPDATE files SET name = '" . $name . "' WHERE id=" . $id);
		if ($save) {
			return json_encode(array('status' => 1, 'new_name' => $file[0] . '.' . $file[1]));
		}
	}
	function save_user()
	{
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET " . $data);
		} else {
			$save = $this->db->query("UPDATE users SET " . $data . " WHERE id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
}
