<?php

class PlaylistItem
{
	var $id;
	var $playlist_id;
	var $artist;
	var $title;
	var $album;
	
	function PlaylistItem()
	{
		$this->id = null;
	}
	
	function initFromDb($elem)
	{
		if (isset($this))
		{
			$this->id = $elem->id;
			$this->playlist_id = $elem->playlist_id;
			$this->artist = $elem->artist;
			$this->title = $elem->title;
			$this->album = $elem->album;
		}
	}
	
	function save()
	{
		global $wpdb;
		if ($this->title <> null && $this->title <> '')
		{
			if ($this->id == null) {
				$sql = "INSERT INTO {$wpdb->playlist_items} (playlist_id,artist,title,album) VALUES ({$this->playlist_id}, '{$this->artist}', '{$this->title}', '{$this->album}')";
			} else {
				$sql = "REPLACE INTO {$wpdb->playlist_items} (id,playlist_id,artist,title,album) VALUES ({$this->id}, {$this->playlist_id}, '{$this->artist}', '{$this->title}', '{$this->album}')";
			}
			$wpdb->query($sql);
		}
	}
	
	// Static methods
	function getAll($pl_id = null)
	{
		global $wpdb;

		$ret = array();
		
		if ($pl_id <> null) {
			$sql = "SELECT * FROM {$wpdb->playlist_items} WHERE playlist_id = $pl_id ORDER BY title";
		} else {
			$sql = "SELECT * FROM {$wpdb->playlist_items} ORDER BY title";
		}
		$result = $wpdb->get_results($sql);
		
		foreach($result as $elem)
		{
			$pl = new PlaylistItem();
			$pl->initFromDb($elem);
			$ret[] = $pl;
		}
		
		return $ret;
	}
	
	function getFromIndex($id)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->playlist_items} WHERE id = $id";
		$result = $wpdb->get_row($sql);
		
		$pl = new PlaylistItem();
		$pl->initFromDb($result);
		
		return $pl;
	}
	
	function delete($id)
	{
		global $wpdb;
		
		$sql = "DELETE FROM {$wpdb->playlist_items} WHERE id = $id";
		$wpdb->query($sql);
	}
}

?>