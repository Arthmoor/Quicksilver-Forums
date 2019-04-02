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

if (!defined('QUICKSILVERFORUMS')) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

require_once $set['include_path'] . '/global.php';

/**
 * Controls moderator actions
 *
 * @author Jason Warner <jason@mercuryboard.com>
 * @since Beta 2.0
 **/
class mod extends qsfglobal
{
	/**
	 * Choose a moderating action
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 4.0
	 * @return string HTML Output
	 **/
	function execute()
	{
		if (!$this->perms->auth('board_view')) {
			$this->lang->board();
			return $this->message(
				sprintf($this->lang->board_message, $this->sets['forum_name']),
				($this->perms->is_guest) ? sprintf($this->lang->board_regfirst, $this->self) : $this->lang->board_noview
			);
		}

		$this->set_title($this->lang->mod_label_controls);

		if (!isset($this->get['s'])) {
			$this->get['s'] = null;
		}

		switch($this->get['s'])
		{
		case 'del_topic':
			return $this->del_topic();
			break;

		case 'del_post':
			return $this->del_post();
			break;

		case 'lock':
			return $this->lock_topic();
			break;

		case 'pin':
			return $this->pin_topic();
			break;

		case 'edit_topic':
			return $this->edit_topic();
			break;

		case 'edit_post':
			return $this->edit_post();
			break;

		case 'move':
			return $this->move_topic();
			break;

		case 'split':
			return $this->split_topic();
			break;
			
		case 'publish':
			return $this->publish_topic();
			break;

		default:
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_action, $this->lang->continue, "javascript:history.go(-1)");
			break;
		}
	}

	/**
	 * Moves a topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function move_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_move);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_title, topic_forum, topic_starter, topic_modes, topic_poll_options
			FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!isset($topic['topic_title'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		if ($topic['topic_modes'] & TOPIC_GLOBAL) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_move_global, $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!$this->perms->auth('topic_move', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_move_own', $topic['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_move, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
		}

		if (!isset($this->post['submit'])) {
			$forumlist = $this->htmlwidgets->select_forums($topic['topic_forum']);
			return eval($this->template('MOD_MOVE_TOPIC'));
		} else {
			$this->post['newforum'] = intval($this->post['newforum']);

			if ($this->post['newforum'] == $topic['topic_forum']) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_move_same, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			}

			if (!$this->perms->auth('topic_create', $this->post['newforum'])) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_move_create, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			}

			$target = $this->db->fetch('SELECT forum_parent FROM %pforums WHERE forum_id=%d', $this->post['newforum']);
			if (!isset($target['forum_parent'])) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_move_forum, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			} elseif (!$target['forum_parent']) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_move_category, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			}

			if ($this->post['operation'] == 'lock') {
				$this->db->clone_row('topics', 'topic_id', $this->get['t']);
				$newtopic = $this->db->insert_id('topics');

				$this->db->query('UPDATE %ptopics SET topic_modes=%d, topic_moved=%d WHERE topic_id=%d OR topic_moved=%d',
					$topic['topic_modes'] | TOPIC_MOVED, $newtopic, $this->get['t'], $this->get['t']);
			} else {
				$newtopic = $this->get['t'];
			}

			$this->db->query("UPDATE %ptopics SET topic_forum=%d WHERE topic_id=%d", $this->post['newforum'], $newtopic);
			$this->db->query("UPDATE %pposts SET post_topic=%d WHERE post_topic=%d", $newtopic, $this->get['t']);
			$this->db->query("UPDATE %pvotes SET vote_topic=%d WHERE vote_topic=%d", $newtopic, $this->get['t']);

			$this->update_subscriptions( $newtopic );
			$this->update_last_post($topic['topic_forum']);
			$this->update_last_post($this->post['newforum']);

			$ammount = $this->db->fetch("SELECT topic_replies FROM %ptopics WHERE topic_id=%d", $newtopic);
			$ammount = intval($ammount['topic_replies']);

			$this->update_count_move($topic['topic_forum'], $this->post['newforum'], $ammount);

			$this->log_action('topic_move', $this->get['t'], $topic['topic_forum'], $this->post['newforum']);

			return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_topic_move, $this->lang->continue, "{$this->self}?a=topic&amp;t={$newtopic}", "$this->self?a=topic&t={$newtopic}");
		}
	}

	/**
	 * Edits a post
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function edit_post()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_post_edit);

		// Parameters check
		if (!isset($this->get['p'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_post, $this->lang->continue, "javascript:history.go(-1)");
		}

		$p = intval($this->get['p']);
		$data = $this->db->fetch("SELECT p.post_text, p.post_author, p.post_emoticons, p.post_mbcode, p.post_topic, p.post_icon, p.post_time, t.topic_title, t.topic_forum, t.topic_replies,
				u.*, m.membertitle_icon, g.group_name
			FROM (%pposts p, %ptopics t)
			LEFT JOIN %pusers u ON u.user_id = p.post_author
			LEFT JOIN %pmembertitles m ON m.membertitle_id = u.user_level
			LEFT JOIN %pgroups g ON g.group_id = u.user_group
 			WHERE t.topic_id=p.post_topic AND p.post_id=%d", $p);

		// Existence check
		if (!isset($data['post_text'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_post, $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $data['post_author'];

		if (!$this->perms->auth('post_edit', $data['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('post_edit_own', $data['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_post_edit, $this->lang->continue, "$this->self?a=topic&amp;t={$data['post_topic']}");
		}

		if (!isset($this->post['submit'])) {
			$emot_check = $data['post_emoticons'] ? ' checked' : null;
			$code_check = $data['post_mbcode'] ? ' checked' : null;
			$attached = null;
			$attached_data = null;
			$upload_error = null;
			$icon = $data['post_icon'] ? $data['post_icon'] : -1;
			$preview = '';

			$this->templater->add_templates('post');
			$this->lang->post();
			
			if (isset($this->post['post'])) {
				$data['post_text'] = $this->post['post'];
			}
			
			// Handle attachment stuff
			if (!isset($this->post['attached_data'])) {
				$this->post['attached_data'] = $this->attachmentutil->build_attached_data($p);
			}

			if ($this->perms->auth('post_attach', $data['topic_forum'])) {
				// Attach
				if (isset($this->post['attach'])) {
					$upload_error = $this->attachmentutil->attach_now($p, $this->files['attach_upload'], $this->post['attached_data']);
				// Detach
				} elseif (isset($this->post['detach'])) {
					$this->attachmentutil->delete_now($p, $this->post['attached'], $this->post['attached_data']);
				}

				$this->attachmentutil->getdata($attached, $attached_data, $this->post['attached_data']);
			}

			/**
			 * Preview
			 */
			if (isset($this->post['preview']) || isset($this->post['attach']) || isset($this->post['detach'])) {
				$quote = $this->format($this->post['post'], FORMAT_HTMLCHARS);

				$params = FORMAT_BREAKS | FORMAT_CENSOR | FORMAT_HTMLCHARS;

				if (isset($this->post['code']) ) {
					$params |= FORMAT_MBCODE;
					$code_check = ' checked=\'checked\'';
				} else {
					$code_check = '';
				}

				if (isset($this->post['emoticons'])) {
					$params |= FORMAT_EMOTICONS;
					$emot_check = ' checked=\'checked\'';
				} else {
					$emot_check = '';
				}

				$preview_text = $this->post['post'];
				$quote = $this->format($preview_text, FORMAT_HTMLCHARS);
				$preview_text = $this->format($preview_text, $params);

				$preview_title = $this->lang->post_preview;

				$this->lang->topic();

				if ($this->perms->is_guest) {
					$signature = '';
					$Poster_Info = eval($this->template('POST_POSTER_GUEST'));
				} else {
					if (($data['user_avatar_type'] != 'none') ) {
						$avatar = "<img src=\"{$data['user_avatar']}\" alt=\"\" width=\"{$data['user_avatar_width']}\" height=\"{$data['user_avatar_height']}\" /><br /><br />";
					} else {
						$avatar = null;
					}

					if ($data['user_signature'] ) {
						$signature = '.........................<br />' . $this->format($data['user_signature'], FORMAT_CENSOR | FORMAT_HTMLCHARS | FORMAT_BREAKS | FORMAT_MBCODE | FORMAT_EMOTICONS);
					} else {
						$signature = null;
					}

					$joined = $this->mbdate(DATE_ONLY_LONG, $data['user_joined']);
 
					$uid = $data['user_id'];
					$uname = $data['user_name'];
					$utitle = $data['user_title'];
					$utitleicon = $data['membertitle_icon'];
					$gname = $data['group_name'];
					$uposts = $data['user_posts'];

					$Poster_Info = eval($this->template('POST_POSTER_MEMBER'));
				}

				if ($this->post['attached_data']) {
					$this->lang->topic();

					$download_perm = $this->perms->auth('post_attach_download', $data['topic_forum']);

					foreach ($this->post['attached_data'] as $md5 => $file)
					{
						if ($download_perm) {
							$ext = strtolower(substr($file, -4));

							if (($ext == '.jpg') || ($ext == '.gif') || ($ext == '.png')) {
								$preview_text .= "<br /><br />{$this->lang->topic_attached} {$file}<br /><img src='./attachments/$md5' alt='{$file}' />";
								continue;
							}
						}

						$preview_text .= "<br /><br />{$this->lang->topic_attached} {$file}";
					}
				}

				$preview = eval($this->template('POST_PREVIEW'));
			}

			$quote     = $this->format($data['post_text'], FORMAT_HTMLCHARS);
			$icon = isset($this->post['icon']) ? $this->post['icon'] : $icon;
			$msg_icons = $this->htmlwidgets->get_icons($icon);
			$msg_icons = "<li><input type=\"radio\" name=\"icon\" value=\"None\" />{$this->lang->none}&nbsp;</li>" . $msg_icons;

			$posticons = eval($this->template('POST_MESSAGE_ICONS'));

			$smilies = $this->bbcode->generate_emote_links();

			if ($this->perms->auth('post_attach', $data['topic_forum'])) {
				if ($attached) {
					$remove_box = eval($this->template('POST_ATTACH_REMOVE'));
				} else {
					$remove_box = '';
				}

				$attach_box = eval($this->template('POST_ATTACH'));
			} else {
				$attach_box = null;
			}

			$post_box  = eval($this->template($this->post_box()));

			$topic_title = $data['topic_title'];

			return eval($this->template('MOD_EDIT_POST'));
		} else {
			$emot = isset($this->post['emoticons']) ? 1 : 0;
			$code = isset($this->post['code']) ? 1 : 0;

			$this->log_action('post_edit', $p);
			$icon = isset($this->post['icon']) ? $this->post['icon'] : '';
			if( $icon == 'None' )
				$icon = '';

			$this->db->query("UPDATE %pposts SET post_text='%s', post_emoticons=%d, post_mbcode=%d, post_edited_by='%s', post_edited_time=%d, post_icon='%s' WHERE post_id=%d",
				$this->post['post'], $emot , $code, $this->user['user_name'], $this->time, $icon, $p);

			$first = $this->db->fetch( "SELECT p.post_id
			 FROM %pposts p, %ptopics t
			 WHERE p.post_topic=t.topic_id AND t.topic_id=%d
			 ORDER BY p.post_time LIMIT 1", $data['post_topic'] );

			if ($first['post_id'] == $p) {
				$this->db->query( "UPDATE %ptopics SET topic_icon='%s' WHERE topic_id='%d'", $icon, $data['post_topic'] );
			}

			$jump = '&amp;p=' . $p . '#p' . $p;

			return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_post_edit, $this->lang->continue, "{$this->self}?a=topic&amp;t={$data['post_topic']}$jump", "$this->self?a=topic&t={$data['post_topic']}$jump");
		}
	}

	/**
	 * Edits a topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function edit_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_edit);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_title, topic_description, topic_starter, topic_forum, topic_modes
			FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!isset($topic['topic_title'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!$this->perms->auth('topic_edit', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_edit_own', $topic['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_edit);
		}

		if (!isset($this->post['submit'])) {
			$topic['topic_title'] = $this->format($topic['topic_title'], FORMAT_HTMLCHARS);
			$topic['topic_description'] = $this->format($topic['topic_description'], FORMAT_HTMLCHARS);

			if ($this->perms->auth('topic_global')) {
				if ($topic['topic_modes'] & TOPIC_GLOBAL) {
					$checkGlob = ' checked=\'checked\'';
				} else {
					$checkGlob = '';
				}

				$global_topic = eval($this->template('MOD_EDIT_GLOBAL'));
			} else {
				$global_topic = '';
			}
 			return eval($this->template('MOD_EDIT_TOPIC'));
		} else {
			if ($this->perms->auth('topic_global')) {
				if (isset($this->post['global_topic']) && !($topic['topic_modes'] & TOPIC_GLOBAL)) {
					$topic['topic_modes'] |= TOPIC_GLOBAL;
				} elseif (!isset($this->post['global_topic']) && ($topic['topic_modes'] & TOPIC_GLOBAL)) {
					$topic['topic_modes'] ^= TOPIC_GLOBAL;
				}
			}

			$this->db->query("UPDATE %ptopics SET topic_title='%s', topic_description='%s', topic_modes='%s' WHERE topic_id=%d",
				$this->post['title'], $this->post['desc'], $topic['topic_modes'], $this->get['t']);

			$this->log_action('topic_edit', $this->get['t']);

			return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_topic_edit, $this->lang->continue, "{$this->self}?a=topic&amp;t={$this->get['t']}", "$this->self?a=topic&t={$this->get['t']}");
		}
	}

	/**
	 * Pins a topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function pin_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_pin);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_modes, topic_starter, topic_forum FROM %ptopics	WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!$topic) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!($topic['topic_modes'] & TOPIC_PINNED)) {
			if (!$this->perms->auth('topic_pin', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_pin_own', $topic['topic_forum'])))) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_pin, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			} else {
				$this->log_action('topic_pin', $this->get['t']);
				$this->pin($this->get['t'], $topic['topic_modes']);
			}
		} else {
			if (!$this->perms->auth('topic_unpin', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_unpin_own', $topic['topic_forum'])))) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_unpin, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			} else {
				$this->log_action('topic_unpin', $this->get['t']);
				$this->unpin($this->get['t'], $topic['topic_modes']);
			}
		}

		header('Location: ' . $this->self . '?a=topic&t=' . $this->get['t']);
	}

	/**
	 * Locks a topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function lock_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_lock);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_modes, topic_starter, topic_forum FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!$topic) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!($topic['topic_modes'] & TOPIC_LOCKED)) {
			if (!$this->perms->auth('topic_lock', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_lock_own', $topic['topic_forum'])))) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_lock, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			} else {
				$this->log_action('topic_lock', $this->get['t']);
				$lock = $this->lock($this->get['t'], $topic['topic_modes']);
			}
		} else {
			if (!$this->perms->auth('topic_unlock', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_unlock_own', $topic['topic_forum'])))) {
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_unlock, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
			} else {
				$this->log_action('topic_unlock', $this->get['t']);
				$lock = $this->unlock($this->get['t'], $topic['topic_modes']);
			}
		}

		header('Location: ' . $this->self . '?a=topic&t=' . $this->get['t']);
	}

	/**
	 * Deletes a post
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function del_post()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_post_delete);

		$spam = false;
		if( isset($this->get['c']) )
			$spam = true;

		// Parameters check
		if (!isset($this->get['p'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_post, $this->lang->continue, "javascript:history.go(-1)");
		}

		$p = intval($this->get['p']);
		$post = $this->db->fetch('SELECT p.post_id, p.post_author, p.post_topic, p.post_time, p.post_text, p.post_ip, p.post_referrer, p.post_agent,
			t.topic_id, t.topic_forum, t.topic_replies
			FROM %pposts p,	%ptopics t
			WHERE p.post_id=%d AND p.post_topic=t.topic_id', $p);

		// Existence check
		if (!isset($post['post_id'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_post, $this->lang->continue, "javascript:history.go(-1)");
		}

		$first = $this->db->fetch('SELECT p.post_id
			FROM %pposts p,	%ptopics t
			WHERE p.post_topic=t.topic_id AND t.topic_id=%d
			ORDER BY p.post_time LIMIT 1', $post['topic_id']);

		if ($first['post_id'] == $p && !$spam) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_error_first_post);
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $post['post_author'];

		if (!$this->perms->auth('post_delete', $post['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('post_delete_own', $post['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_post_delete, $this->lang->continue, "$this->self?a=topic&amp;t={$post['topic_id']}");
		}

		// Confirmation check
		if (!isset($this->get['confirm'])) {
			if( !$spam )
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_confirm_post_delete, $this->lang->continue, "$this->self?a=mod&amp;s=del_post&amp;p=$p&amp;confirm=1");
			else
				return $this->message($this->lang->mod_label_controls, $this->lang->mod_confirm_post_delete_spam, $this->lang->continue, "$this->self?a=mod&amp;s=del_post&amp;p=$p&amp;confirm=1&amp;c=spam");
		}

		$prev = $this->db->fetch("SELECT MAX(p.post_id) AS prev_post FROM %pposts p
			WHERE p.post_topic = %d AND p.post_time < %d",
			$post['post_topic'], $post['post_time']);
		  
		$jump = '&amp;p=' . $prev['prev_post'] . '#p' . $prev['prev_post'];

		if( $spam ) {
			// Time to report the spammer before we delete the post. Hopefully this is enough info to strike back with.
			$user = $this->db->fetch( "SELECT user_name FROM %pusers WHERE user_id=%d", $post['post_author'] );
			require_once $this->sets['include_path'] . '/lib/akismet.php';
			$akismet = new Akismet($this);
			$akismet->set_comment_author($user['user_name']);
			$akismet->set_comment_content($post['post_text']);
			$akismet->set_comment_ip($post['post_ip']);
			$akismet->set_comment_referrer($post['post_referrer']);
			$akismet->set_comment_useragent($post['post_agent']);
			$akismet->set_comment_type('forum-post');

			$akismet->submit_spam();
 
			$this->sets['spam_post_count']++;
			$this->sets['spam_false_count']++;
			$this->write_sets();
		}

		$this->delete_post($p);
		$this->update_last_post($post['topic_forum']);
		$this->update_last_post_topic($post['topic_id']);

		if( $spam ) {
			$this->log_action('spam_delete', $p);

			// Torch topic along with post if it's the only one.
			if ($post['topic_replies'] < 1) {
				$this->delete_topic($post['post_topic']);
			}
		} else {
			$this->log_action('post_delete', $p);
		}

		return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_post_delete, $this->lang->continue, "{$this->self}?a=topic&amp;t={$post['topic_id']}$jump", "$this->self?a=topic&t={$post['topic_id']}$jump");
	}

	/**
	 * Deletes a single topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.0.0
	 * @return string message
	 **/
	function del_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_delete);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_id, topic_forum, topic_starter FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!isset($topic['topic_id'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!$this->perms->auth('topic_delete', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_delete_own', $topic['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_delete, $this->lang->continue, "$this->self?a=topic&amp;t={$topic['topic_id']}");
		}

		// Confirmation check
		if (!isset($this->get['confirm'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_confirm_topic_delete, $this->lang->continue, "$this->self?a=mod&amp;s=del_topic&amp;t={$this->get['t']}&amp;confirm=1");
		}

		$this->delete_topic($this->get['t']);
		$this->update_last_post($topic['topic_forum']);
		$this->log_action('topic_delete', $this->get['t']);

		return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_topic_delete, $this->lang->continue, "{$this->self}?a=forum&amp;f={$topic['topic_forum']}", "$this->self?a=forum&f={$topic['topic_forum']}");
	}

	/**
	 * Publish / UnPublish
	 *
	 * @author Jonathan West <jon@quicksilverforums.com>
	 * @since 1.2.0
	 * @return string message
	 **/
	function publish_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_publish);

		// Parameters check
		if (!isset($this->get['t'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_modes, topic_forum FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!$topic) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic,  $this->lang->continue, "javascript:history.go(-1)");
		}
		// Check permissions
		if (!$this->perms->auth('topic_publish', $topic['topic_forum'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_publish, $this->lang->continue, "$this->self?a=topic&amp;t={$this->get['t']}");
		}
		
		if (!($topic['topic_modes'] & TOPIC_PUBLISH)) {
			$this->log_action('topic_publish', $this->get['t']);
			$this->publish($this->get['t'], $topic['topic_modes']);
		} else {
			$this->log_action('topic_unpublish', $this->get['t']);
			$this->unpublish($this->get['t'], $topic['topic_modes']);
		}

		header('Location: ' . $this->self . '?a=topic&t=' . $this->get['t']);
	}

	/**
	 * Splits a topic
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.1.0
	 * @return string message
	 **/
	function split_topic()
	{
		$this->tree($this->lang->mod_label_controls, $this->self . '?a=mod');
		$this->tree($this->lang->mod_label_topic_split);

		// Parameters check
		if (!isset($this->get['t']) || !isset($this->post['posttarget'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_no_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		$this->get['t'] = intval($this->get['t']);
		$topic = $this->db->fetch('SELECT topic_id, topic_forum, topic_starter, topic_title, topic_modes
			FROM %ptopics WHERE topic_id=%d', $this->get['t']);

		// Existence check
		if (!isset($topic['topic_id'])) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_missing_topic, $this->lang->continue, "javascript:history.go(-1)");
		}

		// Permissions check
		$is_owner = $this->user['user_id'] == $topic['topic_starter'];

		if (!$this->perms->auth('topic_split', $topic['topic_forum']) && (!$is_owner || ($is_owner && !$this->perms->auth('topic_split_own', $topic['topic_forum'])))) {
			return $this->message($this->lang->mod_label_controls, $this->lang->mod_perm_topic_split, $this->lang->continue, "$this->self?a=topic&amp;t={$topic['topic_id']}");
		}

		if (!isset($this->post['submitsplit'])) {
			$posttarget = htmlspecialchars(serialize($this->post['posttarget']));

			$display[1] = in_array('1', $this->post['posttarget']) ? '' : 'display:none';
			$display[2] = in_array('2', $this->post['posttarget']) ? '' : 'display:none';
			$display[3] = in_array('3', $this->post['posttarget']) ? '' : 'display:none';
			$display[4] = in_array('4', $this->post['posttarget']) ? '' : 'display:none';

			$topic['topic_title'] = $this->format($topic['topic_title'], FORMAT_CENSOR | FORMAT_HTMLCHARS);
			return eval($this->template('MOD_SPLIT_TOPIC'));
		} else {
			$posttarget = unserialize($this->post['posttarget']);
			$where = array();
			$moved = 0;

			foreach ($posttarget as $post => $target)
			{
				if ($target) {
					if (!isset($where[$target])) {
						$where[$target] = array('count' => -1, 'posts' => array());
					}

					$where[$target]['count']++;
					$where[$target]['posts'][] = $post;

					$moved++;
				}
			}

			for ($x = 1; $x <= 4; $x++)
			{
				if (isset($where[$x])) {
					$this->db->clone_row('topics', 'topic_id', $this->get['t']);
					$id = $this->db->insert_id('topics');

					if( $topic['topic_modes'] & TOPIC_PUBLISH ) {
						$mode = TOPIC_PUBLISH;
					} else {
						$mode = 0;
					}
					$this->db->query("UPDATE %ptopics SET topic_title='%s', topic_replies=%d, topic_views=0, topic_description='', topic_modes=%d WHERE topic_id=%d",
						$this->post['topic'][$x], $where[$x]['count'], $mode, $id);
					$this->db->query("UPDATE %pposts SET post_topic=%d WHERE post_id IN (%s)", $id, implode(',', $where[$x]['posts']));

					$this->update_last_post_topic($id);

					$posts = $this->db->fetch("SELECT post_author, post_icon, post_time FROM %pposts WHERE post_topic=%d ORDER BY post_time ASC", $id);
					$this->db->query("UPDATE %ptopics SET topic_starter=%d, topic_icon='%s' WHERE topic_id=%d",
						$posts['post_author'], $posts['post_icon'], $id);
				}
			}

			$this->db->query("UPDATE %ptopics SET topic_replies=topic_replies-%d WHERE topic_id=%d",
				$moved, $this->get['t']);

			$this->update_last_post_topic($this->get['t']);
			$this->update_last_post($topic['topic_forum']);
			$this->log_action('topic_split', $this->get['t']);

			return $this->message($this->lang->mod_label_controls, $this->lang->mod_success_split);
		}
	}

	/**
	 * Deletes a single topic
	 *
	 * @param int $t Topic ID
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 4.0
	 * @return void
	 **/
	function delete_topic($t)
	{
		$posts = $this->db->query("
			SELECT DISTINCT t.topic_forum, t.topic_id, a.attach_file, p.post_author, p.post_id, p.post_count, u.user_posts
			FROM (%ptopics t, %pposts p, %pusers u)
			LEFT JOIN %pattach a ON p.post_id=a.attach_post
			WHERE t.topic_id=%d AND t.topic_id=p.post_topic AND p.post_author=u.user_id", $t);

		$deleted = -1;

		while ($post = $this->db->nqfetch($posts))
		{
			if ($post['post_count']) {
				$this->db->query('UPDATE %pusers SET user_posts=user_posts-1 WHERE user_id=%d', $post['post_author']);
			}

			if ($post['attach_file']) {
				$this->db->query('DELETE FROM %pattach WHERE attach_post=%d', $post['post_id']);
				@unlink('./attachments/' . $post['attach_file']);
			}

			$deleted++;
		}

		$result = $this->db->fetch('SELECT topic_forum FROM %ptopics WHERE topic_id=%d', $t);

		$this->db->query('DELETE FROM %pvotes WHERE vote_topic=%d', $t);
		$this->db->query('DELETE FROM %ptopics WHERE topic_id=%d OR topic_moved=%d', $t, $t);
		$this->db->query('DELETE FROM %pposts WHERE post_topic=%d', $t);
		$this->db->query('DELETE FROM %preadmarks WHERE readmark_topic = %d', $t);

		$this->update_reply_count($result['topic_forum'], $deleted);

		// Update all parent forums if any
		$forums = $this->db->fetch("SELECT forum_tree FROM %pforums WHERE forum_id=%d", $result['topic_forum']);
		$this->db->query("UPDATE %pforums SET forum_topics=forum_topics-1
			WHERE forum_parent > 0 AND forum_id IN (%s) OR forum_id=%d",
			$forums['forum_tree'], $result['topic_forum']);

		$this->sets['posts'] -= ($deleted+1);
		$this->sets['topics'] -= 1;
		$this->write_sets();
	}

	/**
	 * Deletes a single post
	 *
	 * @param int $p Post ID
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 4.0
	 * @return void
	 **/
	function delete_post($p)
	{
		$result = $this->db->fetch("SELECT t.topic_forum, t.topic_id, a.attach_file, p.post_author, p.post_count, u.user_posts
			FROM (%ptopics t, %pposts p, %pusers u)
			LEFT JOIN %pattach a ON p.post_id=a.attach_post
			WHERE p.post_id=%d AND t.topic_id=p.post_topic AND u.user_id=p.post_author", $p);

		$this->db->query('UPDATE %pforums SET forum_replies=forum_replies-1 WHERE forum_id=%d', $result['topic_forum']);
		$this->db->query('UPDATE %ptopics SET topic_replies=topic_replies-1 WHERE topic_id=%d', $result['topic_id']);
		if ($result['post_count']) {
			$posts = $result['user_posts'] - 1;

			if ($posts < 0) {
				$posts = 0;
			}
			$this->db->query('UPDATE %pusers SET user_posts=%d WHERE user_id=%d', $posts, $result['post_author']);
		}

		$this->db->query('DELETE FROM %pposts WHERE post_id=%d', $p);

		if ($result['attach_file']) {
			$this->db->query('DELETE FROM %pattach WHERE attach_post=%d', $p);
			@unlink('./attachments/' . $result['attach_file']);
		}

		$this->update_last_post_topic($result['topic_id']);
		$this->sets['posts'] -= 1;
		$this->write_sets();
	}

	/**
	 * Locks a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return void
	 **/
	function lock($t, $topic_modes)
	{
		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d',
			$topic_modes | TOPIC_LOCKED, $t);
	}

	/**
	 * Unlocks a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return void
	 **/
	function unlock($t, $topic_modes)
	{
		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d',
			$topic_modes ^ TOPIC_LOCKED, $t);
	}

	/**
	 * Pins a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return void
	 **/
	function pin($t, $topic_modes)
	{
		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d',
			$topic_modes | TOPIC_PINNED, $t);
	}

	/**
	 * Unpins a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return void
	 **/
	function unpin($t, $topic_modes)
	{
		$topic = $this->db->fetch('SELECT topic_forum FROM %ptopics WHERE topic_id=%d', $t);

		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d OR topic_moved=%d',
			$topic_modes ^ TOPIC_PINNED, $t, $t);
		$this->update_last_post($topic['topic_forum']);
	}

	/**
	 * Publishes a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @since 1.2
	 **/
	function publish($t, $topic_modes)
	{
		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d',
			$topic_modes | TOPIC_PUBLISH, $t);
	}

	/**
	 * Unpublishes a topic
	 *
	 * @param int $t Topic ID
	 * @param int $topic_modes Topic modes
	 * @since 1.2
	 **/
	function unpublish($t, $topic_modes)
	{
		$this->db->query('UPDATE %ptopics SET topic_modes=%d WHERE topic_id=%d',
			$topic_modes ^ TOPIC_PUBLISH, $t);
	}

	/**
	 * Decrements a forum and all it's parents by the given value.
	 *
	 * @param int $f the forum identifier
	 * @param int $ammount how much to decrement by
	 * @author Matthew Lawrence <matt@quicksilverforums.co.uk>
	 * @since 1.3.0
	 * @returns void
	**/
	function update_reply_count($f, $ammount, $topic = 0)
	{
		if (0 == $ammount && 0 == $topic) // nothing to do
			return;

		/* decrement the parent forums */
		$forums = $this->db->fetch("SELECT forum_tree FROM %pforums WHERE forum_id=%d", $f);

		if (isset($forums['forum_tree']) && 0 != strlen($forums['forum_tree']))
		{
			$wip = explode(',', $forums['forum_tree']);

			array_push($wip, $f);

			foreach ($wip as $fid)
			{
				$fid = intval($fid);

				if (0 == $fid)
					continue;

				$this->db->query('UPDATE %pforums SET forum_replies=forum_replies-%d WHERE forum_id=%d', $ammount, $fid);

				if (0 != $topic)
					$this->db->query('UPDATE %pforums SET forum_topics=forum_topics-%d WHERE forum_id=%d',
						intval($topic), $fid);

			}
		}
	}

	function update_count_move($f_from, $f_to, $ammount)
	{
		$this->update_reply_count($f_from, $ammount,  1);
		$this->update_reply_count($f_to, 0-$ammount, -1);
	}

	/**
	 * Updates the last post of a forum
	 *
	 * @param int $f Forum ID
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 4.0
	 * @return void
	 **/
	function update_last_post($f)
	{
		/* update any parent forums */
		$forums = $this->db->fetch("SELECT forum_tree FROM %pforums WHERE forum_id=%d", $f);

		if (isset($forums['forum_tree']) && 0 != strlen($forums['forum_tree']))
		{
			$wip = explode(',', $forums['forum_tree']);

			foreach ($wip as $fid)
			{
				$fid = intval($fid);

				/* handle weird cases */
				if (0 == $fid)
					continue;

				$this->_update_last_post($fid);
			}
		}

		/* update the specified forum */
		$this->_update_last_post($f);
	}

	function _update_last_post($f)
	{
		$post = $this->db->fetch('SELECT p.post_id FROM (%pposts p, %ptopics t)
			WHERE t.topic_id=p.post_topic AND t.topic_forum=%d
			ORDER BY t.topic_edited DESC, p.post_id DESC
			LIMIT 1', $f);

		if (!isset($post['post_id'])) {
			$post['post_id'] = 0;
		}

		$this->db->query('UPDATE %pforums SET forum_lastpost=%d WHERE forum_id=%d', $post['post_id'], $f);
	}

	/**
	 * Updates the last post of a topic
	 *
	 * @param int $t Topic ID
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.1.1
	 * @return void
	 **/
	function update_last_post_topic($t)
	{
		$last = $this->db->fetch("SELECT p.post_id, p.post_author, p.post_time
			FROM %pposts p, %ptopics t
			WHERE p.post_topic=t.topic_id AND t.topic_id=%d
			ORDER BY p.post_time DESC
			LIMIT 1", $t);

		$this->db->query("UPDATE %ptopics SET topic_last_post=%d, topic_last_poster=%d, topic_edited=%d WHERE topic_id=%d",
			$last['post_id'], $last['post_author'], $last['post_time'], $t);
	}

	/**
	 * Adds a moderator log entry
	 *
	 * @param string $action The action that was taken
	 * @param int $data1 The data acted upon (post ID, forum ID, etc)
	 * @param int $data2 Additional data, if necessary
	 * @param int $data3 Additional data, if necessary
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since 1.1.0
	 * @return void
	 **/
	function log_action($action, $data1, $data2 = 0, $data3 = 0)
	{
		$this->db->query("INSERT INTO %plogs (log_user, log_time, log_action, log_data1, log_data2, log_data3)
			VALUES (%d, %d, '%s', %d, %d, %d)",
			$this->user['user_id'], $this->time, $action, $data1, $data2, $data3);
	}

	/**
	 * Checks Subscriptions to make sure subscribed members can
	 * still view the forum where the topic has been moved too
	 *
	 * @param $newtopic integer of the selected topic
	 * @author Jonathan West <jon@quicksilverforums.com>
	 * @since 1.3.2
	 **/
	function update_subscriptions($newtopic)
	{
		$query = $this->db->query("SELECT s.subscription_user, s.subscription_item, s.subscription_type,
				u.user_id, u.user_group, u.user_perms,
				g.group_id, g.group_perms,
				t.topic_forum
				FROM (%psubscriptions s, %pusers u, %pgroups g, %ptopics t)
				WHERE s.subscription_user=u.user_id
				AND u.user_group=g.group_id
				AND t.topic_id=%d", $this->get['t']);

		while ($sub = $this->db->nqfetch($query))
		{
			$perms = new $this->modules['permissions']($this);
			$perms->db = &$this->db;
			$perms->pre = &$this->pre;
			$perms->get_perms($sub['user_group'], $sub['user_id'], ($sub['user_perms'] ? $sub['user_perms'] : $sub['group_perms']));

			if(!$perms->auth('forum_view', $sub['topic_forum'])) {
				$this->db->query("DELETE FROM %psubscriptions WHERE subscription_user=%d AND subscription_item=%d",
				$sub['user_id'], $sub['subscription_item']);
			} else {
				$this->db->query("UPDATE %psubscriptions SET subscription_item=%d WHERE subscription_item=%d AND subscription_type='topic'",
				$newtopic, $this->get['t']);
			}
		}
	}
}
?>