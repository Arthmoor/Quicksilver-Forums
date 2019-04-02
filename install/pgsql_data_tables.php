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

$queries[] = "DROP SEQUENCE %pattach_id_seq";
$queries[] = "DROP SEQUENCE %pforums_id_seq";
$queries[] = "DROP SEQUENCE %pgroups_id_seq";
$queries[] = "DROP SEQUENCE %phelp_id_seq";
$queries[] = "DROP SEQUENCE %plogs_id_seq";
$queries[] = "DROP SEQUENCE %pmembertitles_id_seq";
$queries[] = "DROP SEQUENCE %ppmsystem_id_seq";
$queries[] = "DROP SEQUENCE %pposts_id_seq";
$queries[] = "DROP SEQUENCE %preplacements_id_seq";
$queries[] = "DROP SEQUENCE %psubscriptions_id_seq";
$queries[] = "DROP SEQUENCE %ptopics_id_seq";
$queries[] = "DROP SEQUENCE %pusers_id_seq";

$queries[] = "CREATE SEQUENCE %pattach_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %pforums_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %pgroups_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %phelp_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %plogs_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %pmembertitles_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %ppmsystem_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %pposts_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %preplacements_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %psubscriptions_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %ptopics_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";
$queries[] = "CREATE SEQUENCE %pusers_id_seq START 1 INCREMENT 1 MAXVALUE 2147483647 MINVALUE 1 CACHE 1";

$queries[] = "DROP TABLE %pactive";
$queries[] = "CREATE TABLE %pactive (
  active_id int4 NOT NULL default '0',
  active_ip varchar(40) NOT NULL default '127.0.0.1',
  active_user_agent varchar(100) NOT NULL default 'Unknown',
  active_action varchar(32) NOT NULL default '',
  active_item int4 NOT NULL default '0',
  active_time int4 NOT NULL default '0',
  active_session varchar(32) NOT NULL default '',
  UNIQUE (active_session, active_ip)
)"; //   UNIQUE KEY active_session (active_session),  UNIQUE KEY active_ip (active_ip)

$queries[] = "DROP TABLE %pattach";
$queries[] = "CREATE TABLE %pattach (
  attach_id int4 DEFAULT nextval('%pattach_id_seq') NOT NULL,
  attach_file varchar(32) NOT NULL default '',
  attach_name varchar(255) NOT NULL default '',
  attach_post int4 NOT NULL default '0',
  attach_downloads int4 NOT NULL default '0',
  attach_size int4 NOT NULL default '0',
  PRIMARY KEY  (attach_id, attach_post)
)";

$queries[] = "DROP TABLE %pforums";
$queries[] = "CREATE TABLE %pforums (
  forum_id int2 DEFAULT nextval('%pforums_id_seq') NOT NULL,
  forum_parent int2 NOT NULL default '0',
  forum_tree varchar(255) NOT NULL default '',
  forum_name varchar(255) NOT NULL default '',
  forum_position int2 NOT NULL default '0',
  forum_description varchar(255) NOT NULL default '',
  forum_topics int4 NOT NULL default '0',
  forum_replies int4 NOT NULL default '0',
  forum_lastpost int4 NOT NULL default '0',
  forum_subcat bool NOT NULL default '0',
  PRIMARY KEY (forum_id, forum_parent)
)";

$queries[] = "DROP TABLE %pgroups";
$queries[] = "CREATE TABLE %pgroups (
  group_id int2 DEFAULT nextval('%pgroups_id_seq') NOT NULL,
  group_name varchar(255) NOT NULL default '',
  group_type varchar(20) NOT NULL default '',
  group_format varchar(255) NOT NULL default '%%s',
  group_perms text NOT NULL default '',
  PRIMARY KEY  (group_id)
)";

$queries[] = "DROP TABLE %phelp";
$queries[] = "CREATE TABLE %phelp (
  help_id int2 DEFAULT nextval('%phelp_id_seq') NOT NULL,
  help_title varchar(255) NOT NULL default '',
  help_article text NOT NULL default '',
  PRIMARY KEY  (help_id)
)";

$queries[] = "DROP TABLE %plogs";
$queries[] = "CREATE TABLE %plogs (
  log_id int4 DEFAULT nextval('%plogs_id_seq') NOT NULL,
  log_user int4 NOT NULL default '0',
  log_time int4 NOT NULL default '0',
  log_action varchar(20) NOT NULL default '',
  log_data1 int4 NOT NULL default '0',
  log_data2 int2 NOT NULL default '0',
  log_data3 int2 NOT NULL default '0',
  PRIMARY KEY  (log_id)
)";

