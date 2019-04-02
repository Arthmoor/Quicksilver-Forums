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

if (!defined('QSF_INSTALLER')) {
	exit('Use index.php to install.');
}

$queries[] = "DROP TABLE IF EXISTS %pactive";
$queries[] = "CREATE TABLE %pactive (
  active_id int(10) unsigned NOT NULL default '0',
  active_ip varchar(40) NOT NULL default '127.0.0.1',
  active_user_agent varchar(100) NOT NULL default 'Unknown',
  active_action varchar(32) NOT NULL default '',
  active_item int(10) unsigned NOT NULL default '0',
  active_time int(10) unsigned NOT NULL default '0',
  active_session varchar(32) NOT NULL default '',
  UNIQUE KEY active_session (active_session),
  UNIQUE KEY active_ip (active_ip)
) ENGINE=MyISAM ROW_FORMAT=FIXED";

$queries[] = "DROP TABLE IF EXISTS %pattach";
$queries[] = "CREATE TABLE %pattach (
  attach_id int(12) unsigned NOT NULL auto_increment,
  attach_file varchar(32) NOT NULL default '',
  attach_name varchar(255) NOT NULL default '',
  attach_post int(12) unsigned NOT NULL default '0',
  attach_downloads int(10) unsigned NOT NULL default '0',
  attach_size int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (attach_id),
  KEY attach_post (attach_post)
) ENGINE=MyISAM ROW_FORMAT=FIXED";

$queries[] = "DROP TABLE IF EXISTS %pforums";
$queries[] = "CREATE TABLE %pforums (
  forum_id smallint(4) unsigned NOT NULL auto_increment,
  forum_parent smallint(4) unsigned NOT NULL default '0',
  forum_tree varchar(255) NOT NULL default '',
  forum_name varchar(255) NOT NULL default '',
  forum_position smallint(4) unsigned NOT NULL default '0',
  forum_description varchar(255) NOT NULL default '',
  forum_topics int(10) unsigned NOT NULL default '0',
  forum_replies int(12) unsigned NOT NULL default '0',
  forum_lastpost int(12) unsigned NOT NULL default '0',
  forum_subcat tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (forum_id),
  KEY Parent (forum_parent)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %pgroups";
$queries[] = "CREATE TABLE %pgroups (
  group_id tinyint(3) unsigned NOT NULL auto_increment,
  group_name varchar(255) NOT NULL default '',
  group_type varchar(20) NOT NULL default '',
  group_format varchar(255) NOT NULL default '%%s',
  group_perms text NOT NULL default '',
  PRIMARY KEY  (group_id)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %phelp";
$queries[] = "CREATE TABLE %phelp (
  help_id smallint(3) NOT NULL auto_increment,
  help_title varchar(255) NOT NULL default '',
  help_article text NOT NULL default '',
  PRIMARY KEY  (help_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$queries[] = "DROP TABLE IF EXISTS %plogs";
$queries[] = "CREATE TABLE %plogs (
  log_id int(10) unsigned NOT NULL auto_increment,
  log_user int(10) unsigned NOT NULL default '0',
  log_time int(10) unsigned NOT NULL default '0',
  log_action varchar(20) NOT NULL default '',
  log_data1 int(12) unsigned NOT NULL default '0',
  log_data2 smallint(4) unsigned NOT NULL default '0',
  log_data3 smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (log_id)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %pmembertitles";
$queries[] = "CREATE TABLE %pmembertitles (
  membertitle_id tinyint(3) unsigned NOT NULL auto_increment,
  membertitle_title varchar(50) NOT NULL default '',
  membertitle_posts int(10) unsigned NOT NULL default '0',
  membertitle_icon varchar(25) NOT NULL default '',
  PRIMARY KEY  (membertitle_id),
  KEY Posts (membertitle_posts)
) ENGINE=MyISAM ROW_FORMAT=FIXED";

