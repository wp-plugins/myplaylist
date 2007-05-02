<?php
/*
Plugin Name: MyPlaylist
Plugin URI: http://www.mog-soft.org
Description: Allow user to create and display playlists of songs
Author: Leo Cacheux
Version: 1.0
Author URI: http://leo.cacheux.net
*/
/*  Copyright 2007  Leo Cacheux  (email : leo@cacheux.net)
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! class_exists( 'MyPlaylist' ) ) :

require_once(dirname(__FILE__).'/Playlist.php');

class MyPlaylist
{
	var $settings = array();
	var $table_version = 1;
	var $added_tables = false;

	function MyPlaylist()
	{
		if (isset($this))
		{
			$this->settings = get_settings('myplaylist');
			$this->register_tables();

			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_filter('the_content', array(&$this, 'insert_playlist'));

			if ($this->settings['table_version'] != $this->table_version) {
				//$this->drop_tables();
				$this->make_tables();
				$this->added_tables = true;
				update_option('myplaylist', $this->settings);
			}
		}
	}

	function register_tables()
	{
		global $wpdb;
		$wpdb->playlists = "{$wpdb->prefix}playlists";
		$wpdb->playlist_items = "{$wpdb->prefix}playlist_items";
	}

	function make_tables() {
		global $wpdb;
		
		if(!include_once(ABSPATH . 'wp-admin/upgrade-functions.php'))
			die(_e('There is was error adding the required tables to the database. ', 'MyPlaylist'));
		
		$sql = "CREATE TABLE {$wpdb->playlists}
				( id INTEGER NOT NULL AUTO_INCREMENT,
					name VARCHAR(128) NOT NULL,
					priority INTEGER DEFAULT '0' NOT NULL,
					created DATETIME,
					published BOOL,
					UNIQUE KEY id (id)
				) TYPE=MyISAM";
		dbDelta($sql);
		
		$sql = "CREATE TABLE {$wpdb->playlist_items}
				( id INTEGER NOT NULL AUTO_INCREMENT,
					playlist_id INTEGER NOT NULL,
					artist VARCHAR(128),
					title VARCHAR(128) NOT NULL,
					album VARCHAR(128),
					UNIQUE KEY id (id)
				) TYPE=MyISAM";
		dbDelta($sql);
		
		$this->settings['table_version'] = $this->table_version;
		$this->settings['display_album'] = false;
		$this->settings['theme_pattern'] = "<em>%title%</em> - <strong>%artist%</strong>";
		$this->settings['post_pattern'] = "<em>%title%</em> - <strong>%artist%</strong>";
		
		$this->save_options();
	}
	
	function save_options()
	{
		update_option('myplaylist', $this->settings);
	}
	
	function admin_menu()
	{
		if (function_exists('add_options_page')) {
			add_options_page('MyPlaylist', 'MyPlaylist', 8, "options-general.php?page=myplaylist/admin.php");
		}
	}
	
	function insert_playlist($content)
	{
		if (preg_match("/\{playlist_([0-9]*)\}/", $content, $matches)) {
			$playlist = Playlist::getFromIndex($matches[1]);
			$pl_content = "<ul>";
			foreach ($playlist->getItems() as $item)
			{
				$pl_content .= "<li>".str_replace(
					array('%artist%', '%album%', '%title%'),
					array($item->artist, $item->album, $item->title),
					$this->settings['post_pattern'])."</li>\n";
			}
			$pl_content .= "</ul>\n";
			return preg_replace("/\{playlist_(.*)\}/", $pl_content, $content);
		} else return $content;
	}
}

endif;

$myplaylist = new MyPlaylist();

function display_playlist($playlist)
{
	global $myplaylist;
	$text =  "<ul>\n";
	$items = $playlist->getItems();
	foreach ($items as $item) {
		$text .= "<li>".str_replace(
					array('%artist%', '%album%', '%title%'),
					array($item->artist, $item->album, $item->title),
					$myplaylist->settings['theme_pattern'])."</li>\n";
	}
	$text .= "</ul>\n";
	
	return $text;
}

function get_last_playlist()
{
	echo display_playlist(Playlist::getLast(true));
}

function get_playlist($id)
{
	echo display_playlist(Playlist::getFromIndex($id));
}

function get_playlists($limit = 1000)
{
	$playlists = Playlist::getAll(true);
	$i = 0;
	echo "<ul>\n";
	foreach($playlists as $pl)
	{
		if ($i < $limit) {
			echo "<li>".$pl->name."</li>\n";
		}
		$i++;
	}
	echo "</ul>\n";
}

?>