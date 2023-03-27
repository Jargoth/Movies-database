<?php
function menu()
{
  echo "<div class = 'menuminwidth0'>\n";
  echo "<div class = 'menu'>\n";
  echo "<ul>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<img src = 'images/movies.gif' alt = '- MOVIES -' title = '' height = '18' width = '112'/>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]--><!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul class = 'skinny'>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>List Movies</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=movies'>&nbsp;My Movies</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allmovies'>&nbsp;All Movies</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li>\n";
    echo "<a>\n";
    echo "<span class = 'drop'>\n";
    echo "<span>Manage Movies</span>&#187;\n";
    echo "</span>\n";
    echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
    echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
    echo "<ul>\n";
    echo "<li><a href = 'index.php?what=addmovies'>&nbsp;Add Movies</a></li>\n";
    echo "<li><a href = 'index.php?what=delmovies'>&nbsp;Delete Movies</a></li>\n";
    echo "</ul>\n";
    echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
    echo "</li>\n";
  }
  echo '
            </ul>
            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>';
  echo "<li>\n";
  echo "<a>\n";
  echo "<img src = 'images/genres.gif' alt = '- GENRES -' title = '' height = '18' width = '112'/>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]--><!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul class = 'skinny'>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>List Genres</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=genres'>&nbsp;My Genres</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allgenres'>&nbsp;All Genres</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo '
            </ul>
            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>';
  echo "<li>\n";
  echo "<a>\n";
  echo "<img src = 'images/keywords.gif' alt = '- KEYWORDS -' title = '' height = '18' width = '112'/>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]--><!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul class = 'skinny'>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>List Keywords</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=keywords'>&nbsp;My keywords</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allkeywords'>&nbsp;All keywords</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo '
            </ul>
            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>';
  echo "<li>\n";
  echo "<a>\n";
  echo "<img src = 'images/persons.gif' alt = '- PERSONS -' title = '' height = '18' width = '112'/>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]--><!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul class = 'skinny'>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>Directors</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=directors'>&nbsp;List My</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=alldirectors'>&nbsp;List All</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>Writers</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=writers'>&nbsp;List My</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allwriters'>&nbsp;List All</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>Actors</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=actors'>&nbsp;List My</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allactors'>&nbsp;List All</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo "<li>\n";
  echo "<a>\n";
  echo "<span class = 'drop'>\n";
  echo "<span>Producers</span>&#187;\n";
  echo "</span>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
  echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li><a href = 'index.php?what=producers'>&nbsp;List My</a></li>\n";
  }
  echo "<li><a href = 'index.php?what=allproducers'>&nbsp;List All</a></li>\n";
  echo "</ul>\n";
  echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
  echo "</li>\n";
  echo '
            </ul>
            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>';
  echo "<li>\n";
  echo "<a>\n";
  echo "<img src = 'images/settings.gif' alt = '- SETTINGS -' title = '' height = '18' width = '112'/>\n";
  echo "<!--[if gt IE 6]><!--></a><!--<![endif]--><!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
  echo "<ul class = 'skinny'>\n";
  if ($_COOKIE['username'] AND $_COOKIE['password'])
  {
    echo "<li>\n";
    echo "<a>\n";
    echo "<span class = 'drop'>\n";
    echo "<span>Manage Movies</span>&#187;\n";
    echo "</span>\n";
    echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
    echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
    echo "<ul>\n";
    echo "<li><a href = 'index.php?what=addmovies'>&nbsp;Add Movies</a></li>\n";
    echo "<li><a href = 'index.php?what=delmovies'>&nbsp;Delete Movies</a></li>\n";
    echo "</ul>\n";
    echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
    echo "</li>\n";
    echo "<li>\n";
    echo "<a href = 'index.php?what=editpersonaldata'>\n";
    echo "<span class = 'drop'>\n";
    echo "<span>Edit Personal Data</span>\n";
    echo "</span>\n";
    echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
    echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";

    echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
    echo "</li>\n";
    echo "<li>\n";
    echo "<a>\n";
    echo "<span class = 'drop'>\n";
    echo "<span>Other Users</span>&#187;\n";
    echo "</span>\n";
    echo "<!--[if gt IE 6]><!--></a><!--<![endif]-->\n";
    echo "<!--[if lt IE 7]><table border='0' cellpadding='0' cellspacing='0'><tr><td><![endif]-->\n";
    echo "<ul>\n";
    echo "<li><a href = 'index.php?what=grantaccess'>&nbsp;Grant Accessright</a></li>\n";
    echo "<li><a href = 'index.php?what=useaccess'>&nbsp;Use Accessrights</a></li>\n";
    echo "</ul>\n";
    echo "<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n";
    echo "</li>\n";
  }
  echo '
            </ul>
            <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>';
  echo '
        </ul>
        <!--[if lte IE 6]></td></tr></table></a><![endif]-->
      </div>
    </div>
	<div>';
  echo "<TABLE>\n";
  echo "<TR>\n";
  echo "<TD ALIGN = 'left'>\n";
  echo "<FORM ACTION = 'index.php?what=search' METHOD = 'POST'>\n";
  echo "<INPUT TYPE = 'text' NAME = 'search1' ID = 'search1'>\n";
  echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'find'>\n";
  echo "</FORM>\n";
  echo "</TD>\n";
  echo "</TR>\n";
  echo "</TABLE>\n";
  if (!$_COOKIE['username'] OR !$_COOKIE['password'])
  {
    echo "<FORM ACTION = 'index.php?login=login' METHOD = 'POST'>\n";
    echo "User: <INPUT TYPE = 'text' NAME = 'username' ID = 'username'>\n";
    echo "Password: <INPUT TYPE = 'password' NAME = 'password' ID = 'password'>\n";
    echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'login'>\n";
    echo "</FORM>\n";
  }
  else
  {
    include "../www/db.php";
    $SQL = "SELECT fname, lname, id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
	echo utf8_decode($user[0]['fname'])." ".utf8_decode($user[0]['lname'])." <A HREF = 'index.php?login=logout'>logout</A>";
  }
  echo "<BR>\n";
}
?>