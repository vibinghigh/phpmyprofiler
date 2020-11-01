<?php
/* phpMyProfiler
 * Copyright (C) 2005-2014 The phpMyProfiler project
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

require_once('include/smallDVD.class.php');

class DVD extends smallDVD {
	function __construct($id) {
		global $pmp_dir_cast, $pmp_thousands_sep, $pmp_dec_point, $pmp_extern_reviews, $pmp_dateformat;

		if ( isset($id) ) {
			parent::__construct($id);

			// Features
			$sql = 'SELECT * FROM pmp_features WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				$row = mysqli_fetch_object($result);
				$this->Scenes = $row->sceneaccess;
				$this->Comment = $row->comment;
				$this->Trailer = $row->trailer;
				$this->BonusTrailer = $row->bonustrailer;
				$this->PhotoGallery = $row->gallery;
				$this->Deleted = $row->deleted;
				$this->MakingOf = $row->makingof;
				$this->Notes = $row->prodnotes;
				$this->Game = $row->game;
				$this->DVDrom = $row->dvdrom;
				$this->Multiangle = $row->multiangle;
				$this->Musicvideos = $row->musicvideos;
				$this->Interviews = $row->interviews;
				$this->Storyboard = $row->storyboard;
				$this->Outtakes = $row->outtakes;
				$this->ClosedCaptioned = $row->closedcaptioned;
				$this->THX = $row->thx;
				$this->PictureInPicture = $row->pip;
				$this->BDLive = $row->bdlive;
				$this->DigitalCopy = $row->digitalcopy;
				$this->Other = htmlspecialchars($row->other, ENT_COMPAT, 'UTF-8');
			}

			// Format
			$sql = 'SELECT * FROM pmp_format WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				$row = mysqli_fetch_object($result);
				$this->Ratio = $row->ratio;
				$this->Video = $row->video;
				$this->Color = $row->clrcolor;
				$this->BlackWhite = $row->clrblackandwhite;
				$this->Colorized = $row->clrcolorized;
				$this->Mixed = $row->clrmixed;
				$this->PanAndScan = $row->panandscan;
				$this->FullFrame = $row->fullframe;
				$this->Widescreen = $row->widescreen;
				$this->Anamorph = $row->anamorph;
				$this->DualSide = $row->dualside;
				$this->DualLayer = $row->duallayer;
				$this->Dim2D = $row->dim2d;
				$this->Anaglyph = $row->anaglyph;
				$this->Bluray3D = $row->bluray3d;
			}

			// Regioncode
			$sql = 'SELECT region FROM pmp_regions WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Regions[] = $row->region;
				}
			}

			// Countries of origin
			$sql = 'SELECT country FROM pmp_countries_of_origin WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Origins[] = $row->country;
				}
			}

			// Genres
			$sql = 'SELECT genre FROM pmp_genres WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Genres[] = $row->genre;
				}
			}

			// Studios
			$sql = 'SELECT studio FROM pmp_studios WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Studios[] = htmlspecialchars($row->studio, ENT_COMPAT, 'UTF-8');
				}
			}

			// Media Companies
			$sql = 'SELECT company FROM pmp_media_companies WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->MediaCompanies[] = htmlspecialchars($row->company, ENT_COMPAT, 'UTF-8');
				}
			}

			// Subtitles
			$sql = 'SELECT subtitle FROM pmp_subtitles WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Subtitles[] = $row->subtitle;
				}
			}

			// Audio
			$sql = 'SELECT content, format, channels FROM pmp_audio WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				$this->dd = false;
				$this->dts = false;

				while ( $row = mysqli_fetch_object($result) ) {
					$this->Audio[] = array('Content' => $row->content, 'Format' => $row->format, 'Channels' => $row->channels);

					if ( preg_match('/\bDolby Digital\b/i', $row->format) ) {
						$this->dd = true;
					}
					if ( preg_match('/\bDTS\b/i', $row->format) ) {
						$this->dts = true;
					}
				}
			}

			// Discs
			$sql = 'SELECT * FROM pmp_discs WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$row->descsidea = htmlspecialchars($row->descsidea, ENT_COMPAT, 'UTF-8');
					$row->descsideb = htmlspecialchars($row->descsideb, ENT_COMPAT, 'UTF-8');
					$this->Discs[] = $row;
				}
			}

			// Events
			$sql  = 'SELECT *, DATE_FORMAT(timestamp, \'%H:%i:%s\') AS time, DATE_FORMAT(timestamp, \'' . $pmp_dateformat . '\') AS date ';
			$sql .= 'FROM pmp_events LEFT JOIN pmp_users ON pmp_events.user_id = pmp_users.user_id ';
			$sql .= 'WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Events[] = $row;
				}
			}

			// Credits
			$sql  = 'SELECT firstname, middlename, lastname, fullname as full, birthyear, type, subtype, creditedas
				FROM pmp_common_credits, pmp_credits WHERE pmp_credits.id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id)
				. '\' AND pmp_credits.credit_id = pmp_common_credits.credit_id ORDER BY sortorder';

			$result = dbexec($sql);
			if( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$row->full_encoded = rawurlencode($row->full);
					$row->picname = getHeadshot($row->full, $row->birthyear, $row->firstname, $row->middlename, $row->lastname);
					$row->pic = !empty($row->picname);
					$this->Credits[] = $row;
				}
			}

			// Cast
			$sql  = 'SELECT firstname, middlename, lastname, fullname as full, birthyear, role, uncredited, voice, creditedas
				FROM pmp_common_actors, pmp_actors WHERE pmp_actors.id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'
				AND pmp_actors.actor_id = pmp_common_actors.actor_id ORDER BY sortorder';

			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$row->full_encoded = rawurlencode($row->full);
					$row->picname = getHeadshot($row->full, $row->birthyear, $row->firstname, $row->middlename, $row->lastname);
					$row->pic = !empty($row->picname);
					$this->Cast[] = $row;
				}
			}

			// Tags
			$sql = 'SELECT name, fullname FROM pmp_tags WHERE id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\'';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Tags[] = array("fullname" => $row->fullname, "name" => $row->name);
				}
			}

			// Reviews
			$sql = 'SELECT name, title, email, date_format(date, \'' . $pmp_dateformat . '\') AS date, text, vote
				FROM pmp_reviews WHERE film_id = \'' . mysqli_real_escape_string($_SESSION['db'], $this->id) . '\' and status = 1
				ORDER BY date DESC';
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Reviews[] = array("Name" => $row->name, "Title" => $row->title, "eMail" => $row->email,
						"Text" => $row->text, "Vote" => $row->vote, "Date" => $row->date);
				}
			}

			// Awards
			if ( !$this->OriginalTitle ) {
				$sql = 'SELECT award, awardyear, category, winner, nominee FROM pmp_awards WHERE LOWER(title) = LOWER(\''
					. mysqli_real_escape_string($_SESSION['db'], html_entity_decode($this->Title, ENT_QUOTES, 'UTF-8')) . '\') AND awardyear BETWEEN \''
					. mysqli_real_escape_string($_SESSION['db'], $this->Year) . '\' AND \''  . mysqli_real_escape_string($_SESSION['db'], $this->Year) . '\'+1 ORDER BY award, winner DESC';
			}
			else {
				$sql = 'SELECT award, awardyear, category, winner, nominee FROM pmp_awards WHERE LOWER(title) = LOWER(\''
					. mysqli_real_escape_string($_SESSION['db'], html_entity_decode($this->OriginalTitle, ENT_QUOTES, 'UTF-8')) . '\') AND awardyear BETWEEN \''
					. mysqli_real_escape_string($_SESSION['db'], $this->Year) . '\' AND \''  . mysqli_real_escape_string($_SESSION['db'], $this->Year) . '\'+1 ORDER BY award, winner DESC';
			}
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Awards[] = $row;
				}
			}

			// Videos
			$sql = "SELECT type, ext_id, title FROM pmp_videos WHERE id = '" . mysqli_real_escape_string($_SESSION['db'], $this->id) . "'";
			$result = dbexec($sql);
			if ( @mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->Videos[] = $row;
				}
			}

			// Get external reviews
			#$sql = "SELECT DISTINCT (title) FROM pmp_reviews_connect WHERE id = '" . mysqli_real_escape_string($_SESSION['db'], $this->id) . "'";
			#$result = dbexec($sql);
			$i = 0;
			#while ( $tmp = mysqli_fetch_object($result) ) {
				$this->extReviews[$i] = new stdClass();
				#$this->extReviews[$i]->reviewTitle = $tmp->title;
				$sql = "SELECT * FROM pmp_reviews_connect LEFT JOIN pmp_reviews_external ON review_id = pmp_reviews_external.id WHERE pmp_reviews_connect.id = '" . mysqli_real_escape_string($_SESSION['db'], $this->id) . "'";
				#if (isset($tmp->title)) {
				#	$sql .= " AND title = '" . mysqli_real_escape_string($_SESSION['db'], $tmp->title) . "'";
				#}
				$result2 = dbexec($sql);
				while ( $row = mysqli_fetch_object($result2) ) {
					if ( $row->type == 'imdb' ) {
						$this->extReviews[$i]->imdbRating = $row->review;
						$this->extReviews[$i]->imdbVotes = $row->votes;
						$this->extReviews[$i]->imdbTop250 = $row->top250;
						$this->extReviews[$i]->imdbBottom100 = $row->bottom100;
						if (!isset($this->imdbID)) {
							$this->imdbID = $row->ext_id;
						}
					}
					if ( $row->type == 'ofdb' ) {
						$this->extReviews[$i]->ofdbRating = $row->review;
						$this->extReviews[$i]->ofdbVotes = $row->votes;
						$this->extReviews[$i]->ofdbTop250 = $row->top250;
						$this->extReviews[$i]->ofdbBottom100 = $row->bottom100;
						if (!isset($this->ofdbID)) {
							$this->ofdbID = $row->ext_id;
						}
					}
					if ( $row->type == 'rotten_c' ) {
						$this->extReviews[$i]->rotcRating = $row->review;
						$this->extReviews[$i]->rotcVotes = $row->votes;
						if (!isset($this->rottenID)) {
							$this->rottenID = $row->ext_id;
						}
					}
					if ( $row->type == 'rotten_u' ) {
						$this->extReviews[$i]->rotuRating = $row->review;
						$this->extReviews[$i]->rotuVotes = $row->votes;
					}
				}
				$i++;
			#}
			if ($i > 0) {
				$this->reviewTitleNum = $i;
			}

			unset($this->_db);

			// My Links
			$sql = "SELECT * FROM pmp_mylinks WHERE id = '" . mysqli_real_escape_string($_SESSION['db'], $this->id) . "' ORDER BY category LIMIT 0, 300";
			$result = dbexec($sql);
			if (@mysqli_num_rows($result) > 0 ) {
				while ( $row = mysqli_fetch_object($result) ) {
					$this->MyLinks[] = $row;
				}
			}
		}
		else {
			return false;
		}
	}
}
?>
