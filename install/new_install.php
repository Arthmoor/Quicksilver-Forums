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

if ( isset( $_POST['dbtype'] ) )
	$set['dbtype'] = $_POST['dbtype'];

require_once $set['include_path'] . '/lib/' . $set['dbtype'] . '.php';
require_once $set['include_path'] . '/global.php';
require_once $set['include_path'] . '/lib/xmlparser.php';
require_once $set['include_path'] . '/lib/packageutil.php';

/**
 * New Board Installation
 *
 * @author Jason Warner <jason@mercuryboard.com>
 */
class new_install extends qsfglobal
{
	function install_board( $step, $mysqli )
	{
		switch($step) {
		default:
			$url = preg_replace('/install\/?$/i', '', $this->server_url() . dirname($_SERVER['PHP_SELF']));

echo "<form action='{$this->self}?mode=new_install&amp;step=2' method='post'>
 <div class='article'>
  <div class='title' style='text-align:center'>New {$this->name} Installation</div>
  <div class='title'>Directory Permissions</div>";

			check_writeable_files();

			if(!is_writeable('../settings.php')) {
				echo "Settings file cannot be written to. The installer cannot continue until this problem is corrected.";
				break;
			}

echo "    <p></p>
  <div class='title' style='text-align:center'>New Database Configuration</div>

  <span class='field'>Host Server:</span>
  <span class='form'><input class='input' type='text' name='db_host' value='{$this->sets['db_host']}' /></span>
  <p class='line'></p>

  <span class='field'>Database Type:</span>
  <span class='form'>";
  if ($mysqli) {
    echo 'MySQLi';
  } else {
    echo 'MySQL';
  }
  echo "</span>
  <p class='line'></p>

  <span class='field'>Database Name:</span>
  <span class='form'><input class='input' type='text' name='db_name' value='{$this->sets['db_name']}' /></span>
  <p class='line'></p>

  <span class='field'>Database Username:</span>
  <span class='form'><input class='input' type='text' name='db_user' value='{$this->sets['db_user']}' /></span>
  <p class='line'></p>

  <span class='field'>Database Password:</span>
  <span class='form'><input class='input' type='password' name='db_pass' value='' /></span>
  <p class='line'></p>

  <span class='field'>Table Prefix:</span>
  <span class='form'>
   <input class='input' type='text' name='prefix' value='{$this->sets['prefix']}' /> This should only be changed if you need to install multiple QSF sites in the same database.
  </span>
  <p class='line'></p>

  <span class='field'>Database Port:</span>
  <span class='form'><input class='input' type='text' name='db_port' value='{$this->sets['db_port']}' /> Blank for none</span>
  <p class='line'></p>

  <span class='field'>Database Socket:</span>
  <span class='form'><input class='input' type='text' name='db_socket' value='{$this->sets['db_socket']}' /> Blank for none</span>
  <p></p>

  <div class='title' style='text-align:center'>New Site Settings</div>

  <span class='field'>Site Name:</span>
  <span class='form'><input class='input' type='text' name='site_name' value='QSF' size='75' /></span>
  <p class='line'></p>

  <span class='field'>Site URL:</span>
  <span class='form'><input class='input' type='text' name='site_url' value='{$url}' size='75' /></span>
  <p></p>

  <div class='title' style='text-align:center'>Administrator Account Settings</div>

  <span class='field'>User Name:</span>
  <span class='form'><input class='input' type='text' name='admin_name' size='30' maxlength='30' /></span>
  <p class='line'></p>

  <span class='field'>User Password:</span>
  <span class='form'><input class='input' type='password' name='admin_pass' size='30' /></span>
  <p class='line'></p>

  <span class='field'>Password (confirmation):</span>
  <span class='form'><input class='input' type='password' name='admin_pass2' size='30' /></span>
  <p class='line'></p>

  <span class='field'>Contact Email:</span>
  <span class='form'>
   <input class='input' type='text' name='admin_email' size='50' maxlength='100' value='{$this->sets['admin_email']}' />
   This is where messages from the system will be sent. Needs to be a real address.
  </span>
  <p class='line'></p>

  <span class='field'>System Email:</span>
  <span class='form'>
   <input class='input' type='text' name='contact_email' size='50' maxlength='100' />
   Address the system sends mail as. Can be either real or fake.
  </span>
  <p class='line'></p>

  <div style='text-align:center'>";

  if ($mysqli) {
    echo "<input type='hidden' name='dbtype' value='mysqli' />";
  } else {
    echo "<input type='hidden' name='dbtype' value='mysql' />";
  }

  echo "<input type='submit' name='submit' value='Continue' /></div>
 </div>
</form>";
break;

		case 2:
  echo "<div class='article'>
  <div class='title'>New {$this->name} Installation</div>";

			$db = new $this->modules['database']($this->post['db_host'], $this->post['db_user'], $this->post['db_pass'], $this->post['db_name'], $this->post['db_port'], $this->post['db_socket'], $this->post['prefix']);

			if (!$db->connection) {
				echo "Couldn't connect to a database using the specified information.";
				break;
			}
			$this->db = &$db;

			$this->sets['db_host']   = $this->post['db_host'];
			$this->sets['db_user']   = $this->post['db_user'];
			$this->sets['db_pass']   = $this->post['db_pass'];
			$this->sets['db_name']   = $this->post['db_name'];
			$this->sets['db_port']   = $this->post['db_port'];
			$this->sets['db_socket'] = $this->post['db_socket'];
			$this->sets['dbtype']    = $this->post['dbtype'];
			$this->sets['prefix']    = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $this->post['prefix']));
			$this->sets['admin_email'] = $this->post['admin_email'];

