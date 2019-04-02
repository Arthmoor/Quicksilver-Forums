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

// Upgrade from 1.3.1 to 1.4.0

// Template changes
$need_templates = true;

$this->sets['cookie_domain'] = '';
$this->sets['cookie_secure'] = 0;

// Permission changes	
$new_permissions['edit_profile'] = true;
$new_permissions['edit_avatar'] = true;
$new_permissions['edit_sig'] = true;

// Queries to run
$queries[] = "ALTER TABLE %pusers ADD user_pm_mail tinyint(1) NOT NULL default '0' AFTER user_pm";
$queries[] = "ALTER TABLE %pusers ADD user_regip INT UNSIGNED NOT NULL default '0' AFTER user_posts_page";
$queries[] = "UPDATE %ptimezones SET zone_updated=1";

// Required update for topic_posted setting
$db->query( "ALTER TABLE %ptopics ADD topic_posted int(10) unsigned NOT NULL DEFAULT '0' AFTER topic_icon" );
$query = $db->query( "SELECT * FROM %ptopics" );
while( $row = $db->nqfetch($query) )
{
	$topic_id = $row['topic_id'];
	if ($row['topic_moved']) {
		$topic_id = $row['topic_moved'];
	}
	// Ripped the code from update_last_post_topic in mod.php for this.
	$first = $db->fetch("
	SELECT
		p.post_id, p.post_time
	FROM
		%pposts p,
		%ptopics t
	WHERE
		p.post_topic=t.topic_id AND
		t.topic_id=%d
	ORDER BY
		p.post_time ASC
	LIMIT 1", $topic_id);

	$db->query("UPDATE %ptopics SET topic_posted=%d WHERE topic_id=%d", $first['post_time'], $topic_id);
}

// New Timezones

?>