$queries[] = "DROP TABLE IF EXISTS %ppmsystem";
$queries[] = "CREATE TABLE %ppmsystem (
  pm_id int(10) unsigned NOT NULL auto_increment,
  pm_to int(10) unsigned NOT NULL default '0',
  pm_from int(10) unsigned NOT NULL default '0',
  pm_ip varchar(40) NOT NULL default '127.0.0.1',
  pm_bcc text NOT NULL default '',
  pm_title varchar(255) NOT NULL default '[No Title]',
  pm_time int(10) unsigned NOT NULL default '0',
  pm_message text NOT NULL default '',
  pm_read tinyint(1) unsigned NOT NULL default '0',
  pm_folder tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (pm_id),
  KEY NewPMs (pm_to,pm_read,pm_folder)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$queries[] = "DROP TABLE IF EXISTS %pposts";
$queries[] = "CREATE TABLE %pposts (
  post_id int(12) unsigned NOT NULL auto_increment,
  post_topic int(10) unsigned NOT NULL default '0',
  post_author int(10) unsigned NOT NULL default '0',
  post_emoticons tinyint(1) unsigned NOT NULL default '1',
  post_mbcode tinyint(1) unsigned NOT NULL default '1',
  post_count tinyint(1) unsigned NOT NULL default '1',
  post_text text NOT NULL default '',
  post_time int(10) unsigned NOT NULL default '0',
  post_icon varchar(32) NOT NULL default '',
  post_ip varchar(40) NOT NULL default '127.0.0.1',
  post_edited_by varchar(32) NOT NULL default '',
  post_edited_time int(10) unsigned NOT NULL default '0',
  post_referrer tinytext,
  post_agent tinytext,
  PRIMARY KEY  (post_id),
  KEY Topic (post_topic),
  FULLTEXT KEY post_text (post_text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$queries[] = "DROP TABLE IF EXISTS %preadmarks";
$queries[] = "CREATE TABLE %preadmarks (
  readmark_user int(10) unsigned NOT NULL default '0',
  readmark_topic int(10) unsigned NOT NULL default '0',
  readmark_lastread int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (readmark_user,readmark_topic)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %preplacements";
$queries[] = "CREATE TABLE %preplacements (
  replacement_id smallint(3) unsigned NOT NULL auto_increment,
  replacement_search varchar(50) NOT NULL default '',
  PRIMARY KEY  (replacement_id)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %psettings";
$queries[] = "CREATE TABLE %psettings (
  settings_id tinyint(2) unsigned NOT NULL auto_increment,
  settings_tos text NOT NULL default '',
  settings_meta_keywords tinytext,
  settings_meta_description tinytext,
  settings_data text NOT NULL default '',
  PRIMARY KEY  (settings_id)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %pskins";
$queries[] = "CREATE TABLE %pskins (
  skin_name varchar(32) NOT NULL default '',
  skin_dir varchar(32) NOT NULL default '',
  PRIMARY KEY  (skin_dir)
) ENGINE=MyISAM ROW_FORMAT=FIXED";