			if (!$this->write_db_sets('../settings.php') && !isset($this->post['downloadsettings'])) {
				echo "The database connection was ok, but settings.php could not be updated.<br />\n";
				echo "You can CHMOD settings.php to 0666 and hit reload to try again<br/>\n";
				echo "Or you can force the install to continue and download the new settings.php file ";
				echo "so you can later place it on the website manually<br/>\n";
				echo "<form action=\"{$this->self}?mode=new_install&amp;step=2\" method=\"post\">\n
					<input type=\"hidden\" name=\"downloadsettings\" value=\"yes\" />\n
					<input type=\"hidden\" name=\"db_host\" value=\"" . htmlspecialchars($this->post['db_host']) . "\" />\n
					<input type=\"hidden\" name=\"db_name\" value=\"" . htmlspecialchars($this->post['db_name']) . "\" />\n
					<input type=\"hidden\" name=\"db_user\" value=\"" . htmlspecialchars($this->post['db_user']) . "\" />\n
					<input type=\"hidden\" name=\"db_pass\" value=\"" . htmlspecialchars($this->post['db_pass']) . "\" />\n
					<input type=\"hidden\" name=\"db_port\" value=\"" . htmlspecialchars($this->post['db_port']) . "\" />\n
					<input type=\"hidden\" name=\"db_socket\" value=\"" . htmlspecialchars($this->post['db_socket']) . "\" />\n
					<input type=\"hidden\" name=\"prefix\" value=\"" . htmlspecialchars($this->post['prefix']) . "\" />\n
					<input type=\"hidden\" name=\"dbtype\" value=\"" . htmlspecialchars($this->post['dbtype']) . "\" />\n
					<input type=\"hidden\" name=\"site_name\" value=\"" . htmlspecialchars($this->post['site_name']) . "\" />\n
					<input type=\"hidden\" name=\"site_url\" value=\"" . htmlspecialchars($this->post['site_url']) . "\" />\n
					<input type=\"hidden\" name=\"admin_name\" value=\"" . htmlspecialchars($this->post['admin_name']) . "\" />\n
					<input type=\"hidden\" name=\"admin_pass\" value=\"" . htmlspecialchars($this->post['admin_pass']) . "\" />\n
					<input type=\"hidden\" name=\"admin_pass2\" value=\"" . htmlspecialchars($this->post['admin_pass2']) . "\" />\n
					<input type=\"hidden\" name=\"admin_email\" value=\"" . htmlspecialchars($this->post['admin_email']) . "\" />\n
					";
				echo "<input type=\"submit\" value=\"Force Install\" />
					</form>
					 ";
				break;
			}

			$filename = './' . $this->sets['dbtype'] . '_data_tables.php';
			if (!is_readable($filename)) {
				echo 'Database connected, settings written, but no tables could be loaded from file: ' . $filename;
				break;
			}

			if (!is_readable('skin_default.xml')) {
				echo 'Database connected, settings written, but no templates could be loaded from file: skin_default.xml';
				break;
			}

