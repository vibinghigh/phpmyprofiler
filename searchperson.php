<?php
/* phpMyProfiler
 * Copyright (C) 2006-2014 The phpMyProfiler project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
*/

// No direct access
defined('_PMP_REL_PATH') or die('Not allowed! Possible hacking attempt detected!');

$pmp_module = 'searchperson';

$smarty = new pmp_Smarty;
$smarty->loadFilter('output', 'trimwhitespace');

dbconnect();

$cast = array();
$crew = array();

// Only if a search-name is given
if ( isset($_GET['name']) ) {
	// Get birthyear if exists
	if ( isset($_GET['birthyear']) ) {
		$birthyear = mysqli_real_escape_string($_SESSION['db'], html2txt($_GET['birthyear']));
	}

	$name = html2txt($_GET['name']);
	$searchstr = str_replace("\\'", "'", html2txt($_GET['name']));
	$searchstr = strtolower($searchstr);
	$searchstr = trim(mysqli_real_escape_string($_SESSION['db'], $searchstr));

	// Actor search (+Rolename)
	$sql  = 'SELECT COUNT(pmp_actors.id) as episodes, pmp_actors.id, firstname, middlename, lastname, fullname, role, birthyear, creditedas ';
	$sql .= 'FROM pmp_common_actors, pmp_actors INNER JOIN pmp_film ON pmp_film.id = pmp_actors.id WHERE ';
	if ( isset($_GET['nowildcards']) ) {
		$sql .= '(LOWER(fullname) = \'' . $searchstr . '\' OR LOWER(role) = \'' . $searchstr . '\' OR LOWER(creditedas) = \'' . $searchstr . '\') ';
	}
	else {
		$sql .= '(LOWER(fullname) LIKE \'%' . $searchstr . '%\' OR LOWER(role) LIKE \'%' . $searchstr . '%\' OR LOWER(creditedas) LIKE \'%' . $searchstr . '%\') ';
	}
	if ( !empty($birthyear) ) {
		$sql .= 'AND birthyear = \'' . $birthyear. '\' ';
	}
	$sql .= 'AND pmp_common_actors.actor_id = pmp_actors.actor_id ';
	$sql .= 'AND pmp_actors.id NOT IN (SELECT id FROM pmp_tags where name = \'' . mysqli_real_escape_string($_SESSION['db'], $pmp_exclude_tag) . '\') ';
	$sql .= 'GROUP BY pmp_actors.id ';
	$sql .= 'ORDER BY birthyear, lastname, firstname, sorttitle';

	$result = dbexec($sql);
	if ( mysqli_num_rows($result) > 0 ) {
		while ( $row = mysqli_fetch_object($result) ) {
			// Get headshot
			$row->picname = getHeadshot($row->fullname, $row->birthyear, $row->firstname, $row->middlename, $row->lastname);
			// If not found set blank
			if ( empty($row->picname) ) {
				$row->picname = 'blank.jpg';
			}
			$row->fullname = $row->fullname;
			$row->role = $row->role;
			$row->creditedas = $row->creditedas;
			$row->DVD = new smallDVD($row->id);
			$cast[] = $row;
		}
	}

	// Crew search
	$sql  = 'SELECT DISTINCT pmp_credits.id, firstname, middlename, lastname, fullname, type, subtype, birthyear, creditedas ';
	$sql .= 'FROM pmp_common_credits, pmp_credits INNER JOIN pmp_film ON pmp_film.id = pmp_credits.id WHERE LOWER(fullname) ';
	if ( isset($_GET['nowildcards']) ) {
		$sql .= '= \'' . $searchstr . '\' ';
	}
	else {
		$sql .= 'LIKE \'%' . $searchstr . '%\' ';
	}
	if ( !empty($birthyear) ) {
		$sql .= 'AND birthyear = \'' . $birthyear. '\' ';
	}
	$sql .= 'AND pmp_common_credits.credit_id = pmp_credits.credit_id ';
	$sql .= 'AND pmp_credits.id NOT IN (SELECT id FROM pmp_tags where name = \'' . mysqli_real_escape_string($_SESSION['db'], $pmp_exclude_tag) . '\') ';
	$sql .= 'ORDER BY birthyear, lastname, firstname, sorttitle, type';

	$result = dbexec($sql);
	if ( mysqli_num_rows($result) > 0 ) {
		while ( $row = mysqli_fetch_object($result) ) {
			// Get headshot
			$row->picname = getHeadshot($row->fullname, $row->birthyear, $row->firstname, $row->middlename, $row->lastname);
			// If no found set blank
			if ( empty($row->picname) ) {
				$row->picname = 'blank.jpg';
			}
			$row->fullname = $row->fullname;
			$row->creditedas = $row->creditedas;
			$row->DVD = new smallDVD($row->id);
			$crew[] = $row;
		}
	}
}

$smarty->assign('cast', $cast);
$smarty->assign('crew', $crew);
$smarty->assign('name', $name);

$smarty->display('searchperson.tpl');
?>