$queries[] = "DROP TABLE IF EXISTS %pspam";
$queries[] = "CREATE TABLE %pspam (
  spam_id int(12) unsigned NOT NULL auto_increment,
  spam_topic int(10) unsigned NOT NULL default '0',
  spam_author int(10) unsigned NOT NULL default '0',
  spam_emoticons tinyint(1) unsigned NOT NULL default '1',
  spam_mbcode tinyint(1) unsigned NOT NULL default '1',
  spam_count tinyint(1) unsigned NOT NULL default '1',
  spam_text text NOT NULL default '',
  spam_time int(10) unsigned NOT NULL default '0',
  spam_icon varchar(32) NOT NULL default '',
  spam_ip varchar(40) NOT NULL default '127.0.0.1',
  spam_edited_by varchar(32) NOT NULL default '',
  spam_edited_time int(10) unsigned NOT NULL default '0',
  spam_svars text,
  PRIMARY KEY  (spam_id),
  KEY Topic (spam_topic),
  FULLTEXT KEY spam_text (spam_text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$queries[] = "DROP TABLE IF EXISTS %psubscriptions";
$queries[] = "CREATE TABLE %psubscriptions (
  subscription_id int(12) unsigned NOT NULL auto_increment,
  subscription_user int(10) unsigned NOT NULL default '0',
  subscription_type varchar(10) NOT NULL default '',
  subscription_item int(10) unsigned NOT NULL default '0',
  subscription_expire int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (subscription_id),
  KEY subscription_item (subscription_item)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %ptemplates";
$queries[] = "CREATE TABLE %ptemplates (
  template_skin varchar(32) NOT NULL default 'default',
  template_set varchar(20) NOT NULL default '',
  template_name varchar(36) NOT NULL default '',
  template_html text NOT NULL default '',
  template_displayname varchar(255) NOT NULL default '',
  template_description varchar(255) NOT NULL default '',
  UNIQUE KEY Piece (template_name,template_skin),
  KEY Section (template_set,template_skin)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %ptopics";
$queries[] = "CREATE TABLE %ptopics (
  topic_id int(10) unsigned NOT NULL auto_increment,
  topic_forum smallint(3) unsigned NOT NULL default '0',
  topic_title varchar(75) NOT NULL default '0',
  topic_description varchar(35) NOT NULL default '',
  topic_starter int(10) unsigned NOT NULL default '0',
  topic_last_post int(10) unsigned NOT NULL default '0',
  topic_last_poster int(10) unsigned NOT NULL default '0',
  topic_icon varchar(32) NOT NULL default '',
  topic_posted int(10) unsigned NOT NULL default '0',
  topic_edited int(10) unsigned NOT NULL default '0',
  topic_replies smallint(5) unsigned NOT NULL default '0',
  topic_views smallint(5) unsigned NOT NULL default '0',
  topic_modes int(10) unsigned NOT NULL default '0',
  topic_moved smallint(3) unsigned NOT NULL default '0',
  topic_poll_options text NOT NULL default '',
  PRIMARY KEY  (topic_id),
  KEY Forum (topic_forum)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";

$queries[] = "DROP TABLE IF EXISTS %pusers";
$queries[] = "CREATE TABLE %pusers (
  user_id int(10) unsigned NOT NULL auto_increment,
  user_name varchar(255) NOT NULL default '',
  user_password varchar(32) NOT NULL default '',
  user_joined int(10) unsigned NOT NULL default '0',
  user_level tinyint(3) unsigned NOT NULL default '1',
  user_title varchar(100) NOT NULL default '',
  user_title_custom tinyint(1) unsigned NOT NULL default '0',
  user_group tinyint(3) unsigned NOT NULL default '2',
  user_skin varchar(32) NOT NULL default 'default',
  user_language varchar(6) NOT NULL default 'en',
  user_avatar varchar(150) NOT NULL default '',
  user_avatar_type enum('local','url','uploaded','none') NOT NULL default 'none',
  user_avatar_width smallint(3) unsigned NOT NULL default '0',
  user_avatar_height smallint(3) unsigned NOT NULL default '0',
  user_email varchar(100) NOT NULL default '',
  user_email_show tinyint(1) unsigned NOT NULL default '0',
  user_email_form tinyint(1) unsigned NOT NULL default '1',
  user_birthday date NOT NULL default '0000-00-00',
  user_timezone float(3,1) NOT NULL default '0.0',
  user_homepage varchar(255) NOT NULL default '',
  user_posts int(10) unsigned NOT NULL default '0',
  user_location varchar(100) NOT NULL default '',
  user_icq int(16) unsigned NOT NULL default '0',
  user_msn varchar(32) NOT NULL default '',
  user_aim varchar(32) NOT NULL default '',
  user_gtalk varchar(32) NOT NULL default '',
  user_pm tinyint(1) unsigned NOT NULL default '1',
  user_pm_mail tinyint(1) unsigned NOT NULL default '0',
  user_active tinyint(1) unsigned NOT NULL default '1',
  user_yahoo varchar(100) NOT NULL default '',
  user_interests varchar(255) NOT NULL default '',
  user_signature text NOT NULL default '',
  user_lastvisit int(10) unsigned NOT NULL default '0',
  user_lastallread int(10) unsigned NOT NULL default '0',
  user_lastpost int(10) unsigned NOT NULL default '0',
  user_lastpm int(10) unsigned NOT NULL default '0',
  user_lastsearch int(10) unsigned NOT NULL default '0',
  user_view_avatars tinyint(1) unsigned NOT NULL default '1',
  user_view_signatures tinyint(1) unsigned NOT NULL default '1',
  user_view_emoticons tinyint(1) unsigned NOT NULL default '1',
  user_topics_page tinyint(2) unsigned NOT NULL DEFAULT '0',
  user_posts_page tinyint(2) unsigned NOT NULL DEFAULT '0',
  user_regip varchar(40) NOT NULL default '127.0.0.1',
  user_register_email varchar(100) default '',
  user_server_data text,
  user_perms text NOT NULL default '',
  PRIMARY KEY  (user_id)
) ENGINE=MyISAM";

