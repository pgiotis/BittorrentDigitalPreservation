<?php
	/*
	 * Module:	bta_confirm.php
	 * Description: This is the confirm operation screen of the administrative interface.
	 * 		This module displays selected torrents from the main admin screen and asks the
	 * 		user to confirm the operation.
	 *
	 * Author:	danomac
	 * Written:	24-March-2004
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
	 * Session webserver farm check
	 */
	require_once ("../config.php");

	if (isset($GLOBALS["webserver_farm"]) && isset($GLOBALS["webserver_farm_session_path"])) {
		if ($GLOBALS["webserver_farm"] && strlen($GLOBALS["webserver_farm_session_path"]) > 0) {
			session_save_path($GLOBALS["webserver_farm_session_path"]);
		}
	}
	session_start();
	header("Cache-control: private");

	/*
	 * List of the external modules required
	 */
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * Get the current script name.
	 */
	$scriptname = $_SERVER["PHP_SELF"];

	/*
	 * Get the client's IP address. Used for verifying access.
	 */
	$ip = str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]);

	/*
	 * Check to make sure person is logged in, and that the session
	 * is actually theirs.
	 */
	if (!admIsLoggedIn($ip)) {
		admShowError("You can't access this page directly.",
			     "You don't appear to be logged in. Use admin/index.php to login to the administrative interface.",
			     $adm_pageerr_title);
		exit;		
	}

	/*
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["peers"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * If the button to confirm everything was pressed, process
	 * and return to the main page.
	 */
	if (isset($_POST["confirmation"])) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or sqlErr(mysql_error());

		/*
		 * Process unhide list, if some were selected
		 */
		if (isset($_SESSION["pbanlist"])) {
			/*
			 * Get the ban reason
			 */
			if (isset($_POST["banreason"]))
				if (strlen($_POST["banreason"])==0)
					$banreason = "Contact the administrator for details.";
				else
					$banreason = $_POST["banreason"];
			else
				$banreason = "Contact the administrator for details.";

			/*
			 * Go through each selected peer and ban them..
			 */
			foreach ($_SESSION["pbanlist"] as $key => $value) {
				/*
				 * This is a two-step process: remove them from the current peer table
				 * (this will not go through all the torrents and remove the peer ID...)
				 * then add them to the banlist. NOTE: You have to set the 'enable_ip_banning' setting
				 * in config.php for it to be enforced!
				 */
	 			$iplong = ip2long($_SESSION["pbanlist"][$key]["ip"]);
				@mysql_query("DELETE FROM x". $_SESSION[pbanlist][$key]["hash"] ." WHERE peer_id=\"".$_SESSION["pbanlist"][$key]["peerid"]."\"");
				@mysql_query("INSERT INTO ipbans (ip, iplong, bandate, reason, autoban) 
									VALUES (\"".$_SESSION["pbanlist"][$key]["ip"]."\", 
										$iplong,
										\"". date("Y-m-d") ."\", 
										\"$banreason\", \"N\")");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["pbanlist"]);
		}

		/*
		 * Process delete list, if some were selected
		 */
		if (isset($_SESSION["pdeletelist"])) {
			/*
			 * Go through each selected peer and remove them from the tables
			 */
			foreach ($_SESSION["pdeletelist"] as $key => $value) {
				@mysql_query("DELETE FROM x". $_SESSION[pdeletelist][$key]["hash"] ." WHERE peer_id=\"".$_SESSION["pdeletelist"][$key]["peerid"]."\"");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["pdeletelist"]);
		}

		admShowMsg("Changes applied.","Redirecting to peer details administration page.","Redirecting", true, "bta_peers.php", 3);
		exit;
	}

	/*
	 * Check to make sure something was actually selected.
	 */	
	if (!isset($_POST["process"])) {
		admShowMsg("Nothing selected.","Redirecting to peer details administration page.","Redirecting", true, "bta_peers.php", 3);
		exit;
	} else
		$processlist = $_POST["process"];

	/*
	 * Let's get the hash table in question from the session variable
	 * previous form.
	 */
	if (!isset($_SESSION["info_hash"])) {
		admShowMsg("There was an error (-1).","Redirecting to peer details administration page.","Redirecting", true, "bta_peers.php", 3);
		exit;
	} else
		$hash = $_SESSION["info_hash"];
	
		
	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or sqlErr(mysql_error());

	foreach ($processlist as $peerid => $action) {
		/*
		 * Design choice: I decided to split up the operations by action. So,
		 * tables will be presented below with the actions grouped together. (i.e.
		 * there will be a table listing items selected for retired, a seperate table
		 * for listing items to be deleted.)
		 *
		 * To do this, they will be broken up into seperate arrays and populated
		 * with items grabbed from mysql. The arrays will be sorted by name using
		 * a function I threw together to sort multidimensional arrays.
		 */
		switch ($action) {
				case ACTION_PBAN:
					/*
					 * Build query string
					 */
					$query = "SELECT ip, 
											port, 
											bytes, 
											uploaded, 
											clientversion FROM x$hash WHERE peer_id = \"$peerid\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$pbanlist[] = array('peerid' => $peerid,
												'action' => $action,
												'hash' => $hash,
												'ip' => $row[0],
												'port' => $row[1],
												'bytes' => round($row[2]/1048576,2) . " MiB",
												'uploaded' => round($row[3]/1048576,2) . " MiB",
												'clientversion' => $row[4]);
					break;
				case ACTION_PDELETE:
					/*
					 * Build query string
					 */
					$query = "SELECT ip, 
											port, 
											bytes, 
											uploaded, 
											clientversion FROM x$hash WHERE peer_id = \"$peerid\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					/*
					 * If it's external delete it. You can't retire an external torrent.
					 * Otherwise retire as usual.
					 */
					$pdeletelist[] = array('peerid' => $peerid,
												'action' => $action,
												'hash' => $hash,
												'ip' => $row[0],
												'port' => $row[1],
												'bytes' => round($row[2]/1048576,2) . " MiB",
												'uploaded' => round($row[3]/1048576,2) . " MiB",
												'clientversion' => $row[4]);
					break;
		}
	}

	/*
	 * Okay, they are seperated, now sort the lists,
	 * and put them in a session variable for later use.
	 * First, unset the session variables, in case the screen has been
	 * used previously in the same session.
	 */
	unset($_SESSION['pdeletelist'], $_SESSION['pbanlist']);
	if (count($pdeletelist) > 0)
		$_SESSION['pdeletelist'] = array_sort($pdeletelist, "ip");
	if (count($pbanlist) > 0)
		$_SESSION['pbanlist'] = array_sort($pbanlist, "ip");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META NAME="Author" CONTENT="danomac">
