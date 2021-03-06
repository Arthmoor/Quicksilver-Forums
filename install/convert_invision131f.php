<?php
/**
 * Quicksilver Forums
 * Copyright (c) 2005-2011 The Quicksilver Forums Development Team
 * https://github.com/Arthmoor/Quicksilver-Forums
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
 * Invisionboard 1.31f Conversion Script
 * Based on work by Yazinin Nick <admin@vk.net.ru>
 *
 * Roger Libiez [Samson] 
 *
 * Script tested on an unmodified Invisionboard 1.31f database.
 * Use with any other version is not advised!
 **/
class conversion
{
	public function __construct( $oldf, $newf )
	{
		$this->oldboard = &$oldf;
		$this->qsf = &$newf;
		$this->converter = 'Invisionboard 1.31f';
	}

	public function execute()
	{
		$this->convert_censors();
		$this->convert_members();
		$this->convert_pms();
		$this->convert_titles();
		$this->convert_forums();
		$this->convert_posts();

		$censor_count = $this->qsf->db->fetch( "SELECT COUNT(replacement_id) AS count FROM %preplacements" );
		$prof_count = $this->qsf->db->fetch( "SELECT COUNT(user_id) AS count FROM %pusers" );
		$pm_count = $this->qsf->db->fetch( "SELECT COUNT(pm_id) AS count FROM %ppmsystem" );
		$title_count = $this->qsf->db->fetch( "SELECT COUNT(membertitle_id) AS count FROM %pmembertitles" );
		$cat_count = $this->qsf->db->fetch( "SELECT COUNT(forum_id) AS count FROM %pforums WHERE forum_parent=0" );
		$forum_count = $this->qsf->db->fetch( "SELECT COUNT(forum_id) AS count FROM %pforums WHERE forum_parent != 0" );
		$topic_count = $this->qsf->db->fetch( "SELECT COUNT(topic_id) AS count FROM %ptopics" );
		$poll_count = $this->qsf->db->fetch( "SELECT COUNT(topic_id) AS count FROM %ptopics WHERE topic_modes & %d", TOPIC_POLL );
		$post_count = $this->qsf->db->fetch( "SELECT COUNT(post_id) AS count FROM %pposts" );

   		echo "
		    <span class='half'>
		     <div class='title'>Conversion Step</div>
		     Censored Words:<br />
		     Member Profiles:<br />
		     Private Messages:<br />
		     Member Titles:<br />
		     Forum Structure:
		    </span>

		    <span class='half'>
		     <div class='title'>Results</div>
		     {$censor_count['count']} censored words converted.<br />
		     {$prof_count['count']} member profiles converted.<br />
		     {$pm_count['count']} private messages converted.<br />
		     {$title_count['count']} member titles converted.<br />
		     {$cat_count['count']} categories converted.<br />
		     {$forum_count['count']} forums converted.<br />
		     {$topic_count['count']} topics converted.<br />
		     {$poll_count['count']} polls converted.<br />
		     {$post_count['count']} posts converted.<br />
		    </span>
		    <p></p>";
	}

	public function destroy_old_data()
	{
		$tb = $this->oldboard->db->query( "SHOW TABLES LIKE '%p%%'" );

		while( $tb1 = $this->oldboard->db->nqfetch($tb) )
		{
			foreach( $tb1 as $col => $data )
				$this->oldboard->db->query( "DROP TABLE IF EXISTS %s", $data );
		}
	}