$queries[] = "DROP TABLE IF EXISTS %pvotes";
$queries[] = "CREATE TABLE %pvotes (
  vote_user int(10) unsigned NOT NULL default '0',
  vote_topic int(10) unsigned NOT NULL default '0',
  vote_option smallint(4) NOT NULL default '-1',
  PRIMARY KEY  (vote_user,vote_topic)
) ENGINE=MyISAM";

$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (1, 'Administrators', 'ADMIN', '<b>%%s</b>', 'a:46:{s:10:\"board_view\";b:1;s:17:\"board_view_closed\";b:1;s:11:\"do_anything\";b:1;s:11:\"edit_avatar\";b:1;s:12:\"edit_profile\";b:1;s:8:\"edit_sig\";b:1;s:9:\"email_use\";b:1;s:10:\"forum_view\";b:1;s:8:\"is_admin\";b:1;s:10:\"pm_noflood\";b:1;s:11:\"poll_create\";b:1;s:9:\"poll_vote\";b:1;s:11:\"post_attach\";b:1;s:20:\"post_attach_download\";b:1;s:11:\"post_create\";b:1;s:11:\"post_delete\";b:1;s:15:\"post_delete_own\";b:1;s:9:\"post_edit\";b:1;s:13:\"post_edit_own\";b:1;s:18:\"post_inc_userposts\";b:1;s:12:\"post_noflood\";b:1;s:11:\"post_viewip\";b:1;s:14:\"search_noflood\";b:1;s:12:\"topic_create\";b:1;s:12:\"topic_delete\";b:1;s:16:\"topic_delete_own\";b:1;s:10:\"topic_edit\";b:1;s:14:\"topic_edit_own\";b:1;s:12:\"topic_global\";b:1;s:10:\"topic_lock\";b:1;s:14:\"topic_lock_own\";b:1;s:10:\"topic_move\";b:1;s:14:\"topic_move_own\";b:1;s:9:\"topic_pin\";b:1;s:13:\"topic_pin_own\";b:1;s:13:\"topic_publish\";b:1;s:18:\"topic_publish_auto\";b:1;s:11:\"topic_split\";b:1;s:15:\"topic_split_own\";b:1;s:12:\"topic_unlock\";b:1;s:16:\"topic_unlock_mod\";b:1;s:16:\"topic_unlock_own\";b:1;s:11:\"topic_unpin\";b:1;s:15:\"topic_unpin_own\";b:1;s:10:\"topic_view\";b:1;s:22:\"topic_view_unpublished\";b:1;}')";
$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (2, 'Members', 'MEMBER', '%%s', 'a:46:{s:10:\"board_view\";b:1;s:17:\"board_view_closed\";b:0;s:11:\"do_anything\";b:1;s:11:\"edit_avatar\";b:1;s:12:\"edit_profile\";b:1;s:8:\"edit_sig\";b:1;s:9:\"email_use\";b:1;s:10:\"forum_view\";b:1;s:8:\"is_admin\";b:0;s:10:\"pm_noflood\";b:0;s:11:\"poll_create\";b:1;s:9:\"poll_vote\";b:1;s:11:\"post_attach\";b:1;s:20:\"post_attach_download\";b:1;s:11:\"post_create\";b:1;s:11:\"post_delete\";b:0;s:15:\"post_delete_own\";b:1;s:9:\"post_edit\";b:0;s:13:\"post_edit_own\";b:1;s:18:\"post_inc_userposts\";b:1;s:12:\"post_noflood\";b:0;s:11:\"post_viewip\";b:0;s:14:\"search_noflood\";b:1;s:12:\"topic_create\";b:1;s:12:\"topic_delete\";b:0;s:16:\"topic_delete_own\";b:1;s:10:\"topic_edit\";b:0;s:14:\"topic_edit_own\";b:1;s:12:\"topic_global\";b:0;s:10:\"topic_lock\";b:0;s:14:\"topic_lock_own\";b:0;s:10:\"topic_move\";b:0;s:14:\"topic_move_own\";b:0;s:9:\"topic_pin\";b:0;s:13:\"topic_pin_own\";b:0;s:13:\"topic_publish\";b:0;s:18:\"topic_publish_auto\";b:0;s:11:\"topic_split\";b:0;s:15:\"topic_split_own\";b:0;s:12:\"topic_unlock\";b:0;s:16:\"topic_unlock_mod\";b:0;s:16:\"topic_unlock_own\";b:0;s:11:\"topic_unpin\";b:0;s:15:\"topic_unpin_own\";b:0;s:10:\"topic_view\";b:1;s:22:\"topic_view_unpublished\";b:0;}')";
$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (3, 'Guests', 'GUEST', '%%s', 'a:46:{s:10:\"board_view\";b:1;s:17:\"board_view_closed\";b:0;s:11:\"do_anything\";b:1;s:11:\"edit_avatar\";b:0;s:12:\"edit_profile\";b:0;s:8:\"edit_sig\";b:0;s:9:\"email_use\";b:0;s:10:\"forum_view\";b:1;s:8:\"is_admin\";b:0;s:10:\"pm_noflood\";b:0;s:11:\"poll_create\";b:0;s:9:\"poll_vote\";b:0;s:11:\"post_attach\";b:0;s:20:\"post_attach_download\";b:0;s:11:\"post_create\";b:0;s:11:\"post_delete\";b:0;s:15:\"post_delete_own\";b:0;s:9:\"post_edit\";b:0;s:13:\"post_edit_own\";b:0;s:18:\"post_inc_userposts\";b:0;s:12:\"post_noflood\";b:0;s:11:\"post_viewip\";b:0;s:14:\"search_noflood\";b:0;s:12:\"topic_create\";b:0;s:12:\"topic_delete\";b:0;s:16:\"topic_delete_own\";b:0;s:10:\"topic_edit\";b:0;s:14:\"topic_edit_own\";b:0;s:12:\"topic_global\";b:0;s:10:\"topic_lock\";b:0;s:14:\"topic_lock_own\";b:0;s:10:\"topic_move\";b:0;s:14:\"topic_move_own\";b:0;s:9:\"topic_pin\";b:0;s:13:\"topic_pin_own\";b:0;s:13:\"topic_publish\";b:0;s:18:\"topic_publish_auto\";b:0;s:11:\"topic_split\";b:0;s:15:\"topic_split_own\";b:0;s:12:\"topic_unlock\";b:0;s:16:\"topic_unlock_mod\";b:0;s:16:\"topic_unlock_own\";b:0;s:11:\"topic_unpin\";b:0;s:15:\"topic_unpin_own\";b:0;s:10:\"topic_view\";b:1;s:22:\"topic_view_unpublished\";b:0;}')";
$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (4, 'Banned', 'BANNED', '%%s', 'a:46:{s:10:\"board_view\";b:0;s:17:\"board_view_closed\";b:0;s:11:\"do_anything\";b:0;s:11:\"edit_avatar\";b:0;s:12:\"edit_profile\";b:0;s:8:\"edit_sig\";b:0;s:9:\"email_use\";b:0;s:10:\"forum_view\";b:0;s:8:\"is_admin\";b:0;s:10:\"pm_noflood\";b:0;s:11:\"poll_create\";b:0;s:9:\"poll_vote\";b:0;s:11:\"post_attach\";b:0;s:20:\"post_attach_download\";b:0;s:11:\"post_create\";b:0;s:11:\"post_delete\";b:0;s:15:\"post_delete_own\";b:0;s:9:\"post_edit\";b:0;s:13:\"post_edit_own\";b:0;s:18:\"post_inc_userposts\";b:0;s:12:\"post_noflood\";b:0;s:11:\"post_viewip\";b:0;s:14:\"search_noflood\";b:0;s:12:\"topic_create\";b:0;s:12:\"topic_delete\";b:0;s:16:\"topic_delete_own\";b:0;s:10:\"topic_edit\";b:0;s:14:\"topic_edit_own\";b:0;s:12:\"topic_global\";b:0;s:10:\"topic_lock\";b:0;s:14:\"topic_lock_own\";b:0;s:10:\"topic_move\";b:0;s:14:\"topic_move_own\";b:0;s:9:\"topic_pin\";b:0;s:13:\"topic_pin_own\";b:0;s:13:\"topic_publish\";b:0;s:18:\"topic_publish_auto\";b:0;s:11:\"topic_split\";b:0;s:15:\"topic_split_own\";b:0;s:12:\"topic_unlock\";b:0;s:16:\"topic_unlock_mod\";b:0;s:16:\"topic_unlock_own\";b:0;s:11:\"topic_unpin\";b:0;s:15:\"topic_unpin_own\";b:0;s:10:\"topic_view\";b:0;s:22:\"topic_view_unpublished\";b:0;}')";
$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (5, 'Awaiting Activation', 'AWAIT', '%%s', 'a:46:{s:10:\"board_view\";b:1;s:17:\"board_view_closed\";b:0;s:11:\"do_anything\";b:1;s:11:\"edit_avatar\";b:1;s:12:\"edit_profile\";b:1;s:8:\"edit_sig\";b:1;s:9:\"email_use\";b:0;s:10:\"forum_view\";b:1;s:8:\"is_admin\";b:0;s:10:\"pm_noflood\";b:0;s:11:\"poll_create\";b:0;s:9:\"poll_vote\";b:0;s:11:\"post_attach\";b:0;s:20:\"post_attach_download\";b:0;s:11:\"post_create\";b:0;s:11:\"post_delete\";b:0;s:15:\"post_delete_own\";b:0;s:9:\"post_edit\";b:0;s:13:\"post_edit_own\";b:0;s:18:\"post_inc_userposts\";b:0;s:12:\"post_noflood\";b:0;s:11:\"post_viewip\";b:0;s:14:\"search_noflood\";b:0;s:12:\"topic_create\";b:0;s:12:\"topic_delete\";b:0;s:16:\"topic_delete_own\";b:0;s:10:\"topic_edit\";b:0;s:14:\"topic_edit_own\";b:0;s:12:\"topic_global\";b:0;s:10:\"topic_lock\";b:0;s:14:\"topic_lock_own\";b:0;s:10:\"topic_move\";b:0;s:14:\"topic_move_own\";b:0;s:9:\"topic_pin\";b:0;s:13:\"topic_pin_own\";b:0;s:13:\"topic_publish\";b:0;s:18:\"topic_publish_auto\";b:0;s:11:\"topic_split\";b:0;s:15:\"topic_split_own\";b:0;s:12:\"topic_unlock\";b:0;s:16:\"topic_unlock_mod\";b:0;s:16:\"topic_unlock_own\";b:0;s:11:\"topic_unpin\";b:0;s:15:\"topic_unpin_own\";b:0;s:10:\"topic_view\";b:1;s:22:\"topic_view_unpublished\";b:0;}')";
$queries[] = "INSERT INTO %pgroups (group_id, group_name, group_type, group_format, group_perms) VALUES (6, 'Moderators', 'MODS', '<b>%%s</b>', 'a:46:{s:10:\"board_view\";b:1;s:17:\"board_view_closed\";b:0;s:11:\"do_anything\";b:1;s:11:\"edit_avatar\";b:1;s:12:\"edit_profile\";b:1;s:8:\"edit_sig\";b:1;s:9:\"email_use\";b:1;s:10:\"forum_view\";b:1;s:8:\"is_admin\";b:0;s:10:\"pm_noflood\";b:0;s:11:\"poll_create\";b:1;s:9:\"poll_vote\";b:1;s:11:\"post_attach\";b:1;s:20:\"post_attach_download\";b:1;s:11:\"post_create\";b:1;s:11:\"post_delete\";b:1;s:15:\"post_delete_own\";b:1;s:9:\"post_edit\";b:1;s:13:\"post_edit_own\";b:1;s:18:\"post_inc_userposts\";b:1;s:12:\"post_noflood\";b:0;s:11:\"post_viewip\";b:1;s:14:\"search_noflood\";b:1;s:12:\"topic_create\";b:1;s:12:\"topic_delete\";b:1;s:16:\"topic_delete_own\";b:1;s:10:\"topic_edit\";b:1;s:14:\"topic_edit_own\";b:1;s:12:\"topic_global\";b:1;s:10:\"topic_lock\";b:1;s:14:\"topic_lock_own\";b:1;s:10:\"topic_move\";b:1;s:14:\"topic_move_own\";b:1;s:9:\"topic_pin\";b:1;s:13:\"topic_pin_own\";b:1;s:13:\"topic_publish\";b:1;s:18:\"topic_publish_auto\";b:1;s:11:\"topic_split\";b:1;s:15:\"topic_split_own\";b:1;s:12:\"topic_unlock\";b:1;s:16:\"topic_unlock_mod\";b:1;s:16:\"topic_unlock_own\";b:1;s:11:\"topic_unpin\";b:1;s:15:\"topic_unpin_own\";b:1;s:10:\"topic_view\";b:1;s:22:\"topic_view_unpublished\";b:1;}')";

