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

// Upgrade from 1.5.1 to 1.5.2

// Template changes
$need_templates = array(
	// Added templates
	// Changed templates
	'ADMIN_EDIT_BOARD_SETTINGS',
	'PM_FOLDER',
	'STAT_SPAM'
	);

// New settings
$this->sets['akismet_profiles'] = 0;
$this->sets['spam_profile_count'] = 0;

// Deleted settings

// Permission changes	

// Queries to run
// Nice mess we made here! Time to clean it up. IPv6 support will be possible once this is done.
$db->query( 'ALTER TABLE %pactive CHANGE active_ip active_ip varchar(40) NOT NULL' );

$db->query( 'ALTER TABLE %ppmsystem CHANGE pm_ip pm_ip varchar(40) NOT NULL' );
$query = $db->query( 'SELECT pm_id, INET_NTOA(pm_ip) as pm_ip FROM %ppmsystem' );
while( $row = $db->nqfetch($query) )
{
	$db->query( "UPDATE %ppmsystem SET pm_ip='%s' WHERE pm_id=%d", $row['pm_ip'], $row['pm_id'] );
}

$db->query( 'ALTER TABLE %pposts CHANGE post_ip post_ip varchar(40) NOT NULL' );
$query = $db->query( 'SELECT post_id, INET_NTOA(post_ip) as post_ip FROM %pposts' );
while( $row = $db->nqfetch($query) )
{
	$db->query( "UPDATE %pposts SET post_ip='%s' WHERE post_id=%d", $row['post_ip'], $row['post_id'] );
}

$db->query( 'ALTER TABLE %pspam CHANGE spam_ip spam_ip varchar(40) NOT NULL' );
$query = $db->query( 'SELECT spam_id, INET_NTOA(spam_ip) as spam_ip FROM %pspam' );
while( $row = $db->nqfetch($query) )
{
	$db->query( "UPDATE %pspam SET spam_ip='%s' WHERE spam_id=%d", $row['spam_ip'], $row['spam_id'] );
}

$db->query( 'ALTER TABLE %pusers CHANGE user_regip user_regip varchar(40) NOT NULL' );
$query = $db->query( 'SELECT user_id, INET_NTOA(user_regip) as user_regip FROM %pusers' );
while( $row = $db->nqfetch($query) )
{
	$db->query( "UPDATE %pusers SET user_regip='%s' WHERE user_id=%d", $row['user_regip'], $row['user_id'] );
}
?>