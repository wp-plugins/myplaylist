=== MyPlaylist ===
Contributors: leocacheux
Tags: music, list, playlist
Requires at least: 2.0.0
Tested up to: 2.1.2
Stable tag: trunk

Manage playlists of songs and display them in layout or in posts.

== Description ==

This plugin allows you to create and edit playlists. They can be displayed in the layout of your theme, or directly in your posts by using a tag.

A playlist won't be displayed until you activate the "published" checkbox. You can order them by setting the priority of each playlist.

== Installation ==

1. Unzip the archive in the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Edit your playlists and some options in the `Options > MyPlaylist` page of your admin.

There are two ways to use the plugin :

1. Edit your template by adding :
* `get_playlists($limit = 1000)` : Show a list of published playlists. Optionnal parameter can show only the last ones.
* `get_playlist($id)` : Show a specified playlist. You can find the id in the administration section.
* `get_last_playlist()` : Show always the last published playlist (or the playlist with the highest priority).
2. Insert in your post `{playlist_id}`. Replace `id` with the id number of the playlist. You can find this id in the administration section.