$queries[] = "INSERT INTO %pmembertitles (membertitle_id, membertitle_title, membertitle_posts, membertitle_icon) VALUES (1, 'Newbie', 0, '1.png')";
$queries[] = "INSERT INTO %pmembertitles (membertitle_id, membertitle_title, membertitle_posts, membertitle_icon) VALUES (2, 'Member', 25, '2.png')";
$queries[] = "INSERT INTO %pmembertitles (membertitle_id, membertitle_title, membertitle_posts, membertitle_icon) VALUES (3, 'Droplet', 100, '3.png')";
$queries[] = "INSERT INTO %pmembertitles (membertitle_id, membertitle_title, membertitle_posts, membertitle_icon) VALUES (4, 'Puddle', 250, '4.png')";
$queries[] = "INSERT INTO %pmembertitles (membertitle_id, membertitle_title, membertitle_posts, membertitle_icon) VALUES (5, 'Pool', 500, '5.png')";

$sets = array();
$settings = serialize($sets);
$queries[] = "INSERT INTO %psettings (settings_id, settings_data) VALUES (1, '{$settings}')";
$queries[] = "INSERT INTO %pskins (skin_name, skin_dir) VALUES ('QSF Comet', 'default')";
$queries[] = "INSERT INTO %pusers (user_id, user_name, user_group) VALUES (1, 'Guest', 3)";
?>