			if ((trim($this->post['admin_name']) == '')
			|| (trim($this->post['admin_pass']) == '')
			|| (trim($this->post['admin_email']) == '')) {
				echo 'You have not specified an admistrator account. Please go back and correct this error.';
				break;
			}

			if ($this->post['admin_pass'] != $this->post['admin_pass2']) {
				echo 'Your administrator passwords do not match. Please go back and correct this error.';
				break;
			}

			$queries = array();
			$pre = $this->sets['prefix'];
			$this->pre = $this->sets['prefix'];

			// Create tables
			include './' . $this->sets['dbtype'] . '_data_tables.php';

			execute_queries($queries, $db);
			$queries = null;
			
			// Create template
			$xmlInfo = new xmlparser();
			$xmlInfo->parse('skin_default.xml');
			$templatesNode = $xmlInfo->GetNodeByPath('QSFMOD/TEMPLATES');
			packageutil::insert_templates('default', $this->db, $templatesNode);
			unset($templatesNode);
			$xmlInfo = null;

			$this->sets = $this->get_settings($this->sets);

			$this->sets['loc_of_board'] = $this->post['site_url'];
			$this->sets['forum_name'] = $this->post['site_name'];

			$this->post['admin_pass'] = md5($this->post['admin_pass']);

			$this->post['admin_name'] = str_replace(
				array('&amp;#', '\''),
				array('&#', '&#39;'),
				htmlspecialchars($this->post['admin_name'])
			);

			$this->db->query("INSERT INTO %pusers (user_name, user_password, user_group, user_title, user_title_custom, user_joined, user_email, user_timezone, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height)
				VALUES ('%s', '%s', %d, 'Administrator', 1, %d, '%s', %d, '%s', '%s', %d, %d)",
				$this->post['admin_name'], $this->post['admin_pass'], USER_ADMIN, $this->time, $this->post['admin_email'], 0, './avatars/avatar.jpg', 'local', 100, 100);
			$admin_uid = $this->db->insert_id("users");

			$this->sets['akismet_email'] = 0;
			$this->sets['akismet_ureg'] = 0;
			$this->sets['akismet_sigs'] = 0;
			$this->sets['akismet_profiles'] = 0;
			$this->sets['akismet_posts'] = 0;
			$this->sets['akismet_posts_number'] = 5;
			$this->sets['wordpress_api_key'] = '';
			$this->sets['spam_post_count'] = 0;
			$this->sets['spam_email_count'] = 0;
			$this->sets['spam_reg_count'] = 0;
			$this->sets['spam_sig_count'] = 0;
			$this->sets['spam_profile_count'] = 0;
			$this->sets['ham_count'] = 0;
			$this->sets['spam_false_count'] = 0;
			$this->sets['spam_pending'] = 0;

			$this->sets['attach_upload_size'] = 51200;
			$this->sets['attach_types'] = array('jpg', 'gif', 'png', 'bmp', 'zip', 'tgz', 'gz', 'rar', '7z');

			$this->sets['avatar_width'] = 100;
			$this->sets['avatar_height'] = 100;
			$this->sets['avatar_upload_size'] = 51200;

			$this->sets['banned_ips'] = array();
			$this->sets['closed'] = 0;
			$this->sets['closedtext'] = 'The forum is currently closed for maintenance. Please check back later.';

			$server = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			$this->sets['cookie_domain'] = $server;

			$path = dirname($_SERVER['PHP_SELF']);
			$path = str_replace( 'install', '', $path );
			$this->sets['cookie_path'] = $path;

			$this->sets['cookie_prefix'] = 'qsf_';
			$this->sets['cookie_secure'] = 0;

			$this->sets['default_timezone'] = 0.0;
			$this->sets['servertime'] = 0.0;

