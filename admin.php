<?php

require_once(dirname(__FILE__).'/Playlist.php');

if (is_plugin_page()) :

if (isset($_POST['action']))
{
	if ( $_POST['action'] == 'options' ) {
		$myplaylist->settings['theme_pattern'] = $_POST['themepattern'];
		$myplaylist->settings['post_pattern'] = $_POST['postpattern'];
		$myplaylist->save_options();
		//update_option('myplaylist',$myplaylist->settings);
	}

	if ( $_POST['action'] == 'create' && isset($_POST['newname']) && $_POST['newname'] <> '' ) {
		$playlist = new Playlist();
		$playlist->name = $_POST['newname'];
		$playlist->save();
	}

	if ( $_POST['action'] == 'additem' && isset($_POST['title']) && $_POST['title'] <> '' ) {
		$plitem = new PlaylistItem();
		$plitem->artist = $_POST['artist'];
		$plitem->title = $_POST['title'];
		$plitem->album = $_POST['album'];
		$plitem->playlist_id = $_GET['edit'];
		$plitem->save();
	}
	
	if ( $_POST['action'] == 'edititem' && isset($_POST['title']) && $_POST['title'] <> '' ) {
		$plitem = PlaylistItem::getFromIndex($_POST['itemid']);
		$plitem->artist = $_POST['artist'];
		$plitem->title = $_POST['title'];
		$plitem->album = $_POST['album'];
		$plitem->save();
	}
	
	if ( $_POST['action'] == 'edit' && isset($_POST['name']) && $_POST['name'] <> '' ) {
		$playlist = Playlist::getFromIndex($_GET['edit']);
		$playlist->name = $_POST['name'];
		$playlist->priority = $_POST['priority'];
		if ($_POST['published']=='on')
			$playlist->published = 1;
		else
			$playlist->published = 0;
		
		$playlist->save();
	}
}

if (isset($_GET['delpl']))
{
	Playlist::delete($_GET['delpl']);
}

if (isset($_GET['delitem']))
{
	PlaylistItem::delete($_GET['delitem']);
}

?>
<div class="wrap">
<h2>MyPlaylist Options</h2>

<p>Create and edit playlists to display. To insert a playlist in a post, add <code>{playlist_id}</code>, where <code>id</code> is the id number associated to a playlist. You can find them below.</p>

<h3>General options</h3>

<form name="generaloptions" method="post">
<input type="hidden" name="action" value="options" />
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr>
		<td width="33%" valign="top" scope="row">Theme pattern</td>
		<td><input name="themepattern" type="text" id="themepattern" value="<?php echo $myplaylist->settings['theme_pattern']; ?>" /></td>
	</tr>
	<tr>
		<td width="33%" valign="top" scope="row">Posts pattern</td>
		<td><input name="postpattern" type="text" id="postpattern" value="<?php echo $myplaylist->settings['post_pattern']; ?>" /></td>
	</tr>
</table>
<br/><code>%artist%, %album% and %title% will be replaced by values</code>
<br/><br/><input type="submit" name="Submit" value="Edit options" />
</form>

<br/>

<h3>Create a new playlist</h3>
<form name="createplaylist" method="post" >
<input type="hidden" name="action" value="create" />
<p>Name <input name="newname" type="text" id="newname" /><input type="submit" name="Submit" value="Create new &raquo;" /></p>
</form>

<br/>

<h3>Edit an existing playlist</h3>
<ul>
<?php
	$lists = Playlist::getAll();
	foreach ($lists as $list) {
		echo "<li>".$list->id.": <a href=\"".$_SERVER['PHP_SELF']."?page=myplaylist/admin.php&edit={$list->id}\">".$list->name."</a> (<a href=\"".$_SERVER['PHP_SELF']."?page=myplaylist/admin.php&delpl={$list->id}\" onclick=\"return window.confirm('Really delete playlist ?');\">delete</a>)</li>\n";
	}
?>
</ul>

<?php
if (isset($_GET['edit'])) {
	$playlist = Playlist::getFromIndex($_GET['edit']);
	echo "<br/><h3>Edit playlist &laquo; {$playlist->name} &raquo;</h3>\n";
	
	echo "<form name=\"editplaylist\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"edit\" />\n";
	echo "<input name=\"name\" type=\"text\" id=\"name\" value=\"{$playlist->name}\" />\n";
	echo "&nbsp;&nbsp;Priority : <input name=\"priority\" type=\"text\" id=\"priority\" value=\"{$playlist->priority}\" size=\"3\" />\n";
	echo "<input name=\"published\" type=\"checkbox\" id=\"published\" ".($playlist->published?'checked="checked"':'')."/> Published\n";
	echo "<input type=\"submit\" value=\"Edit\" />\n";
	echo "</form>\n";
	
	echo "<br/>\n";
	
	echo "<table><tr>\n";
	echo "<th style=\"width:180px\">Title (required)</th>";
	echo "<th style=\"width:180px\">Artist</th>";
	echo "<th style=\"width:180px\">Album</th>";
	echo "<th style=\"width:50px\"></th></tr>\n";
	echo "</table>\n";
	
	$items = $playlist->getItems();
	foreach ($items as $item)
	{
		echo "<form name=\"editplaylistitem\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"edititem\" />\n";
		echo "<input type=\"hidden\" name=\"itemid\" value=\"{$item->id}\" />\n";
		echo "<table><tr>\n";
		echo "<td style=\"width:180px\"><input name=\"title\" value=\"{$item->title}\" /></td>\n";
		echo "<td style=\"width:180px\"><input name=\"artist\" value=\"{$item->artist}\" /></td>\n";
		echo "<td style=\"width:180px\"><input name=\"album\" value=\"{$item->album}\" /></td>\n";
		echo "<td style=\"width:50px\"><input type=\"submit\" value=\"Edit\" /></td>\n";
		echo "<td style=\"width:50px\"><a href=\"".$_SERVER['PHP_SELF']."?page=myplaylist/admin.php&edit=".$_GET['edit']."&delitem={$item->id}\">Delete</a></td>\n";
		echo "</table>\n";
		echo "</form>\n";
//		echo "<tr><td>{$item->title}</td><td>{$item->artist}</td><td>{$item->album}</td>";
//		echo "<td><a href=\"".$_SERVER['PHP_SELF']."?page=myplaylist/admin.php&edit=".$_GET['edit']."&delitem={$item->id}\">Delete</a></td></tr>";
	}
	
	echo "</table>\n";
	
	echo "<form name=\"addplaylistitem\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"additem\" />\n";
	echo "<table><tr>\n";
	echo "<td style=\"width:180px\"><input name=\"title\" /></td>\n";
	echo "<td style=\"width:180px\"><input name=\"artist\" /></td>\n";
	echo "<td style=\"width:180px\"><input name=\"album\" /></td>\n";
	echo "<td style=\"width:50px\"><input type=\"submit\" value=\"Add\" /></td>\n";
	echo "</table>\n";
	echo "</form>\n";
}
?>
</div>
<?php
endif;
?>