<LINK REL="stylesheet" HREF="admin.css" TYPE="text/css" TITLE="Default">
<?php
	/*
	 * Set the page title.
	 */
	echo "<TITLE>". $adm_page_title . " - Confirm selections</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_peer_confirm.php">
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Confirm selections</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_peer_confirm.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_peers.php\">Return to Peer Details screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15>
		<FONT SIZE=+2>You have elected to:</FONT><BR><BR>
<?php
	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	if (isset($_SESSION["pbanlist"])) {
		echo "\t\t<FONT SIZE=+2><B>BAN the following IP addresses:</B></FONT><BR><DIV class=\"specialtag\">BAN Reason: <INPUT TYPE=text NAME=\"banreason\" SIZE=40 MAXLENGTH=200 VALUE=\"Contact the administrator for details.\"></DIV>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>IP</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Port</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Amount<BR>Remaining</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Uploaded</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Client Version</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["pbanlist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["pbanlist"][$key]["ip"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pbanlist"][$key]["port"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pbanlist"][$key]["bytes"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pbanlist"][$key]["uploaded"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["pbanlist"][$key]["clientversion"]."</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["pdeletelist"])) {
		echo "\t\t<FONT SIZE=+2><B>DELETE the following peers:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>IP</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Port</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Amount<BR>Remaining</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Uploaded</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Client Version</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["pdeletelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["pdeletelist"][$key]["ip"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pdeletelist"][$key]["port"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pdeletelist"][$key]["bytes"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["pdeletelist"][$key]["uploaded"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["pdeletelist"][$key]["clientversion"]."</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}
?>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_peers.php\">Return to Peer Details screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center">If the information above is correct, click the <I>Confirm and process</I> button below to proceed.</TD>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center"><INPUT TYPE="submit" NAME="confirmation" VALUE="Confirm and process" CLASS="button"></TD>
</TR>
</TABLE>
</FORM>
</BODY>
</HTML>