$queries[] = "DROP TABLE %pmembertitles";
$queries[] = "CREATE TABLE %pmembertitles (
  membertitle_id int2 DEFAULT nextval('%pmembertitles_id_seq') NOT NULL,
  membertitle_title varchar(50) NOT NULL default '',
  membertitle_posts int4 NOT NULL default '0',
  membertitle_icon varchar(25) NOT NULL default '',
  PRIMARY KEY  (membertitle_id, membertitle_posts)
)";

$queries[] = "DROP TABLE %ppmsystem";
$queries[] = "CREATE TABLE %ppmsystem (
  pm_id int4 DEFAULT nextval('%ppmsystem_id_seq') NOT NULL,
  pm_to int4 NOT NULL default '0',
  pm_from int4 NOT NULL default '0',
  pm_ip varchar(40) NOT NULL default '127.0.0.1',
  pm_bcc text NOT NULL default '',
  pm_title varchar(255) NOT NULL default '[No Title]',
  pm_time int4 NOT NULL default '0',
  pm_message text NOT NULL default '',
  pm_read bool NOT NULL default '0',
  pm_folder int2 NOT NULL default '0',
  PRIMARY KEY  (pm_id)
)"; //   KEY NewPMs (pm_to,pm_read,pm_folder)

$queries[] = "DROP TABLE %pposts";
$queries[] = "CREATE TABLE %pposts (
  post_id int4 DEFAULT nextval('%pposts_id_seq') NOT NULL,
  post_topic int4 NOT NULL default '0',
  post_author int4 NOT NULL default '0',
  post_emoticons bool NOT NULL default '1',
  post_mbcode bool NOT NULL default '1',
  post_count bool NOT NULL default '1',
  post_text text NOT NULL default '',
  post_time int4 NOT NULL default '0',
  post_icon varchar(32) NOT NULL default '',
  post_ip varchar(40) NOT NULL default '127.0.0.1',
  post_edited_by varchar(32) NOT NULL default '',
  post_edited_time int4 NOT NULL default '0',
  post_referrer tinytext,
  post_agent tinytext,
  PRIMARY KEY  (post_id)
)"; //   KEY Topic (post_topic),  FULLTEXT KEY post_text (post_text)

$queries[] = "DROP TABLE %preadmarks";
$queries[] = "CREATE TABLE %preadmarks (
  readmark_user int4 NOT NULL default '0',
  readmark_topic int4 NOT NULL default '0',
  readmark_lastread int4 NOT NULL default '0',
  PRIMARY KEY  (readmark_user,readmark_topic)
)";

$queries[] = "DROP TABLE %preplacements";
$queries[] = "CREATE TABLE %preplacements (
  replacement_id int4 DEFAULT nextval('%preplacements_id_seq') NOT NULL,
  replacement_search varchar(50) NOT NULL default '',
  PRIMARY KEY  (replacement_id)
)";

$queries[] = "DROP TABLE %psettings";
$queries[] = "CREATE TABLE %psettings (
  settings_id int2 NOT NULL default '0',
  settings_tos text NOT NULL default '',
  settings_meta_keywords text,
  settings_meta_description text,
  settings_data text NOT NULL default '',
  PRIMARY KEY  (settings_id)
)";

$queries[] = "DROP TABLE %pskins";
$queries[] = "CREATE TABLE %pskins (
  skin_name varchar(32) NOT NULL default '',
  skin_dir varchar(32) NOT NULL default '',
  PRIMARY KEY  (skin_dir)
)";

$queries[] = "DROP TABLE %pspam";
$queries[] = "CREATE TABLE %pspam (
  spam_id int4 DEFAULT nextval('%pspam_id_seq') NOT NULL,
  spam_topic int4 NOT NULL default '0',
  spam_author int4 NOT NULL default '0',
  spam_emoticons bool NOT NULL default '1',
  spam_mbcode bool NOT NULL default '1',
  spam_count bool NOT NULL default '1',
  spam_text text NOT NULL default '',
  spam_time int4 NOT NULL default '0',
  spam_icon varchar(32) NOT NULL default '',
  spam_ip varchar(40) NOT NULL default '127.0.0.1',
  spam_edited_by varchar(32) NOT NULL default '',
  spam_edited_time int4 NOT NULL default '0',
  spam_svars text,
  PRIMARY KEY  (spam_id)
)"; //   KEY Topic (post_topic),  FULLTEXT KEY post_text (post_text)

$queries[] = "DROP TABLE %psubscriptions";
$queries[] = "CREATE TABLE %psubscriptions (
  subscription_id int4 DEFAULT nextval('%psubscriptions_id_seq') NOT NULL,
  subscription_user int4 NOT NULL default '0',
  subscription_type varchar(10) NOT NULL default '',
  subscription_item int4 NOT NULL default '0',
  subscription_expire int4 NOT NULL default '0',
  PRIMARY KEY  (subscription_id)
)"; //   KEY subscription_item (subscription_item)

