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

/**
 * Generate the list of active users
 *
 * @author Roger Libiez [Samson] 
 * @since 1.3.0
 **/
class users_online extends modlet
{
	/**
	* Display a listing of who is online.
	*
	* @param string $arg set to "true" to generate the onlinetoday list
	* @author Geoffrey Dunn <geoff@warmage.com>
	* @since 1.2.0
	* @return string HTML with current online users and total membercount
	**/
	function run( $arg )
	{
		if (!isset($this->qsf->lang->board_members)) {
			$this->qsf->lang->board();
		}

		$users = $this->doActive();

		$userlist = "";
		if( $arg == "true" ) {
			$this->userlist = $this->usersonline();
			$userlist = "<br /><hr />" . $this->userlist['TITLEONTABLE'] . "<br />" . $this->userlist['USERNAMES'];
		}

		$link = "<a href=\"{$this->qsf->self}?a=active\" class=\"activeusers\">{$this->qsf->lang->board_active_users}</a>";
		if( $this->qsf->perms->is_guest )
			$link = $this->qsf->lang->board_active_users;

		return eval($this->qsf->template('BOARD_USERS'));
	}

	/**
	 * Formats list of active users
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return array Active users: USERS, MEMBERCOUNT, GUESTCOUNT, TOTALCOUNT
	 **/
	function doActive()
	{
		/**
		 * If it exists, perhaps do something like UPDATE ... SELECT
		 */

		$Active = $this->getActive();
		if ($Active) {
			$Active = implode(', ', $Active);
		} else {
			$Active = $this->qsf->lang->board_nobody;
		}
		
		$OnGuests = $this->qsf->activeutil->get_guests_online();
		$OnMembers = $this->qsf->activeutil->get_members_online();
		$OnTotal = $OnMembers + $OnGuests;

		if ($OnTotal > $this->qsf->sets['mostonline']) {
			$this->qsf->sets['mostonline']     = $OnTotal;
			$this->qsf->sets['mostonlinetime'] = $this->qsf->time;
			$this->qsf->write_sets();
		}

		return array(
			'USERS'       => $Active,
			'MEMBERCOUNT' => $OnMembers,
			'GUESTCOUNT'  => $OnGuests,
			'TOTALCOUNT'  => $OnTotal
		);
	}

	/**
	 * Makes list of active users and filters out inactive ones - see doActive()
	 *
	 * @access protected
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return array Array of active members
	 **/
	function getActive()
	{
		$allusers = array();
		$allnames = array();
		$all_active_users = $this->qsf->activeutil->get_active();

		foreach ($all_active_users as $user)
		{
			if (($user['id'] != USER_GUEST_UID || $user['bot']) && !in_array($user['name'], $allnames)) {
				if ($user['bot']) {
					$allusers[] = $user['name'];
				} else {
					$allusers[] = "<a {$user['link']} title=\"{$user['title']}\">{$user['name']}</a>";
				}
				$allnames[] = $user['name'];
			}
		}
		return $allusers;
	}

	function usersonline()
	{
		$which_day = gmdate("d F Y", $this->qsf->time);
		$today_date = strtotime("$which_day");

		$query = $this->qsf->db->query( "SELECT user_id, user_name, user_lastvisit FROM %pusers
			WHERE user_lastvisit >= %d AND user_name NOT LIKE 'Guest' ORDER BY user_name",
			$today_date);

		$count_users = $this->qsf->db->num_rows($query);
		$user_names = '';

		if($count_users == '0') {
			$title_onlinetd_table = '<b>There have been no members online today.</b>';
		} else {
			$i = 0;
			while( $row = $this->qsf->db->nqfetch($query) )
			{
				$user_id = $row['user_id'];
				$user_name = $row['user_name'];

				if ($i == ($count_users - 1)) {
					$comma = "";
				} else {
					$comma = ", ";
				}

               			if($count_users == '1') {
					$title_onlinetd_table = "<b>There has been " . $count_users . " member online today:</b>";
				}
				if($count_users > '1') {
					$title_onlinetd_table = "<b>There have been " . $count_users . " members online today:</b>";
				}
				$user_names = $user_names . "<a href='{$this->qsf->self}?a=profile&amp;w=$user_id' class='small'>" . $user_name . "</a>" . $comma;
			}
		}
		return array( 'TITLEONTABLE'  => $title_onlinetd_table, 'USERNAMES' => $user_names );
	}
}
?>