	/*
	 * Remove all of the offending junky HTML Invisionboard encoded into message text.
	 * Thanks to the good people who frequent php.net for posting examples of how to use regex.
	 * It obviously helped alot, especially with Invisionboard's ugly non-validating HTML 4.01
	 */
	private function strip_invision_tags( $text )
	{
		// The [html] BBCode tag is not supported by Quicksilver Forums, so when the horrid mess is encountered in Invisionboard.....
		$text = preg_replace( "#<!--html-->(.*?)<!--html3-->#", "HTML BLOCK REMOVED", $text );

		// Convert emoticons....
		$text = preg_replace( "#<!--emo&(.+?)-->.+?<!--endemo-->#", "\\1" , $text );

		/* Convert image tags.
		 * We'll even be nice and fix malformed ones in posts.
		 */
		$text = preg_replace( "#<img src=[\"'](\S+?)['\"].+?".">#", "[img]\\1[/img]", $text );
		$text = preg_replace( '/\[img=(.*?)\](.*?)\[\/img\]/si', '[img]\\1[/img]', $text );

		// Convert email tags....
		$text = preg_replace( "#<a href=[\"']mailto:(.+?)['\"]>(.+?)</a>#", "[email=\\1]\\2[/email]", $text );

		// Convert URLs....
		$text = preg_replace( "#<a href=[\"'](.+?)['\"] target=[\"'](.+?)['\"]>(.+?)</a>#", "[url=\\1]\\3[/url]", $text );

		// Convert color tags....
		$text = preg_replace( "#<span style=[\'']color:(.+?)[\'']>(.+?)</span>#", "[color=\\1]\\2[/color]", $text );

		// Font tags are not desired, so they will be summarily parsed out....
		$text = preg_replace( "#<font(.+?)>#", "", $text );
		$text = str_replace ( "</font>", "", $text );

		// Span tags at this point are also not desired.
		$text = preg_replace( "#<span(.+?)>#", "", $text );
		$text = str_replace ( "</span>", "", $text );

		// Reconfigure code tags.
		$text = preg_replace( "#<!--c1-->(.+?)<!--ec1--><br>#", '[code]', $text );
		$text = preg_replace( "#<!--c1-->(.+?)<!--ec1-->#", '[code]', $text );
		$text = preg_replace( "#<!--c2-->(.+?)<!--ec2-->#", '[/code]', $text );

		// Reconfigure SQL tags
		$text = preg_replace( "#<!--sql-->(.+?)<!--sql1-->#", '[code]', $text );
		$text = preg_replace( "#<!--sql2-->(.+?)<!--sql3-->#", '[/code]', $text );

		// Strip flash movies and let people know about it.
		$text = preg_replace( "'<!--Flash[^>]*?>.*?<!--End Flash-->'si", "Flash image stripped during conversion.", $text );

	 	// Reconfigure quote tags.
		$text = preg_replace( "#<!--QuoteBegin-(.+?)<!--QuoteEBegin-->#", "[quote]", $text );
		$text = preg_replace( "#<!--QuoteEnd-->(.+?)<!--QuoteEEnd-->#", "[/quote]", $text );

		// Fix the text formatting tags....
		$text = preg_replace( "#<i>(.+?)</i>#is", "[i]\\1[/i]", $text );
		$text = preg_replace( "#<b>(.+?)</b>#is", "[b]\\1[/b]", $text );
		$text = preg_replace( "#<s>(.+?)</s>#is", "[s]\\1[/s]", $text );
		$text = preg_replace( "#<u>(.+?)</u>#is", "[u]\\1[/u]", $text );
		$text = str_replace( "<br>", "\n", $text );

		// Fix random junk in the post code....
		$text = str_replace( "&nbsp;", " ", $text );
		$text = str_replace( "&gt;", ">", $text );
		$text = str_replace( "&lt;", "<", $text );
		$text = str_replace( "&amp;", "&", $text );
		$text = str_replace( "&quot;", "\"", $text );
		$text = str_replace( "&#33;", "!", $text );
		$text = str_replace( "&#033;", "!", $text );
		$text = str_replace( "&#34;", "\"", $text );
		$text = str_replace( "&#36;", "$", $text );
		$text = str_replace( "&#036;", "$", $text );
		$text = str_replace( "&#37;", "\%", $text );
		$text = str_replace( "&#39;", "'", $text );
		$text = str_replace( "&#039;", "'", $text );
		$text = str_replace( "&#40;", "(", $text );
		$text = str_replace( "&#41;", ")", $text );
		$text = str_replace( "&#58;", ":", $text );
		$text = str_replace( "&#59;", ";", $text );
		$text = str_replace( "&#60;", "<", $text );
		$text = str_replace( "&#62;", ">", $text );
		$text = str_replace( "&#064;", "@", $text );
		$text = str_replace( "&#64;", "@", $text );
		$text = str_replace( "&#91;", "[", $text );
		$text = str_replace( "&#092;", "\\", $text );
		$text = str_replace( "&#92;", "\\", $text );
		$text = str_replace( "&#92", "\\", $text );
		$text = str_replace( "&#93;", "]", $text );
		$text = str_replace( "&#95;", "\_", $text );
		$text = str_replace( "&#124;", "|", $text );

		return $text;
	}

