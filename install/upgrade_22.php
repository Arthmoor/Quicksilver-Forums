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

// Upgrade from 1.3.0 to 1.3.1

// Template changes
$need_templates = array(
	// Added templates
	// Changed templates
	'FORUM_TOPICS_MAIN',
	'RECENT_TOPIC',
	'ADMIN_INSTALL_SKIN',
	'CP_PREFS'
	);

// Permission changes	
// $new_permissions['new_perm'] = true;

// Queries to run

// New Timezones

?>