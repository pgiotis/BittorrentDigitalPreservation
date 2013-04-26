<?php
	/*
	 * Module:	install.php
	 * Description: This script prepares the database for use.
	 *
	 * Author:	danomac
	 * Written:	06-May-2005
	 *
	 * Copyright (C) 2004 danomac
	 *
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 */

	/*
	 * Declare queries as an array for easy(?) adding
	 * 'query' is the mysql query, 'name' is message displayed to user
	 * while creating the table
	 */
	$query_list[0]['name'] = "Creating namemap table...";
	$query_list[0]['query'] = "CREATE TABLE `namemap` (`info_hash` VARCHAR (40) NOT NULL DEFAULT '', `filename` VARCHAR (250) NOT NULL DEFAULT '',`threshold` VARCHAR (250) NOT NULL DEFAULT '',`strategy` VARCHAR (250) NOT NULL DEFAULT '', `url` VARCHAR (250) NOT NULL DEFAULT '', `mirrorurl` VARCHAR (250) NOT NULL DEFAULT '', `info` VARCHAR (250) NOT NULL DEFAULT '', `size` FLOAT NOT NULL DEFAULT 0, `crc32` VARCHAR (254) NOT NULL DEFAULT '', `DateAdded` DATE NOT NULL DEFAULT '0000-00-00', `category` VARCHAR (10) NOT NULL DEFAULT 'main', `sfvlink` VARCHAR (250), `md5link` VARCHAR (250), `infolink` VARCHAR (250), `DateToRemoveURL` DATE DEFAULT '0000-00-00' NOT NULL, `DateToHideTorrent` DATE DEFAULT '0000-00-00' NOT NULL, `addedby` VARCHAR (32) DEFAULT 'root' NOT NULL, `grouping` INT (5) UNSIGNED NOT NULL DEFAULT 0, `sorting` INT (5) UNSIGNED NOT NULL DEFAULT 0, `comment` VARCHAR (255) NOT NULL DEFAULT '', `show_comment` ENUM('Y','N') NOT NULL DEFAULT 'N', `tsAdded` BIGINT (20) NOT NULL DEFAULT 0, `torrent_size` BIGINT (10) NOT NULL DEFAULT 0, PRIMARY KEY(`info_hash`), INDEX(`category`, `DateToHideTorrent`))";
	$query_list[1]['name'] = "Creating torrent summary table...";
	$query_list[1]['query'] = "CREATE TABLE `summary` (`info_hash` CHAR (40) NOT NULL, `dlbytes` BIGINT (20) UNSIGNED DEFAULT 0 NOT NULL, `seeds` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `leechers` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `finished` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `lastcycle` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `lastSpeedCycle` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `lastAvgCycle` INT (10) UNSIGNED DEFAULT 0 NOT NULL, `speed` BIGINT (20) UNSIGNED DEFAULT 0 NOT NULL, `hide_torrent` ENUM ('N','Y') DEFAULT 'N', `avgdone` FLOAT DEFAULT 0 NOT NULL, `external_torrent` ENUM ('N','Y') DEFAULT 'N', `ext_no_scrape_update` ENUM ('N','Y') DEFAULT 'N', PRIMARY KEY(`info_hash`), INDEX(`hide_torrent`,`external_torrent`,`ext_no_scrape_update`))";
	$query_list[2]['name'] = "Creating timestamps table...";
	$query_list[2]['query'] = "CREATE TABLE `timestamps` (`info_hash` CHAR(40) NOT NULL, `sequence` INT UNSIGNED NOT NULL AUTO_INCREMENT, `bytes` BIGINT UNSIGNED NOT NULL, `delta` SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(`sequence`), KEY SORTING (`info_hash`))";
	$query_list[3]['name'] = "Creating logins table...";
	$query_list[3]['query'] = "CREATE TABLE `logins` (`id` INT (11) NOT NULL AUTO_INCREMENT, `used` TINYINT (1) DEFAULT '0' NOT NULL, `ipaddr` VARCHAR (16), PRIMARY KEY(`id`))";
	$query_list[4]['name'] = "Creating IP ban table...";
	$query_list[4]['query'] = "CREATE TABLE `ipbans` (`ban_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `ip` VARCHAR (16) NOT NULL, `iplong` INT(10)  DEFAULT '0' NOT NULL, `bandate` DATE DEFAULT '0000-00-00' NOT NULL, `reason` VARCHAR (50) NOT NULL, `autoban` ENUM ('Y','N') DEFAULT 'N' NOT NULL, `banlength` TINYINT (3) UNSIGNED DEFAULT 0 NOT NULL, `banexpiry` DOUBLE DEFAULT 0 NOT NULL, `banautoexpires` ENUM ('Y','N') DEFAULT 'N' NOT NULL, PRIMARY KEY(`ban_id`), INDEX(`bandate`,`autoban`,`banexpiry`,`banautoexpires`, `iplong`))";
	$query_list[5]['name'] = "Creating external torrents table...";
	$query_list[5]['query'] = "CREATE TABLE `trk_ext` (`info_hash` CHAR (40) NOT NULL, `scrape_url` VARCHAR (255) NOT NULL, `last_update` BIGINT (20) DEFAULT '0' NOT NULL, PRIMARY KEY(`info_hash`), INDEX(`scrape_url`))";
	$query_list[6]['name'] = "Creating retired torrents table...";
	$query_list[6]['query'] = "CREATE TABLE `retired` (`info_hash` VARCHAR (40) NOT NULL, `filename` VARCHAR (250) NOT NULL, `size` FLOAT DEFAULT '0' NOT NULL, `crc32` VARCHAR (254) NOT NULL, `category` VARCHAR (10) NOT NULL, `completed` INT (11) DEFAULT '0' NOT NULL, `transferred` BIGINT (20) DEFAULT '0' NOT NULL, `dateadded` DATE DEFAULT '0000-00-00' NOT NULL, `dateretired` DATE DEFAULT '0000-00-00' NOT NULL, PRIMARY KEY(`info_hash`))";
	$query_list[7]['name'] = "Creating user permissions table...";
	$query_list[7]['query'] = "CREATE TABLE `adminusers` (`username` VARCHAR (32) NOT NULL, `password` VARCHAR (32), `category` VARCHAR (10), `comment` VARCHAR (200), `enabled` ENUM('Y','N')  DEFAULT 'Y' NOT NULL, `disable_reason` VARCHAR(255) DEFAULT \"\" NOT NULL, `perm_add` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_addext` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_mirror` ENUM('Y','N')  DEFAULT 'N' NOT NULL, `perm_edit` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_delete` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_retire` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_unhide` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_peers` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_viewconf` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_retiredmgmt` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_ipban` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_usermgmt` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_advsort` ENUM('N','Y') DEFAULT 'N' NOT NULL, PRIMARY KEY(`username`))";
	$query_list[8]['name'] = "Creating torrents table...";
	$query_list[8]['query'] = "CREATE TABLE `torrents` (`info_hash` VARCHAR (40) DEFAULT '0' NOT NULL, `name` VARCHAR (255) NOT NULL, `metadata` LONGBLOB NOT NULL, PRIMARY KEY(`info_hash`))";
	$query_list[9]['name'] = "Creating subgrouping table...";
	$query_list[9]['query'] = "CREATE TABLE `subgrouping` (`group_id` BIGINT (10) UNSIGNED AUTO_INCREMENT NOT NULL, `heading` TEXT NOT NULL DEFAULT '', `groupsort` INT (5) UNSIGNED NOT NULL DEFAULT 0, `category` VARCHAR (10) NOT NULL DEFAULT 'main', PRIMARY KEY(`group_id`))";
	
	/*
	 * Constants for install type selection
	 */
	define("TABLES", 1);
	define("DB_TABLES", 2);
	define("DB_USER_TABLES", 3);

	/*
	 * Function to write a config.php to the server... of course
	 * write access is needed for this...
	 */
	function writeConfig($hostname, $username, $password, $database) {
		$fd = @fopen("config.php", "w");
		if (!$fd) return false;

		fwrite($fd, "<?php\r\n\t/* Tracker Configuration - config.php\r\n\t *\r\n\t * This file provides configuration information for\r\n\t * the tracker. The user-editable variables are at the top. It is\r\n\t * recommended that you do not change the database settings\r\n\t * unless you know what you are doing.\r\n\t *\r\n\t * Copyright (C) 2004 DeHackEd\r\n\t * Portions Copyright (C) 2004 danomac\r\n\t *\r\n\t * This program is free software; you can redistribute it and/or modify\r\n\t * it under the terms of the GNU General Public License as published by\r\n\t * the Free Software Foundation; either version 2 of the License, or\r\n\t * (at your option) any later version.\r\n\t *\r\n\t * This program is distributed in the hope that it will be useful,\r\n\t * but WITHOUT ANY WARRANTY; without even the implied warranty of\r\n\t * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\r\n\t * GNU General Public License for more details.\r\n\t *\r\n\t * You should have received a copy of the GNU General Public License\r\n\t * along with this program; if not, write to the Free Software\r\n\t * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA\r\n\t */\r\n"
			. "\r\n\t/*\r\n\t * Maximum reannounce interval\r\n\t *\r\n\t * The tracker expects the client to report before this time expires\r\n\t * from the previous contact (in seconds.)\r\n\t *\r\n\t * Default: 1800 seconds.\r\n\t */\r\n\t" . '$GLOBALS["report_interval"] = 1800;' . "\r\n"
			. "\r\n\t/*\r\n\t * Minimum reannounce interval (Optional)\r\n\t *\r\n\t * The tracker does not expect contact from the client until at least\r\n\t * this much time has elapsed from the previous contact (in seconds.)\r\n\t *\r\n\t * Default: 300 seconds.\r\n\t */\r\n\t" . '$GLOBALS["min_interval"] = 300;' . "\r\n"
			. "\r\n\t/*\r\n\t * Maximum Peers\r\n\t *\r\n\t * Maximum number of peers to send in one request.\r\n\t *\r\n\t * Default: 50.\r\n\t */\r\n\t" . '$GLOBALS["maxpeers"] = 50;' . "\r\n"
			. "\r\n\t/*\r\n\t * Require torrent authorization?\r\n\t *\r\n\t * If set to true, then the tracker will accept any and all\r\n\t * torrents given to it, it will not need to be specifically\r\n\t * added through the Administration interface.\r\n\t *\r\n\t * NOT RECOMMENDED, but available if you need it.\r\n\t *\r\n\t * Default: No anonymous torrents (false).\r\n\t */\r\n\t" . '$GLOBALS["dynamic_torrents"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Check for NAT clients?\r\n\t *\r\n\t * If set to true, NAT checking will be performed.\r\n\t * This may cause trouble with some providers!\r\n\t * Also,  so it's\r\n\t * off by default.\r\n\t *\r\n\t * Default: No checking (false).\r\n\t */\r\n\t" . '$GLOBALS["NAT"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Use a persistent database connection?\r\n\t *\r\n\t * Check with your webmaster to see if you're allowed to use these.\r\n\t *\r\n\t * Recommended if the database server is not on the same machine as\r\n\t * the web server software, or if there is a high load on the tracker.\r\n\t *\r\n\t * NOTE: If you share a server with someone else, this is not a good idea.\r\n\t *\r\n\t * Default: Normal connection (false).\r\n\t */\r\n\t" . '$GLOBALS["persist"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow IP override?\r\n\t *\r\n\t * Allow users to override the IP address reported. Usually not needed,\r\n\t * but if you seed torrents on the same intranet on the tracker, this\r\n\t * needs to be enabled.\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["ip_override"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Enable peer caching?\r\n\t *\r\n\t *\r\n\t * Table caches!\r\n\t * Lowers the load on all systems, but takes up more disk space.\r\n\t * You win some, you lose some. But since the load is the big problem,\r\n\t * grab this.\r\n\t *\r\n\t * Warning! Enable this BEFORE making torrents, or else run makecache.php\r\n\t * immediately, or else you'll be in deep trouble. The tables will lose\r\n\t * sync and the database will be in a somewhat \"stale\" state.\r\n\t *\r\n\t * Default: Enabled (true)\r\n\t */\r\n\t" . '$GLOBALS["peercaching"] = true;' . "\r\n"
			. "\r\n\t/*\r\n\t * How often to refresh tracker speed data?\r\n\t *\r\n\t * The tracker will update speed data on each torrent based on this interval\r\n\t * (in seconds.)\r\n\t *\r\n\t * For heavily loaded trackers, set ".'$GLOBALS["countbytes"]'." to false. It will\r\n\t * stop counting the number of downloaded bytes and the calculation of the \r\n\t * speed of the torrent; but it will significantly reduce the load.\r\n\t *\r\n\t * Default: spdrefresh: 60 seconds.\r\n\t *          countbytes: true.\r\n\t */\r\n\t".'$GLOBALS["spdrefresh"] = 60;'."\r\n\t".'$GLOBALS["countbytes"] = true;'."\r\n"
			. "\r\n\t/*\r\n\t * How often to refresh torrent average progress?\r\n\t *\r\n\t * The tracker will update average torrent progress based on this interval\r\n\t * (in seconds.) To disable altogether set ".'$GLOBALS["doavg"]'." to false. \r\n\t * Disabling will reduce the load on the tracker.\r\n\t *\r\n\t * Default: avgrefresh: 100 seconds.\r\n\t *          doavg:      true.\r\n\t */\r\n\t".'$GLOBALS["avgrefresh"] = 100;'."\r\n\t".'$GLOBALS["doavg"] = true;'."\r\n"
			. "\r\n\t/*\r\n\t * Optimize for heavy loads?\r\n\t *\r\n\t * Setting this to true will disable:\r\n\t *   -torrent average progress\r\n\t *   -amount transferred on torrent\r\n\t *   -torrent speed\r\n\t *\r\n\t * It will also enable (very slight) benefits in tracker.php.\r\n\t *\r\n\t * Default: normally loaded trackers (false)\r\n\t */\r\n\t" .'$GLOBALS["heavyload"] = false;'. "\r\n"
			. "\r\n\t/*\r\n\t * Only allow clients that support the compact protocol to connect?\r\n\t *\r\n\t * This offers bandwidth savings, but be aware that peer caching\r\n\t * HAS to be ENABLED for this to work.\r\n\t *\r\n\t * Default: allow all clients (false)\r\n\t */\r\n\t".'$GLOBALS["compactonly"] = false;'."\r\n\t"
			. "\r\n\t/*\r\n\t * Check client versions when they connect?\r\n\t *\r\n\t * Set to true to check to see which BT clients the leechers are using.\r\n\t * If the version is not recognized, it isn't allowed access.\r\n\t *\r\n\t * This can add a significant amount of processing overhead if a lot of\r\n\t * clients are connected to the tracker.\r\n\t *\r\n\t * Default: No filtering (false).\r\n\t */\r\n\t" . '$GLOBALS["filter_clients"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Scrape interval\r\n\t *\r\n\t * This is the minimum time for clients to wait before requesting another\r\n\t * scrape output from the tracker (in seconds). Maximum allowed is one hour \r\n\t * (3600 seconds).\r\n\t *\r\n\t * NOTE: Not all clients respect this.\r\n\t *\r\n\t * Default: 1/2 hour (1800 seconds.)\r\n\t */\r\n\t" . '$GLOBALS["scrape_min_interval"] = 1800;' . "\r\n"
			. "\r\n\t/*\r\n\t * Multi-user mode?\r\n\t *\r\n\t * Allow users specified in the User Management section to access the \r\n\t * Administration interface. See README for more details on this.\r\n\t *\r\n\t * Even with this disabled, the 'root' user can still access the Administration\r\n\t * interface.\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["allow_group_admin"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Automatic statistic consistency check?\r\n\t *\r\n\t * Set to true to run the consistency check if a client requests scrape output\r\n\t * and the script detects the statistics are not accurate. If there are no\r\n\t * clients on a torrent, this obviously won't do anything!\r\n\t *\r\n\t * NOTE: This can be processor-intensive!\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["auto_db_check_scrape"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow IP Banning?\r\n\t *\r\n\t * Set to true to check the client's ip address when connecting\r\n\t * and refusing a connection if they are banned.\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["enable_ip_banning"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow Automatic IP Banning?\r\n\t *\r\n\t * If client filtering is enabled, and this is enabled, will temporary ban\r\n\t * a client that is very old or refusing to identify itself.\r\n\t * \r\n\t * autobanlength is how long IP is temporarily banned when the tracker \r\n\t * automatically bans a client, in days\r\n\t *\r\n\t * Default: allow_unidentified_clients: No automatic banning (true).\r\n\t *          autobanlength: 3 days.\r\n\t */\r\n\t" . '$GLOBALS["allow_unidentified_clients"] = true;' . "\r\n\t" . '$GLOBALS["autobanlength"] = 3;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow torrent uploading?\r\n\t *\r\n\t * This tracker can upload the torrent automatically to the webserver, \r\n\t * provided the apache process has write access to your torrent folder. \r\n\t * Ideally, having ssh access is recommended in case you need to change the \r\n\t * owner of the files. \r\n\t * IF YOU DON'T KNOW WHAT ssh IS, DON'T TRY USING THIS.\r\n\t *\r\n\t * -Set 'allow_torrent_move' to true to copy the torrent to the specified \r\n\t *  folder.\r\n\t * -Set 'torrent_folder' to the folder that you wish to use, but remember that\r\n\t *  the apache processes need to have write access to this folder. This is\r\n\t *  relative to the root of your webserver (ie. setting to 'torrents' will\r\n\t *  use http://mysite.com/torrents as the destination.)\r\n\t * -Set 'max_torrent_size' to the maximum torrent size you want to allow (in \r\n\t *  bytes).\r\n\t * -Set 'move_to_db' to true to move the torrent into the database. If false,\r\n\t *  it will be placed in the folder specified on the server. \r\n\t *\r\n\t * Default: No (false); max torrent size of 100000 bytes.\r\n\t */\r\n\t" . '$GLOBALS["allow_torrent_move"] = false;' . "\r\n\t" . '$GLOBALS["max_torrent_size"] = 100000;' . "\r\n\t" . '$GLOBALS["torrent_folder"] = "torrents";' . "\r\n\t" . '$GLOBALS["move_to_db"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow /scrape requests?\r\n\t *\r\n\t * Set this to false to report an error if a client tries to use the\r\n\t * scrape output. NOTE: If you make torrents without the trailing\r\n\t * '/announce' in the tracker URL you don't need to use this. If you\r\n\t * expect a high load torrent, you can use this to disable it temporarily.\r\n\t *\r\n\t * Default: Yes (true).	\r\n\t */\r\n\t" . '$GLOBALS["allow_scrape"] = true;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow extra stats to be reported in /scrape data?\r\n\t *\r\n\t * Set this to true to report extra stats in scrape output.\r\n\t * This consists of the torrent name, average progress, speed,\r\n\t * and amount transferred.\r\n\t *\r\n\t * This uses extra bandwidth.\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["scrape_extras"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow the scrape scanning script to be used?\r\n\t * \r\n\t * Set this to true to allow scrape_scan.php to function.\r\n\t *\r\n\t * Default: No (false).\r\n\t */\r\n\t" . '$GLOBALS["scrape_scanning"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Set this to what your announce URL will be. An example would be\r\n\t * 'http://myweb.org/tracker/tracker.php/announce'. The administration\r\n\t * interface uses this to check if torrents are yours so it MUST be set.\r\n\t */\r\n\t" . '$GLOBALS["my_tracker_announce"] = "";' . "\r\n"
			. "\r\n\t/*\r\n\t * Allow external torrents to be added to this tracker?\r\n\t *\r\n\t * This tracker supports showing stats for external torrents by using\r\n\t * the external torrents' /scrape output. Note, in order for this to work,\r\n\t * the external tracker's announce url HAS TO END with '/announce'.\r\n\t * PHP needs to have it's allow_url_fopen ENABLED for this to work, or\r\n\t * ext_scan.php will fail to open the remote streams. See your php.ini file\r\n\t * and/or http://www.php.net/manual/en/ref.filesystem.php#ini.allow-url-fopen\r\n\t * for details.\r\n\t *\r\n\t * With that out of the way, set 'allow_external_scanning' to true to allow\r\n\t * you to add an external torrent.\r\n\t *\r\n\t * Set 'auto_add_external_torrents' to true to assume that if it is an\r\n\t * external torrent, just to add it. If this is false there will be an option\r\n\t * to add the torrent in admin as an external reference IF APPLICABLE (users \r\n\t * that aren't allowed to add external torrents will not see this option).\r\n\t * This requires the 'my_tracker_announce' to be set above. \r\n\t *\r\n\t * Set the 'external_refresh' to the amount of minutes before contacting\r\n\t * external sites. DO NOT SET THIS to a really low interval or you will find \r\n\t * yourself being banned from tracker sites.\r\n\t * \r\n\t * The 'external_refresh_tolerance' should be left at its default (5).\r\n\t *\r\n\t * Set the 'ext_batch_scrape' to false if you want the script to use the\r\n\t * info_hash parameter when querying external trackers, otherwise it will\r\n\t * request all the scrape data and parse through it.\r\n\t *\r\n\t * YOU NEED TO ADD external.php TO YOUR CRONTAB for every 15 minutes or so for\r\n\t * this to work. This script will use these setting here and contact the \r\n\t * external sites only when needed; it won't contact each site at the interval\r\n\t * you set in crontab.\r\n\t *\r\n\t * Defaults: allow_external_scanning = false\r\n\t *           auto_add_external_torrents = false\r\n\t *           external_refresh = 30\r\n\t *           ext_batch_scrape = false\r\n\t */\r\n\t" . '$GLOBALS["allow_external_scanning"] = false;' . "\r\n\t" . '$GLOBALS["auto_add_external_torrents"] = false;' . "\r\n\t" . '$GLOBALS["external_refresh"] = 30;' . "\r\n\t" . '$GLOBALS["external_refresh_tolerance"] = 5;' . "\r\n\t" . '$GLOBALS["ext_batch_scrape"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * Enable RSS system? Set to true if you want to use the builtin RSS support.\r\n\t * Configuration items for RSS are in rss_conf.php.\r\n\t *\r\n\t * Default: false\r\n\t */\r\n\t" . '$GLOBALS["enable_rss"] = false;' . "\r\n"
			. "\r\n\t/*\r\n\t * If your website is part of a webserver farm, you aren't guaranteed to get the\r\n\t * same server when you use sessions. In this case, set \r\n\t * " . '$GLOBALS["webserver_farm"]' . " to true and set \r\n\t * " . '$GLOBALS["webserver_farm_session_path"]' . " to a path that ALL webservers have\r\n\t * write access to (don't use a trailing slash on the path.)\r\n\t *\r\n\t * Default: false\r\n\t */\r\n\t" . '$GLOBALS["webserver_farm"] = false;' . "\r\n\t" . '$GLOBALS["webserver_farm_session_path"] = "/tmp";' . "\r\n"
			. "\t\r\t/*\r\n\t * Administration root username/password.\r\n\t * YOU HAVE TO SET THIS; THE ADMINISTRATION SYSTEM STAYS DISABLED UNTIL THIS\r\n\t * IS SET! It doesn't hurt to use a strong username/password combination...\r\n\t */\r\n\t".'$admin_user="";'."\r\n\t".'$admin_pass="";'."\r\n"
			. "\r\n\t/*\r\n\t * Database settings - These are used to connect to the database \r\n\t *\r\n\t * Don't change these unless you know what you are doing.\r\n\t */\r\n\t" . '$dbhost = "' . $hostname . '"; $dbuser = "' .  $username . '"; $dbpass = "' .  $password . '"; $database = "' . $database . '";' . "\r\n?>");

		fclose($fd);
		return true;
	}


	if (isset($_POST["install"])) {
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n\t<TITLE>PHPBTTracker+ Installation</TITLE>\r\n</HEAD>";
		echo "<BODY>\r\n\t<FORM ACTION=\"install.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Installation</H1>\r\n";
		echo "\tInstall in progress....<BR><BR>";

		/*
		 * OK, check the input
		 */
		$checkfailed = false;

		/*
		 * Administration username
		 */
		if (!((isset($_POST["username"])) ? ((strlen($_POST["username"])==0) ? false : true) : true)) {
			$checkfailed = true;
			echo "\t<FONT COLOR=RED><B>You need to specify the database administration username.</B></FONT><BR>";
		}

		/*
		 * If no password is set, use an empty string
		 */
		$dbpassword = (isset($_POST["password"])) ? $_POST["password"] : "";

		/*
		 * Database name
		 */
		if (!((isset($_POST["db_name"])) ? ((strlen($_POST["db_name"])==0) ? false : true) : true)) {
			$checkfailed = true;
			echo "\t<FONT COLOR=RED><B>You need to specify a database name.</B></FONT><BR>";
		}

		/*
		 * Database location
		 */
		if (!((isset($_POST["db_loc"])) ? ((strlen($_POST["db_loc"])==0) ? false : true) : true)) {
			$checkfailed = true;
			echo "\t<FONT COLOR=RED><B>You need to specify a database location.</B></FONT><BR>";
		}

		/*
		 * New username and password, if needed
		 */
		if (isset($_POST["install_type"]) && $_POST["install_type"] == DB_USER_TABLES ) {
			/*
			 * Username
			 */
			if (!((isset($_POST["newusername"])) ? ((strlen($_POST["newusername"])==0) ? false : true) : true)) {
				$checkfailed = true;
				echo "\t<FONT COLOR=RED><B>You need to specify a new username.</B></FONT><BR>";
			}

			/*
			 * New password
			 */
			if (!((isset($_POST["newpassword"])) ? ((strlen($_POST["newpassword"])==0) ? false : true) : true)) {
				$checkfailed = true;
				echo "\t<FONT COLOR=RED><B>You need to specify a new password.</B></FONT><BR>";
			}
		}

		/*
		 * If the test passes, proceed with installation
		 */
		if (!$checkfailed) {
			/*
			 * Connect to the database
			 */
			@mysql_connect($_POST["db_loc"], $_POST["username"], $dbpassword) or die("\tCan't connect to database: ".mysql_error(). "\r\n</BODY>\r\n</HTML>"); 

			/*
			 * Check mysql compatability and issue warnings, if needed
			 */
			echo "Attempting to check mysql compatability [4.1.2 or greater required]... ";
			$rstVer = @mysql_query("SHOW VARIABLES LIKE 'version'");
			if (!$rstVer) {
				echo "<FONT COLOR=RED>FAILED.</FONT><BR><BR>";
			} else {
				$rowVer = mysql_fetch_row($rstVer);
				echo $rowVer[1] . " detected... ";

				/*
				 * Break down the version number and check it
				 */
				$splitVer = explode(".", $rowVer[1]);
				
				if ($splitVer[0] < 4) {
					echo " <B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
				} else {
					if ($splitVer[0] == 4) {
						if ($splitVer[1] < 1 ) {
							echo " <B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
						} else {
							if ($splitVer[1]==1) {
								if ($splitVer[2] < 2) {
									echo "<B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
								} else {
									echo "Pass.<BR><BR>";
								}
							}
						}

					} else {
						echo "Pass.<BR><BR>";
					}
				}
				echo "<BR><BR>";
			}

			/*
			 * If the database needs to be created, do that first...
			 */
			if ($_POST["install_type"] > TABLES) {
				echo "Attempting to create database...";
				@mysql_query("CREATE DATABASE " . $_POST["db_name"]) or die("\tFAILED. Can't create database ".$_POST["db_name"].": ".mysql_error()."\r\n</BODY>\r\n</HTML>");
				echo "done!<BR>";
			}

			/*
			 * If user needs to be created, create it and assign appropriate permissions
			 */
			if ($_POST["install_type"] == DB_USER_TABLES) {
				echo "Attempting to create new user...";
				@mysql_query("GRANT SELECT, INSERT, UPDATE, DELETE, INDEX, ALTER, CREATE, DROP, REFERENCES ON ". $_POST["db_name"] .".* TO '". $_POST["newusername"] ."'@'". $_POST["db_loc"] ."' IDENTIFIED BY '". $_POST["newpassword"] ."'") or die("\tFAILED. Can't create new user ".$_POST["newusername"].": ". mysql_error());
				@mysql_query("FLUSH PRIVILEGES") or die("\tCan't flush database privileges" . mysql_error() ."\r\n</BODY>\r\n</HTML>");
				echo "done!<BR>";
			}

			/*
			 * Connect to the desired database
			 */
			@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "\r\n</BODY>\r\n</HTML>");

			/*
			 * Now go and create all the tables...
			 */
			$tablescreated = 0;
			
			/*
			 * Some error checking, if the error array is created it, terminate it
			 */
			if (isset($sqlerror)) unset($sqlerror);

			echo "<BR>Starting to create tables...<BR>";
			for ($i=0; $i < count($query_list); $i++) {
				if (strlen($query_list[$i]["query"]) > 0) {
					echo $query_list[$i]["name"];
					if (!@mysql_query($query_list[$i]["query"])) {
						echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						$sqlerror[] = array('name' => $query_list[$i]["name"], 'error' => mysql_error());
					} else {
						echo "done!<BR>";
						$tablescreated++;
					}
				}
			}

			echo "<BR>$tablescreated tables were created.<BR><BR>";

			/*
			 * Check to see if any queries failed, and if so, report the mysql errors.
			 */
			if (isset($sqlerror)) {
				echo "<FONT COLOR=RED><B>Problems creating required tables were detected.</B> Please use the following error output to report a bug to http://sourceforge.net/tracker/?group_id=120663&atid=687790.<BR><BR>";
				for ($i=0, $totalerr=count($sqlerror); $i < $totalerr; $i++) {
					echo "<B>Error " . $sqlerror[$i]["name"] . "</B>:  " . $sqlerror[$i]["error"] . "<BR>";
				}

				echo "<BR> Please copy the above information into a new bugreport.<BR><BR></FONT>";
			}

			/*
			 * If requested, attempt to write a configuration file.
			 */
			if ((isset($_POST["write_conf"])) ? ((strcmp($_POST["write_conf"], "checked")==0) ? true : false) : false) {
				/*
				 * Hm, decide which username and password to put in the config file
				 */
				$cfgusername = ($_POST["install_type"] == DB_USER_TABLES) ? $_POST["newusername"] : $_POST["username"];
				$cfgpassword = ($_POST["install_type"] == DB_USER_TABLES) ? $_POST["newpassword"] : $_POST["password"];


				if (writeConfig($_POST["db_loc"], $cfgusername, $cfgpassword, $_POST["db_name"]))
					echo "<B><FONT COLOR=BLUE>config.php written.</FONT></B><BR><BR>";
				else 
					echo "<B><FONT COLOR=RED>Can't create config.php, more than likely write permission denied.</FONT></B><BR><BR>";
			} else 
				echo "This script did NOT attempt to write a configuration file to the server. You will need to rename config_sample.php to config.php manually.<BR><BR>";

			if ($tablescreated == count($query_list))
				echo "<B><FONT COLOR=BLUE>Installation appears to have completed. Delete install.php and/or upgrade.php from your web server. Refer to the INSTALL for the rest of the installation procedure.</FONT></B>";
			else
				echo "<B><FONT COLOR=RED>Installation appears to have had errors. Please check and try again, if needed.</FONT></B>";
		} else {
			echo "<BR><FONT COLOR=RED><B>Some fields were not filled out.</B></FONT> Correct the errors and try again.";
		}
		echo "</BODY>\r\n</HTML>";
		exit;
	}

	if (isset($_POST["step2"])) {
		switch ($_POST["install_sel"]) {
			case TABLES:
				echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n\t<TITLE>PHPBTTracker+ Installation</TITLE>\r\n</HEAD>";
				echo "<BODY>\r\n\t<FORM ACTION=\"install.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Installation</H1>\r\n";
				echo "<B>This install stage will only create the tables in the database you specify here.</B><BR><BR>Enter the database name, the database location (usually localhost), and the username and password of a user that has CREATE TABLES permissions for the database.<BR><BR>";
				echo "\t<TABLE BORDER=0>\r\n";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Username:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"username\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Password:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"password\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database name:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_name\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database location:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT><INPUT TYPE=CHECKBOX NAME=\"write_conf\" VALUE=\"checked\" CHECKED></TD>\r\n\t\t<TD>Attempt to write config.php on server (apache needs write access to do this)</TD>\r\n\t</TR>\r\n";
				echo "\t</TABLE>\r\n\t<INPUT TYPE=\"SUBMIT\" NAME=\"install\" VALUE=\"Install...\">\r\n\t<INPUT TYPE=\"HIDDEN\" NAME=\"install_type\" VALUE=\"".$_POST["install_sel"]."\">\r\n\t</FORM>\r\n</BODY>\r\n</HTML>";
				exit;
				break;
			case DB_TABLES:
				echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n\t<TITLE>PHPBTTracker+ Installation</TITLE>\r\n</HEAD>";
				echo "<BODY>\r\n\t<FORM ACTION=\"install.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Installation</H1>\r\n";
				echo "<B>This install stage will only create the database and tables with the username and password you specify here.</B><BR><BR>Enter the database name to create, the database location (usually localhost), and the username and password of a user that has CREATE DATABASE and CREATE TABLES permissions for the database.<BR><BR>";
				echo "\t<TABLE BORDER=0>\r\n";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Username:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"username\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Password:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"password\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database name:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_name\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database location:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT><INPUT TYPE=CHECKBOX NAME=\"write_conf\" VALUE=\"checked\" CHECKED></TD>\r\n\t\t<TD>Attempt to write config.php on server (apache needs write access to do this)</TD>\r\n\t</TR>\r\n";
				echo "\t</TABLE>\r\n\t<INPUT TYPE=\"SUBMIT\" NAME=\"install\" VALUE=\"Install...\">\r\n\t<INPUT TYPE=\"HIDDEN\" NAME=\"install_type\" VALUE=\"".$_POST["install_sel"]."\">\r\n\t</FORM>\r\n</BODY>\r\n</HTML>";
				exit;
				break;
			case DB_USER_TABLES:
				echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n\t<TITLE>PHPBTTracker+ Installation</TITLE>\r\n</HEAD>";
				echo "<BODY>\r\n\t<FORM ACTION=\"install.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Installation</H1>\r\n";
				echo "<B>This install stage will create a new username, database and tables with the username and password you specify here.</B><BR><BR>Enter the database name to create, the database location (usually localhost), the new username and password to create, and the username and password of a user that has CREATE DATABASE, CREATE TABLES, and ADD USER permissions for the database.<BR><BR>";
				echo "\t<TABLE BORDER=0>\r\n";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Username:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"username\" SIZE=40>(Used to create database and username)</TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>DB Admin Password:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"password\" SIZE=40>(Used to create database and username)</TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT COLSPAN=2>&nbsp;</TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database name:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_name\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>Database location:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>New Username:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"newusername\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT>New Password:</TD>\r\n\t\t<TD><INPUT TYPE=\"text\" NAME=\"newpassword\" SIZE=40></TD>\r\n\t</TR>";
				echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT><INPUT TYPE=CHECKBOX NAME=\"write_conf\" VALUE=\"checked\" CHECKED></TD>\r\n\t\t<TD>Attempt to write config.php on server (apache needs write access to do this)</TD>\r\n\t</TR>\r\n";
				echo "\t</TABLE>\r\n\t<INPUT TYPE=\"SUBMIT\" NAME=\"install\" VALUE=\"Install...\">\r\n\t<INPUT TYPE=\"HIDDEN\" NAME=\"install_type\" VALUE=\"".$_POST["install_sel"]."\">\r\n\t</FORM>\r\n</BODY>\r\n</HTML>";
				exit;
				break;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<TITLE>PHPBTTracker+ Installation</TITLE>
</HEAD>
<BODY>
	<FORM ACTION="install.php" METHOD="POST">
	<H1>PHPBTTracker+ Installation</H1>
	Now that you have made it this far, the database needs to be prepared for the tracker. This script will help you do that.<BR><BR>

	<TABLE BORDER=0>
	<TR>
		<TD>Make a selection:</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><INPUT TYPE=RADIO NAME="install_sel" VALUE="<?php echo TABLES; ?>" CHECKED></TD>
		<TD>I have a Username, Password, and Database created already; I want to create the tables only</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><INPUT TYPE=RADIO NAME="install_sel" VALUE="<?php echo DB_TABLES; ?>"></TD>
		<TD>I have a username that can create databases; I want to create the database and tables only <B>Don't have the tracker using the database as root!</B></TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT><INPUT TYPE=RADIO NAME="install_sel" VALUE="<?php echo DB_USER_TABLES; ?>"></TD>
		<TD>I have a username that can create databases and user accounts; I want to create a new user, database, and tables</TD>
	</TR>
	</TABLE>


	<BR><INPUT TYPE="SUBMIT" NAME="step2" VALUE="Next -&gt;">
	</FORM>
</BODY>
</HTML>
