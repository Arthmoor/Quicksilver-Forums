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
	exit('Use index.php to upgrade.');
}

// Upgrade from 1.4.2 to 1.5

// Template changes
$need_templates = array(
	// Added templates
	'ADMIN_LOGIN',
	'SPAM_CLEARALL',
	'SPAM_LIST',
	'SPAM_LIST_ENTRY',
	'STAT_GRAPH',
	'STAT_PAGE',
	'STAT_SPAM',
	'STAT_STAT',
	// Changed templates
	'REGISTER_MAIN',
	'REGISTER_IMAGE',
	'CP_PREFS',
	'ADMIN_INDEX',
	'MAIN',
	'BOARD_MAIN',
	'ADMIN_COPYRIGHT',
	'MAIN_COPYRIGHT',
	'MOD_EDIT_POST',
	'MOD_EDIT_GLOBAL',
	'MOD_EDIT_TOPIC',
	'MOD_MOVE_TOPIC',
	'MOD_SPLIT_SELECT',
	'MOD_SPLIT_TOPIC',
	'TOPIC_POSTER_MEMBER',
	'TOPIC_POST',
	'MEMBERS_MAIN',
	'MEMBERS_MEMBER',
	'TOPIC_MAIN',
	'PROFILE_MAIN',
	'POST_POLL',
	'POST_REPLY',
	'POST_TOPIC',
	'FORUM_TOPIC',
	'RSSFEED_FORUM',
	'ADMIN_EDIT_BOARD_SETTINGS',
	'ADMIN_MESSAGE',
	'ADMIN_BACKUP',
	'ADMIN_BAN_FORM',
	'ADMIN_CENSOR_FORM',
	'ADMIN_HOME',
	'ADMIN_TABLE',
	'ADMIN_ETABLE',
	'ADMIN_FORUM_ADD',
	'ADMIN_FORUM_EDIT',
	'ADMIN_FORUM_ORDER',
	'ADMIN_GROUP_EDIT',
	'ADMIN_MOD_LOGS',
	'ADMIN_MOD_LOGS_ENTRY',
	'ADMIN_MASS_MAIL',
	'ADMIN_MEMBER_EDIT',
	'ADMIN_MEMBER_PROFILE',
	'ADMIN_PRUNE_FORM',
	'ADMIN_PRUNE_TOPIC',
	'ADMIN_PRUNE_TOPICLIST',
	'ADMIN_EDIT_DB_SETTINGS',
	'ADMIN_ADD_TEMPLATE',
	'ADMIN_CSS_EDIT',
	'ADMIN_DELETE_TEMPLATE',
	'ADMIN_EDIT_SKIN',
	'ADMIN_EDIT_TEMPLATE',
	'ADMIN_EDIT_TEMPLATE_ENTRY',
	'ADMIN_INSTALL_SKIN',
	'ADMIN_LIST_TEMPLATES',
	'ADMIN_LIST_TEMPLATES_DELETE',
	'ADMIN_TEMPLATE_DELETE_CONTENTS',
	'ADMIN_TEMPLATE_ENTRY',
	'ADMIN_TITLE_ENTRY',
	'ADMIN_TITLE_ENTRY_MOD',
	'ADMIN_TITLE_FORM',
	'ADMIN_RSSREADER_MAIN',
	'ADMIN_RSSREADER_TITLE',
	'ACTIVE_MAIN',
	'ACTIVE_USER',
	'MAIN_QUICKLOGIN',
	'MAIN_HEADER_GUEST',
	'CP_AVATAR',
	'CP_EDIT_SIG',
	'CP_HOME',
	'CP_MAIN',
	'CP_PASS',
	'CP_PROFILE',
	'CP_SUB_MAIN',
	'CP_SUB_ROW',
	'EMAIL_MAIN',
	'FORUM_NO_TOPICS',
	'FORUM_SUBFORUM_MAIN',
	'PM_FOLDER',
	'PM_NO_MESSAGES',
	'PM_PREVIEW',
	'PM_SEND',
	'PM_VIEW',
	'POLL_MAIN',
	'POLL_OPTION',
	'POLL_RESULTS_ENTRY',
	'POLL_RESULTS_MAIN',
	'POST_BOX_PLAIN',
	'TOPIC_QUICKREPLY',
	'PROFILE_POST_INFO',
	'FORUM_TOPICS_MAIN',
	'POST_PREVIEW',
	'MAIN_TABLE',
	'MAIN_ETABLE',
	'MAIN_HEADER_MEMBER',
	'MAIN_MESSAGE',
	'MAIN_REMINDER',
	'BOARD_CATEGORY',
	'BOARD_CATEGORY_END',
	'BOARD_FORUM',
	'BOARD_STATS',
	'BOARD_USERS',
	'POST_ATTACH',
	'POST_MESSAGE_ICONS',
	'POST_OPTIONS',
	'POST_POSTER_MEMBER',
	'POST_POSTER_GUEST',
	'POST_REVIEW_ENTRY',
	'TOPIC_POSTER_GUEST',
	'TOPIC_POST_ATTACHMENT',
	'PROFILE_NO_POSTS',
	'HELP_DESCRIPTIVE_ENTRY',
	'HELP_FULL',
	'HELP_SIMPLE_ENTRY',
	'LOGIN_MAIN',
	'LOGIN_PASS',
	'RECENT_NO_TOPICS',
	'RECENT_TOPIC',
	'RSSFEED_ALL_POSTS',
	'RSSFEED_ERROR',
	'RSSFEED_ITEM',
	'RSSFEED_TOPIC',
	'SEARCH_MAIN',
	'SEARCH_RESULTS_ENTRY',
	'SEARCH_RESULTS_GUEST_INFO',
	'SEARCH_RESULTS_MAIN',
	'SEARCH_RESULTS_MEMBER_INFO',
	'SEARCH_RESULTS_POST'
	);