			$this->sets['emailactivation'] = 1;
			$this->sets['flood_time'] = 30;
			$this->sets['hot_limit'] = 20;
			$this->sets['link_target'] = '_blank';
			$this->sets['logintime'] = 31536000;
			$this->sets['mailserver'] = 'localhost';
			$this->sets['max_load'] = 0;
			$this->sets['analytics_id'] = '';
			$this->sets['mostonline'] = 0;
			$this->sets['mostonlinetime'] = 0;
			$this->sets['posts'] = 0;
			$this->sets['posts_per_page'] = 15;
			$this->sets['register_image'] = 0;
			$this->sets['topics'] = 0;
			$this->sets['topics_per_page'] = 20;
			$this->sets['vote_after_results'] = 0;
			$this->sets['default_skin'] = 'default';
			$this->sets['default_email_shown'] = 0;
			$this->sets['default_lang'] = 'en';
			$this->sets['default_group'] = 2;
			$this->sets['default_pm'] = 1;
			$this->sets['default_view_avatars'] = 1;
			$this->sets['default_view_sigs'] = 1;
			$this->sets['default_view_emots'] = 1;
			$this->sets['flood_time_pm'] = 30;
			$this->sets['flood_time_search'] = 10;
			$this->sets['spider_active'] = 1;
			$this->sets['spider_name'] = array(
				'googlebot' 	=> 'Google',
				'yahoo'		=> 'Yahoo!',
				'bingbot'	=> 'Bing'
			);
			$this->sets['debug_mode'] = 0;
			$this->sets['rss_feed_title'] = '';
			$this->sets['rss_feed_desc'] = '';
			$this->sets['rss_feed_posts'] = 5;
			$this->sets['rss_feed_time'] = 60;

			$this->sets['last_member'] = $this->post['admin_name'];
			$this->sets['last_member_id'] = $admin_uid;
			$this->sets['admin_incoming'] = $this->post['admin_email'];
			$this->sets['admin_outgoing'] = $this->post['contact_email'];
			$this->sets['members'] = 1;
			$this->sets['installed'] = 1;
			$this->sets['app_version'] = $this->version;

			$topicName = "Welcome to {$this->name}";
			$topicDesc = '';
			$topicIcon = 'exclaim.gif';
			$topicPost = "Congratulations on your successful install of {$this->name} {$this->version}. A couple of places you should visit now that things are up and running:

[b]Admin CP[/b]
In the Admin CP, you can configure the details of your site from the Board Settings menu. Then from there you can start creating more forums for your site.

[b]Control Panel[/b]
The control panel is where you and your future members can configure their user data such as avatars, signatures, and their profiles.

Should you need assistance with something, have an issue to report, or just want to drop by and show your appreciation, the project's [url=https://github.com/Arthmoor/Quicksilver-Forums]Google Code[/url] page is the place do to it.

Have fun and enjoy your new site!";

			// Create Category
			$categoryId = $this->create_forum('Discussion', '', 0);

			// Create Forum
			$forumId = $this->create_forum('Chat', 'Welcome to the forum. Introduce yourself, ask a question or join in on any conversation here.', $categoryId);

			// Create Topic
			$this->db->query("INSERT INTO %ptopics (topic_title, topic_forum, topic_description, topic_starter, topic_icon, topic_posted, topic_edited, topic_last_poster, topic_modes) 
				VALUES ('%s', %d, '%s', %d, '%s', %d, %d, %d, %d)",
				$topicName, $forumId, $topicDesc, $admin_uid, $topicIcon, $this->time, $this->time, $admin_uid, TOPIC_PUBLISH);
			$topicId = $this->db->insert_id("topics");

			// Create Post
			$this->db->query("INSERT INTO %pposts (post_topic, post_author, post_text, post_time, post_emoticons, post_mbcode, post_ip, post_icon)
				VALUES (%d, %d, '%s', %d, 1, 1, INET_ATON('%s'), '%s')",
				$topicId, $admin_uid, $topicPost, $this->time, $this->ip, $topicIcon);
			$postId = $this->db->insert_id("posts");

			$this->db->query("UPDATE %ptopics SET topic_last_post=%d WHERE topic_id=%d", $postId, $topicId);

			$this->db->query("UPDATE %pusers SET user_posts=user_posts+1, user_lastpost=%d WHERE user_id=%d", $this->time, $admin_uid);

			$this->db->query("UPDATE %pforums SET forum_topics=forum_topics+1, forum_lastpost=%d WHERE forum_id=%d", $postId, $forumId);

			$this->sets['topics']++;
			$this->sets['posts']++;

			$writeSetsWorked = $this->write_db_sets('../settings.php');
			$this->write_sets();

			setcookie($this->sets['cookie_prefix'] . 'user', $admin_uid, $this->time + $this->sets['logintime'], $this->sets['cookie_path'], $this->sets['cookie_domain'], $this->sets['cookie_secure'], true );
			setcookie($this->sets['cookie_prefix'] . 'pass', $this->post['admin_pass'], $this->time + $this->sets['logintime'], $this->sets['cookie_path'], $this->sets['cookie_domain'], $this->sets['cookie_secure'], true );

