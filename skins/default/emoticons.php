<?php
/**
 *
 * Quicksilver Forums
 * Copyright (c) 2005-2011 The Quicksilver Forums Development Team
 * http://code.google.com/p/quicksilverforums/
 * 
 * MercuryBoard
 * Copyright (c) 2001-2006 The Mercury Development Team
 * http://www.mercuryboard.com/
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

// This file defines the emoticons for the Ashlander 3 skin.

// Left side = Text to replace. Ex: ":alien:" would be what the user types in.
// Right side = Image file to replace the text with. Ex: ":alien:" turns into alien.gif.
// Alt text is not necessary, so if you don't want it, make it blank, like so: alt=""
// All graphic files must go into http://yoururl.com/skins/Default/emoticons/

$this->emotes['click_replacement'][':cool:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/cool.gif" alt=":cool:" />';
$this->emotes['click_replacement'][':cyclops:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/cyclops.gif" alt=":cyclops:" />';
$this->emotes['click_replacement'][':mad:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/mad.gif" alt=":mad:" />';
$this->emotes['click_replacement'][':rolleyes:']	= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/rolleyes.gif" alt=":rolleyes:" />';
$this->emotes['click_replacement'][':sad:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/sad.gif" alt=":sad:" />';
$this->emotes['click_replacement'][':smile:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/smile.gif" alt=":smile:" />';
$this->emotes['click_replacement'][':smirk:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/smirk.gif" alt=":smirk:" />';
$this->emotes['click_replacement'][':stare:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/stare.gif" alt=":stare:" />';
$this->emotes['click_replacement'][':surprised:']	= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/surprised.gif" alt=":surprised:" />';
$this->emotes['click_replacement'][':thinking:']	= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/thinking.gif" alt=":thinking:" />';
$this->emotes['click_replacement'][':tongue:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/tongue.gif" alt=":tongue:" />';
$this->emotes['click_replacement'][':wink:']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/wink.gif" alt=":wink:" />';

// Put emoticon replacements that aren't clickable here. They will still be replaced if the user types them in manually.

$this->emotes['replacement'][':)']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/smile.gif" alt=":)" />';
$this->emotes['replacement'][':(']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/sad.gif" alt=":(" />';
$this->emotes['replacement'][':P']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/tongue.gif" alt=":P" />';
$this->emotes['replacement'][';)']		= '<img src="' . $this->settings['loc_of_board'] . 'skins/' . $this->skin . '/emoticons/wink.gif" alt=";)" />';

?>