$queries[] = "DROP TABLE %ptemplates";
$queries[] = "CREATE TABLE %ptemplates (
  template_skin varchar(32) NOT NULL default 'default',
  template_set varchar(20) NOT NULL default '',
  template_name varchar(36) NOT NULL default '',
  template_html text NOT NULL default '',
  template_displayname varchar(255) NOT NULL default '',
  template_description varchar(255) NOT NULL default '',
  UNIQUE (template_name,template_skin)
)"; //   KEY Section (template_set,template_skin)

$queries[] = "DROP TABLE %ptopics";
$queries[] = "CREATE TABLE %ptopics (
  topic_id int4 DEFAULT nextval('%ptopics_id_seq') NOT NULL,
  topic_forum int2 NOT NULL default '0',
  topic_title varchar(75) NOT NULL default '0',
  topic_description varchar(35) NOT NULL default '',
  topic_starter int4 NOT NULL default '0',
  topic_last_post int4 NOT NULL default '0',
  topic_last_poster int4 NOT NULL default '0',
  topic_icon varchar(32) NOT NULL default '',
  topic_posted int4 NOT NULL default '0',
  topic_edited int4 NOT NULL default '0',
  topic_replies int4 NOT NULL default '0',
  topic_views int4 NOT NULL default '0',
  topic_modes int4 NOT NULL default '0',
  topic_moved int4 NOT NULL default '0',
  topic_poll_options text NOT NULL default '',
  PRIMARY KEY  (topic_id)
)"; //   KEY Forum (topic_forum)

$queries[] = "DROP TABLE %pusers";
$queries[] = "CREATE TABLE %pusers (
  user_id int4 DEFAULT nextval('%pusers_id_seq') NOT NULL,
  user_name varchar(255) NOT NULL default '',
  user_password varchar(32) NOT NULL default '',
  user_joined int4 NOT NULL default '0',
  user_level int2 NOT NULL default '1',
  user_title varchar(100) NOT NULL default '',
  user_title_custom bool NOT NULL default '0',
  user_group int2 NOT NULL default '2',
  user_skin varchar(32) NOT NULL default 'default',
  user_language varchar(6) NOT NULL default 'en',
  user_avatar varchar(150) NOT NULL default '',
  user_avatar_type varchar(20) NOT NULL default 'none',
  user_avatar_width int2 NOT NULL default '0',
  user_avatar_height int2 NOT NULL default '0',
  user_email varchar(100) NOT NULL default '',
  user_email_show bool NOT NULL default '0',
  user_email_form bool NOT NULL default '1',
  user_birthday date,
  user_timezone float(3,1) NOT NULL default '0.0',
  user_homepage varchar(255) NOT NULL default '',
  user_posts int4 NOT NULL default '0',
  user_location varchar(100) NOT NULL default '',
  user_icq int4 NOT NULL default '0',
  user_msn varchar(32) NOT NULL default '',
  user_aim varchar(32) NOT NULL default '',
  user_gtalk varchar(32) NOT NULL default '',
  user_pm bool NOT NULL default '1',
  user_pm_mail bool NOT NULL default '0',
  user_active bool NOT NULL default '1',
  user_yahoo varchar(100) NOT NULL default '',
  user_interests varchar(255) NOT NULL default '',
  user_signature text NOT NULL default '',
  user_lastvisit int4 NOT NULL default '0',
  user_lastallread int4 NOT NULL default '0',
  user_lastpost int4 NOT NULL default '0',
  user_lastpm int4 NOT NULL default '0',
  user_lastsearch int4 NOT NULL default '0',
  user_view_avatars bool NOT NULL default '1',
  user_view_signatures bool NOT NULL default '1',
  user_view_emoticons bool NOT NULL default '1',
  user_topics_page int2 NOT NULL DEFAULT '0',
  user_regip varchar(40) NOT NULL default '127.0.0.1',
  user_register_email varchar(100) default '',
  user_server_data text,
  user_posts_page int2 NOT NULL DEFAULT '0',
  user_perms text NOT NULL default '',
  PRIMARY KEY  (user_id)
)";

$queries[] = "DROP TABLE %pvotes";
$queries[] = "CREATE TABLE %pvotes (
  vote_user int4 NOT NULL default '0',
  vote_topic int4 NOT NULL default '0',
  vote_option int2 NOT NULL default '-1',
  PRIMARY KEY  (vote_user,vote_topic)
)";

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