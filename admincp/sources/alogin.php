<?php
/**
 * Quicksilver Forums
 * Copyright (c) 2005-2011 The Quicksilver Forums Development Team
 *  https://github.com/Arthmoor/Quicksilver-Forums
 * 
 * Based on MercuryBoard
 * Copyright (c) 2001-2005 The Mercury Development Team
 *  https://github.com/markelliot/MercuryBoard
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 **/

if (!defined('QUICKSILVERFORUMS') || !defined('QSF_ADMIN')) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

require_once $set['include_path'] . '/admincp/admin.php';

class alogin extends admin
{
	function execute()
	{
		$this->set_title($this->lang->login_header);
		$this->tree($this->lang->login_header);

		if (!isset($this->post['submit']))
		{
			return eval($this->template('ADMIN_LOGIN'));
		}

		$data  = $this->db->fetch('SELECT user_password FROM %pusers WHERE user_id = %d LIMIT 1',
				$this->user['user_id'] );
		$pass  = $data['user_password'];

		$this->post['pass'] = str_replace('$', '', $this->post['pass']);
		$this->post['pass'] = md5($this->post['pass']);

		if ($this->post['pass'] == $pass)
		{
			$_SESSION['admincp_timeout'] = time();
			return $this->message($this->lang->login_header, $this->lang->login_logged, $this->lang->continue, $this->self, $this->self);
		} else {
			return $this->message($this->lang->login_header, sprintf($this->lang->login_cant_logged, $this->self));
		}
	}
}
?>