	private function convert_censors()
	{
		$this->qsf->db->query( "TRUNCATE %preplacements" );
		$result = $this->oldboard->db->query( "SELECT * FROM %pbadwords" );

  		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$this->qsf->db->query( "INSERT INTO %preplacements (replacement_search) VALUES( '%s' )", $row['type'] );
		}
	}

	private function convert_members()
	{
		$this->qsf->db->query( "TRUNCATE %pusers" );
		$this->qsf->db->query( "INSERT INTO %pusers (user_id, user_name, user_group) VALUES (1, 'Guest', 3)" );

 		$result = $this->oldboard->db->query( "SELECT * FROM %pmembers" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			if( $row['id'] == 0 )
				;
			else {
				$row['id']++;

				if( $row['hide_email'] == '' || $row['hide_email'] == 1 )
					$showmail = 0;
				else
					$showmail = 1;

				$row['name'] = $this->strip_invision_tags( $row['name'] );
				$row['email'] = $this->strip_invision_tags( $row['email'] );
				$row['website'] = $this->strip_invision_tags( $row['website'] );
				$row['location'] = $this->strip_invision_tags( $row['location'] );
				$row['interests'] = $this->strip_invision_tags( $row['interests'] );
				$row['signature'] = $this->strip_invision_tags( $row['signature'] );

				if( $row['last_visit'] == '' )
					$row['last_visit'] = $row['joined'];
				if( $row['last_activity'] == '' )
					$row['last_activity'] = $row['joined'];

				/* The default Invisionboard groups they claim you can never alter.
				 * Additional groups will not be converted. Members in these groups will become standard members.
				 */
				if( $row['mgroup'] == 1 )
					$row['mgroup'] = 5;
				else if( $row['mgroup'] == 2 )
					$row['mgroup'] = 3;
				else if( $row['mgroup'] == 3 )
					$row['mgroup'] = 2;
				else if( $row['mgroup'] == 4 )
					$row['mgroup'] = 1;
				else if( $row['mgroup'] == 5 )
					$row['mgroup'] = 4;
				else
					$row['mgroup'] = 2;

				$pos = strpos( $row['avatar'], '://' );
				if( $pos == 4 )
				{
					$avatar = $row['avatar'];
					$width = 100;
					$height = 100;
					$type = "url";
				}
				else
				{
					$avatar = '';
					$width = 0;
					$height = 0;
					$type = "none";
				}

				if( $row['bday_year'] != '' && $row['bday_month'] != '' && $row['bday_day'] != '' )
					$bday = sprintf( "%04d-%02d-%02d", $row['bday_year'], $row['bday_month'], $row['bday_day'] );
				else
					$bday = "0000-00-00";

				$icq = 0;
				if( $row['icq_number'] )
					$icq = intval( $row['icq_number'] );

				if( $row['ip_address'] == '' )
					$row['ip_address'] = '0.0.0.0';

 				$this->qsf->db->query( "INSERT INTO %pusers
					(user_id, user_name, user_password, user_joined, user_title, user_group, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_email, user_email_show, user_birthday, user_homepage, user_posts, user_location, user_icq, user_msn, user_aim, user_yahoo, user_interests, user_signature, user_lastvisit, user_lastpost, user_pm_mail, user_view_signatures, user_view_avatars, user_regip)
					VALUES( %d, '%s', '%s', %d, '%s', %d, '%s', '%s', %d, %d, '%s', %d, '%s', '%s', %d, '%s', %d, '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, INET_ATON( '%s' ) )",
					$row['id'], $row['name'], $row['password'], $row['joined'], $row['title'], $row['mgroup'], $avatar, $type, $width, $height, $row['email'], $showmail, $bday, $row['website'], $row['posts'], $row['location'], $icq, $row['msnname'], $row['aim_name'], $row['yahoo'], $row['interests'], $row['signature'], $row['last_visit'], $row['last_activity'], $row['email_pm'], $row['view_sigs'], $row['view_avs'], $row['ip_address'] );
			}
		}
	}

	private function convert_pms()
	{
		$this->qsf->db->query( "TRUNCATE %ppmsystem" );
		$result = $this->oldboard->db->query( "SELECT * FROM %pmessages" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			if( $row['vid'] == "in" || $row['vid'] == "sent" )
			{
				// Empty and N/A recipient IDs are no good!
				if( $row['recipient_id'] != '' && $row['recipient_id'] != 'N/A' )
				{
					$row['recipient_id']++;
					$row['from_id']++;

 					$row['title'] = $this->strip_invision_tags( $row['title'] );
					$row['message'] = $this->strip_invision_tags( $row['message'] );

					if( $row['vid'] == "in" )
						$folder = 0;
					else
						$folder = 1;

					$bcc = '';
					if( $folder == 1 )
					{
						$bcc = $row['recipient_id'];
						$row['recipient_id'] = $row['from_id'];
					}

					if( $row['title'] == '' )
						$row['title'] = "No Title";

					$this->qsf->db->query( "INSERT INTO %ppmsystem
						(pm_id, pm_to, pm_from, pm_bcc, pm_title, pm_time, pm_message, pm_read, pm_folder)
						VALUES( %d, %d, %d, '%s', '%s', %d, '%s', %d, %d )",
						$row['msg_id'], $row['recipient_id'], $row['from_id'], $bcc, $row['title'], $row['msg_date'], $row['message'], $row['read_state'], $folder );
				}
			}
		}
	}

	private function convert_titles()
	{
		$num = $this->oldboard->db->fetch( "SELECT COUNT(id) AS count FROM %ptitles" );

		if( $num['count'] > 0 )
		{
			$this->qsf->db->query( "TRUNCATE %pmembertitles" );

			$result = $this->oldboard->db->query( "SELECT * FROM %ptitles" );
			while( $row = $this->oldboard->db->nqfetch($result) )
			{
				if( $row['pips'] > 5 )
					$icon = '5.png';
				else if( $row['pips'] < 1 )
					$icon = '1.png';
				else
				{
					$icon = $row['pips'];
					$icon .= '.png';
				}
				$this->qsf->db->query( "INSERT INTO %pmembertitles
					(membertitle_id, membertitle_title, membertitle_posts, membertitle_icon)
					VALUES( %d, '%s', %d, '%s' )",
					$row['id'], $row['title'], $row['posts'], $icon );
			}
		}
	}

	private function convert_forums()
	{
		$this->qsf->db->query( "TRUNCATE %pforums" );

		$result = $this->oldboard->db->query( "SELECT * FROM %pcategories" );
		$fid = 1;
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			if( $row['name'] != '-' )
			{
				$forum_table[] = array( 'new_id' => $fid++, 'cat' => $row['id'], 'parent' => -2, 'name' => $row['name'], 'position' => $row['position'], 'description' => $row['description'], 'topics' => 0, 'replies' => 0, 'lastpost' => 0, 'old_id' => $row['id'], 'old_subcat' => 0 );
			}
		}

		$result = $this->oldboard->db->query( "SELECT * FROM %pforums" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			if( $row['last_post'] == '' )
				$row['last_post'] = 0;
			if( $row['sub_can_post'] == 0 )
				$subcat = 1;
			else
				$subcat = 0;
			$forum_table[] = array( 'new_id' => $fid++, 'cat' => $row['category'], 'parent' => $row['parent_id'], 'name' => $row['name'], 'position' => $row['position'], 'description' => $row['description'], 'topics' => $row['topics'], 'replies' => $row['posts'], 'lastpost' => $row['last_post'], 'old_id' => $row['id'], 'old_subcat' => $subcat );
		}

		for( $x = 0; $x < sizeof( $forum_table ); $x++ )
		{
			$cat = $forum_table[$x]['cat'];
			$parent = $forum_table[$x]['parent'];
			$name = $forum_table[$x]['name'];
			$name = $this->strip_invision_tags( $name );
			$position = $forum_table[$x]['position'];
			$description = $forum_table[$x]['description'];
			$description = $this->strip_invision_tags( $description );
			$topics = $forum_table[$x]['topics'];
			$replies = $forum_table[$x]['replies'];
			$last_post = $forum_table[$x]['lastpost'];
			$id = $forum_table[$x]['old_id'];
			$subcat = $forum_table[$x]['old_subcat'];

			if( $parent == -2 )
				$parent = 0;
			else if( $parent == -1 )
			{
				for( $y = 0; $y < sizeof( $forum_table ); $y++ )
				{
					if( $forum_table[$y]['old_id'] == $cat && $forum_table[$y]['parent'] == -2 )
					{
						$parent = $forum_table[$y]['new_id'];
						break;
					}
				}
			}
			else
			{
				for( $y = 0; $y < sizeof( $forum_table ); $y++ )
				{
					if( $forum_table[$y]['old_id'] == $parent && $forum_table[$y]['parent'] == -1 )
					{
						$parent = $forum_table[$y]['new_id'];
						break;
					}
				}
			}

			$this->qsf->db->query( "INSERT INTO %pforums
				(forum_parent, forum_name, forum_position, forum_description, forum_topics, forum_replies, forum_lastpost, forum_subcat)
				VALUES( %d, '%s', %d, '%s', %d, %d, %d, %d )",
				$parent, $name, $position, $description, $topics, $replies, $last_post, $subcat );
		}

		$this->qsf->db->query( "TRUNCATE %ptopics" );
		$result = $this->oldboard->db->query( "SELECT * FROM %ptopics" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$row['starter_id']++;
			$row['last_poster_id']++;

			$topic_modes = TOPIC_PUBLISH;
			if( $row['state'] == 'closed' )
				$topic_modes = ($topic_modes | TOPIC_LOCKED);
			if( $row['pinned'] == 1 )
				$topic_modes = ($topic_modes | TOPIC_PINNED);
			if( $row['poll_state'] == 'open' )
				$topic_modes = ($topic_modes | TOPIC_POLL);

			$row['title'] = $this->strip_invision_tags( $row['title'] );
			$row['description'] = $this->strip_invision_tags( $row['description'] );

			$fid = 0;
			for( $x = 0; $x < sizeof( $forum_table ); $x++ )
			{
				if( $row['forum_id'] == $forum_table[$x]['old_id'] && $forum_table[$x]['parent'] > -2 )
				{
					$fid = $forum_table[$x]['new_id'];
					break;
				}
			}
			$this->qsf->db->query( "INSERT INTO %ptopics
				(topic_id, topic_forum, topic_title, topic_description, topic_starter, topic_last_poster, topic_last_post, topic_posted, topic_replies, topic_views, topic_modes)
				VALUES( %d, %d, '%s', '%s', %d, %d, %d, %d, %d, %d, %d )",
				$row['tid'], $fid, $row['title'], $row['description'], $row['starter_id'], $row['last_poster_id'], $row['last_post'], $row['start_date'], $row['posts'], $row['views'], $topic_modes );
		}

		$this->qsf->db->query( "TRUNCATE %psubscriptions" );
		$result = $this->oldboard->db->query( "SELECT * FROM %pforum_tracker" );
		$frows = 0;
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$row['member_id']++;

			$fid = 0;
			for( $x = 0; $x < sizeof( $forum_table ); $x++ )
			{
				if( $row['forum_id'] == $forum_table[$x]['old_id'] )
				{
					$fid = $forum_table[$x]['new_id'];
					break;
				}
			}
			$expire = time() + 2592000;
			$this->qsf->db->query( "INSERT INTO %psubscriptions
				(subscription_id, subscription_user, subscription_type, subscription_item, subscription_expire)
				VALUES( %d, %d, 'forum', %d, %d )", $row['frid'], $row['member_id'], $fid, $expire );
			if( $row['frid'] > $frows )
				$frows = $row['frid'];
		}

		$result = $this->oldboard->db->query( "SELECT * FROM %ptracker" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$row['member_id']++;

			$lineid = $row['trid'] + $frows;
			$expire = time() + 2592000;
			$this->qsf->db->query( "INSERT INTO %psubscriptions
				(subscription_id, subscription_user, subscription_type, subscription_item, subscription_expire)
				VALUES( %d, %d, 'topic', %d, %d )", $lineid, $row['member_id'], $row['topic_id'], $expire );
		}

		$result = $this->oldboard->db->query( "SELECT * FROM %ppolls" );
		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$this->qsf->db->query( "UPDATE %ptopics SET topic_poll_options='%s' WHERE topic_id=%d", $row['choices'], $row['tid'] );
		}

		$this->qsf->db->query( "TRUNCATE %pvotes" );
		$result = $this->oldboard->db->query( "SELECT * FROM %pvoters" );

		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$row['member_id']++;
			$this->qsf->db->query( "INSERT INTO %pvotes (vote_user, vote_topic) VALUES( %d, %d )", $row['member_id'], $row['tid'] );
		}
	}

	private function convert_posts()
	{
		$this->qsf->db->query( "TRUNCATE %pposts" );
		$num = $this->oldboard->db->fetch( "SELECT COUNT(pid) AS count FROM %pposts" );

		$result = $this->oldboard->db->query( "SELECT * FROM  %pposts" );

		while( $row = $this->oldboard->db->nqfetch($result) )
		{
			$row['author_id']++;

			$row['post'] = $this->strip_invision_tags( $row['post'] );

			$this->qsf->db->query( "INSERT INTO %pposts
				(post_id, post_topic, post_author, post_emoticons, post_text, post_time, post_ip, post_edited_by, post_edited_time)
				VALUES( %d, %d, %d, %d, '%s', %d, INET_ATON('%s'), '%s', %d )",
				$row['pid'], $row['topic_id'], $row['author_id'], $row['use_emo'], $row['post'], $row['post_date'], $row['ip_address'], $row['edit_name'], $row['edit_time'] );
		}
	}
}
?>