// New settings
$this->sets['analytics_id'] = '';
$this->sets['wordpress_api_key'] = '';
$this->sets['akismet_email'] = 0;
$this->sets['akismet_ureg'] = 0;
$this->sets['akismet_sigs'] = 0;
$this->sets['akismet_posts'] = 0;
$this->sets['akismet_posts_number'] = 5;
$this->sets['spam_post_count'] = 0;
$this->sets['spam_email_count'] = 0;
$this->sets['spam_reg_count'] = 0;
$this->sets['spam_sig_count'] = 0;
$this->sets['ham_count'] = 0;
$this->sets['spam_false_count'] = 0;
$this->sets['spam_pending'] = 0;

// Deleted settings
unset($this->sets['optional_modules']);
unset($this->sets['clickable_per_row']);
unset($this->sets['output_buffer']);
unset($this->sets['flash_avs']);

// Permission changes	

// Queries to run
$queries[] = "ALTER TABLE %pusers CHANGE user_timezone user_timezone FLOAT(3,1) NOT NULL DEFAULT '0.0'";
$queries[] = "UPDATE %pusers SET user_timezone=0.0";
$queries[] = "DROP TABLE IF EXISTS %ptimezones";
$queries[] = "ALTER TABLE %pusers ADD user_register_email varchar(100) default ''";
$queries[] = "ALTER TABLE %pusers ADD user_server_data text";
$queries[] = "ALTER TABLE %pposts ADD post_referrer tinytext";
$queries[] = "ALTER TABLE %pposts ADD post_agent tinytext";
$queries[] = "DELETE FROM %preplacements WHERE replacement_type = 'emoticon'";
$queries[] = "ALTER TABLE %preplacements DROP replacement_replace";
$queries[] = "ALTER TABLE %preplacements DROP replacement_type";
$queries[] = "ALTER TABLE %preplacements DROP replacement_clickable";
$queries[] = "DELETE FROM %ptemplates WHERE template_set = 'emot_control'";
$queries[] = "DELETE FROM %ptemplates WHERE template_name = 'POST_CLICKABLE_SMILIES'";
$queries[] = "DELETE FROM %ptemplates WHERE template_name = 'MAIN_MBCODE'";
$queries[] = "DELETE FROM %ptemplates WHERE template_name = 'POST_GLOBAL'";
$queries[] = "ALTER TABLE %psettings ADD settings_meta_keywords tinytext AFTER settings_tos";
$queries[] = "ALTER TABLE %psettings ADD settings_meta_description tinytext AFTER settings_meta_keywords";

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
  spam_ip INT UNSIGNED NOT NULL default '0',
  spam_edited_by varchar(32) NOT NULL default '',
  spam_edited_time int(10) unsigned NOT NULL default '0',
  spam_svars text,
  PRIMARY KEY  (spam_id),
  KEY Topic (spam_topic),
  FULLTEXT KEY spam_text (spam_text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
?>