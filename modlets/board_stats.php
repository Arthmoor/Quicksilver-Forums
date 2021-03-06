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
 * Generate board statistics information
 *
 * @author Roger Libiez [Samson] 
 * @since 1.3.0
 **/
class board_stats extends modlet
{
	/**
	* Display the board statistics
	*
	* @param string $arg set to true to display birthdays list
	* @author Geoffrey Dunn <geoff@warmage.com>
	* @since 1.2.0
	* @return string HTML with current online users and total membercount
	**/
	function run( $arg )
	{
		if (!isset($this->qsf->lang->board_members)) {
			$this->qsf->lang->board();
		}

		$stats = $this->getStats();

		$this->qsf->lang->board_stats_string = sprintf($this->qsf->lang->board_stats_string,
		    $stats['MEMBERS'], "<a href=\"{$this->qsf->self}?a=profile&amp;w={$stats['LASTMEMBERID']}\">{$stats['LASTMEMBER']}</a>",
		    $stats['TOPICS'], $stats['REPLIES'], $stats['POSTS']);

		$this->qsf->lang->board_most_online = sprintf($this->qsf->lang->board_most_online, $stats['MOSTONLINE'], $stats['MOSTONLINETIME']);

		$bdays = "";
		if( $arg == "true" ) {
			$bdays = "<strong>{$this->qsf->lang->board_birthdays}</strong><br />\n" . $this->getuser_birthdays();
		}

		return eval($this->qsf->template('BOARD_STATS'));
	}

	/**
	 * Gets common board stats
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.0
	 * @return array Board stats: LASTMEMBEreplacement_id, LASTMEMBER, MEMBERS, TOPICS, POSTS, REPLIES, MOSTONLINE, MOSTONLINETIME
	 **/
	function getStats()
	{
		return array(
			'LASTMEMBERID'   => $this->qsf->sets['last_member_id'],
			'LASTMEMBER'     => $this->qsf->sets['last_member'],
			'MEMBERS'        => number_format($this->qsf->sets['members'], 0, null, $this->qsf->lang->sep_thousands),
			'TOPICS'         => number_format($this->qsf->sets['topics'], 0, null, $this->qsf->lang->sep_thousands),
			'POSTS'          => number_format($this->qsf->sets['posts'], 0, null, $this->qsf->lang->sep_thousands),
			'REPLIES'        => number_format($this->qsf->sets['posts'] - $this->qsf->sets['topics'], 0, null, $this->qsf->lang->sep_thousands),
			'MOSTONLINE'     => $this->qsf->sets['mostonline'],
			'MOSTONLINETIME' => $this->qsf->mbdate(DATE_LONG, $this->qsf->sets['mostonlinetime'])
		);
	}

	/**
	 * Makes a list of people whose birthday is today
	 *
	 * @author Jason Warner <jason@mercuryboard.com>
	 * @since Beta 2.1
	 * @return string List of members and their ages
	 **/
	function getuser_birthdays()
	{
		$links  = array();
		$members  = $this->qsf->db->query("SELECT user_id, user_name, user_birthday FROM %pusers WHERE user_birthday LIKE '%s'", $this->qsf->mbdate('%-m-d'));
		$count    = $this->qsf->db->num_rows($members);
	
		if (!$count) {
			return $this->qsf->lang->board_nobday;
		}
	
		while ($m = $this->qsf->db->nqfetch($members))
		{
			$year = explode('-', $m['user_birthday']);
			$day = $this->qsf->mbdate('Y') - $year[0];
			$links[] = "<a href=\"{$this->qsf->self}?a=profile&amp;w={$m['user_id']}\" class=\"bdaylink\">{$m['user_name']}</a> ($day)";
		}
		return implode(', ', $links);
	}
}
?>