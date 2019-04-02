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

class logs extends admin
{
	function execute()
	{
		$this->set_title($this->lang->logs_view);
		$this->tree($this->lang->logs_view);

		$this->lang->main();

		$this->get['min'] = array_key_exists( 'min', $this->get ) ? (int)$this->get['min'] : 0;
		$this->get['num'] = array_key_exists( 'num', $this->get ) ? (int)$this->get['num'] : 60;
		$pages = $this->htmlwidgets->get_pages( 'SELECT l.*, u.user_name FROM %plogs l, %pusers u WHERE u.user_id=l.log_user ORDER BY l.log_time DESC', 'a=logs', $this->get['min'], $this->get['num'] );

		$data = $this->db->query('SELECT l.*, u.user_name FROM %plogs l, %pusers u WHERE u.user_id=l.log_user ORDER BY l.log_time DESC LIMIT %d, %d',
		$this->get['min'], $this->get['num']);

		$this->iterator_init('tablelight', 'tabledark');

		$out = null;
		while ($log = $this->db->nqfetch($data))
		{
			$class = $this->iterate();
			$date = $this->mbdate(DATE_LONG, $log['log_time']);
			$user = $log['user_name'];
			$action = '';
			$id = '';

			switch ($log['log_action'])
			{
			case 'spam_delete':
				$action = $this->lang->logs_reported_spam;
				$id = $this->lang->logs_post . " #" . $log['log_data1'];
				break;

			case 'post_delete':
				$action = $this->lang->logs_deleted_post;
				$id = $this->lang->logs_post . " #" . $log['log_data1'];
				break;

			case 'post_edit':
				$action = $this->lang->logs_edited_post;
				$id = $this->lang->logs_post . " #" . $log['log_data1'];
				break;

			case 'topic_publish':
				$action = $this->lang->logs_published_topic;
				$id = $this->lang->logs_topic . ' #' . $log['log_data1'];
				break;

			case 'topic_unpublish':
				$action = $this->lang->logs_unpublished_topic;
				$id = $this->lang->logs_topic . ' #' . $log['log_data1'];
				break;

			case 'topic_lock':
				$action = $this->lang->logs_locked_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			case 'topic_unlock':
				$action = $this->lang->logs_unlocked_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			case 'topic_move':
				$action = $this->lang->logs_moved_topic;
				$id = $this->lang->logs_moved_topic_num . $log['log_data1'] . " " . $this->lang->logs_moved_from . " #" . $log['log_data2'] . " " . $this->lang->logs_moved_to . " #" . $log['log_data3'];
				break;

			case 'topic_edit':
				$action = $this->lang->logs_edited_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			case 'topic_pin':
				$action = $this->lang->logs_pinned_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			case 'topic_unpin':
				$action = $this->lang->logs_unpinned_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			case 'topic_delete':
				$action = $this->lang->logs_deleted_topic;
				$id = $this->lang->logs_topic . " #" . $log['log_data1'];
				break;

			default:
				$action = $log['log_action'];
				$id = $log['log_data1'];
			}

			$out .= eval($this->template('ADMIN_MOD_LOGS_ENTRY'));
		}
		return eval($this->template('ADMIN_MOD_LOGS'));
	}
}
?>