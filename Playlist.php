<?php

require_once(dirname(__FILE__).'/PlaylistItem.php');

class Playlist
{
	var $id;
	var $name;
	var $priority;
	var $published;

	function Playlist()
	{
		$this->id = null;
		$this->name = null;
		$this->priority = 0;
		$this->published = 0;
	}
	
	function initFromDb($elem)
	{
		if (isset($this))
		{
			$this->id = $elem->id;
			$this->name = $elem->name;
			$this->priority = $elem->priority;
			$this->published = $elem->published;
		}
	}
	
	function save()
	{
		global $wpdb;
		if ($this->name <> null && $this->name <> '')
		{
			if ($this->id == null) {
				$sql = "INSERT INTO {$wpdb->playlists} (name,priority,created) VALUES ('{$this->name}', {$this->priority}, NOW())";
			} else {
				$sql = "UPDATE {$wpdb->playlists} SET name='{$this->name}', priority={$this->priority}, published={$this->published} WHERE id={$this->id}";
			}
			$wpdb->query($sql);
		}
	}
	
	function getItems()
	{
		return PlaylistItem::getAll($this->id);
	}

	// Static methods
	function getAll($published_only = false)
	{
		global $wpdb;
		
		$ret = array();
		
		if ($published_only) {
			$sql = "SELECT * FROM {$wpdb->playlists} WHERE published = 1 ORDER BY priority DESC, created DESC";
		} else {
			$sql = "SELECT * FROM {$wpdb->playlists} ORDER BY priority DESC, created DESC";
		}
		$result = $wpdb->get_results($sql);
		
		foreach($result as $elem)
		{
			$pl = new Playlist();
			$pl->initFromDb($elem);
			$ret[] = $pl;
		}
		
		return $ret;
	}
	
	function getFromIndex($id)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->playlists} WHERE id = $id";
		$result = $wpdb->get_row($sql);
		
		$pl = new Playlist();
		$pl->initFromDb($result);
		
		return $pl;
	}
	
	function getLast($published_only = false)
	{
		global $wpdb;
		
		if ($published_only) {
			$sql = "SELECT * FROM {$wpdb->playlists} WHERE published = 1 ORDER BY priority DESC, created DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM {$wpdb->playlists} ORDER BY priority DESC, created DESC LIMIT 1";
		}
		$result = $wpdb->get_row($sql);	

		$pl = new Playlist();
		$pl->initFromDb($result);
		
		return $pl;
	}
	
	function delete($id)
	{
		global $wpdb;
		
		$sql = "DELETE FROM {$wpdb->playlist_items} WHERE playlist_id = $id";
		$wpdb->query($sql);
		
		$sql = "DELETE FROM {$wpdb->playlists} WHERE id = $id";
		$wpdb->query($sql);
	}
}

?>