			if (!$writeSetsWorked) {
				echo "Congratulations! Your board has been installed.<br />
				An administrator account was registered.<br />";
				echo "Click here to download your settings.php file. You must put this file on the webhost before the board is ready to use<br/>\n";
				echo "<form action=\"{$this->self}?mode=new_install&amp;step=3\" method=\"post\">\n
					<input type=\"hidden\" name=\"db_host\" value=\"" . htmlspecialchars($this->post['db_host']) . "\" />\n
					<input type=\"hidden\" name=\"db_name\" value=\"" . htmlspecialchars($this->post['db_name']) . "\" />\n
					<input type=\"hidden\" name=\"db_user\" value=\"" . htmlspecialchars($this->post['db_user']) . "\" />\n
					<input type=\"hidden\" name=\"db_pass\" value=\"" . htmlspecialchars($this->post['db_pass']) . "\" />\n
					<input type=\"hidden\" name=\"db_port\" value=\"" . htmlspecialchars($this->post['db_port']) . "\" />\n
					<input type=\"hidden\" name=\"db_socket\" value=\"" . htmlspecialchars($this->post['db_socket']) . "\" />\n
					<input type=\"hidden\" name=\"prefix\" value=\"" . htmlspecialchars($this->post['prefix']) . "\" />\n
					<input type=\"hidden\" name=\"dbtype\" value=\"" . htmlspecialchars($this->post['dbtype']) . "\" />\n
					<input type=\"submit\" value=\"Download settings.php\" />
					</form>
					<br/>\n
					Once this is done: REMEMBER TO DELETE THE INSTALL DIRECTORY!<br /><br />
					<a href='../index.php'>Go to your board.</a>
					 ";
			} else {
				echo "Congratulations! Your board has been installed.<br />
				An administrator account was registered.<br />
				REMEMBER TO DELETE THE INSTALL DIRECTORY!<br /><br />
				<a href='../index.php'>Go to your board.</a>";
			}
			break;
		case 3:
			// Give them the settings.php file
			$this->sets['db_host']   = $this->post['db_host'];
			$this->sets['db_user']   = $this->post['db_user'];
			$this->sets['db_pass']   = $this->post['db_pass'];
			$this->sets['db_name']   = $this->post['db_name'];
			$this->sets['db_port']   = $this->post['db_port'];
			$this->sets['db_socket'] = $this->post['db_socket'];
			$this->sets['dbtype']    = $this->post['dbtype'];
			$this->sets['installed'] = 1;
			$this->sets['prefix']    = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $this->post['prefix']));

			$settingsFile = $this->create_settings_file();
			ob_clean();
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"settings.php\"");
			echo $settingsFile;
			exit;
			break;
		}

		echo '</div>';
	}

	function server_url()
	{
	   $proto = "http" .
		   ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
	   $server = isset($_SERVER['HTTP_HOST']) ?
		   $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	   return $proto . $server;
	}
	
	/**
	 * Creates a category or forum
	 *
	 * @param string $name Name of the forum
	 * @param string $desc Description of the forum
	 * @param int $parent Parent id of the forum (0 if a category)
	 * @author Geoffrey Dunn <geoff@warmage.com>
	 * @since 1.1.9
	 * @return int id of the forum created
	 **/
	function create_forum($name, $desc, $parent)
	{
		$parent ? $tree = $parent : $tree = '';
		
		$this->db->query("INSERT INTO %pforums
			(forum_tree, forum_parent, forum_name, forum_description, forum_position, forum_subcat) VALUES
			('%s', %d, '%s', '%s', '0', '0')",
			$tree, $parent, $name, $desc);
		
		$forumId = $this->db->insert_id("forums");
		
		$perms = new $this->modules['permissions']($this);
		
		while ($perms->get_group())
		{
			if (!$parent) {
				// Default permissions
				$perms->add_z($forumId);
			} else {
				// Copy permissions
				$perms->add_z($forumId, false);

				foreach ($perms->standard as $perm => $false)
				{
					if (!isset($perms->globals[$perm])) {
						$perms->set_xyz($perm, $forumId, $perms->auth($perm, $parent));
					}
				}
			}
			$perms->update();
		}
		return $forumId;
	}
}
?>