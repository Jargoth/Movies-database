<?php
if ($_COOKIE['username'] AND $_COOKIE['password'])
{
  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
  $user = pg_fetch_all(pg_query($db, $SQL));
  $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
  $others = pg_fetch_all(pg_query($db, $SQL));
  menu();
  $SQL = "SELECT g.title as genre, count(gm.movie) as movies, g.id FROM movies.genre g, movies.genre_movie gm, movies.user_movie um WHERE gm.movie = um.movie AND g.id = gm.genre AND g.title <> 'Adult' AND (um.\"user\" = ".$user[0]['id'];
  if ($others)
  {
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
  }
  $SQL = $SQL.") GROUP BY g.title, g.id ORDER BY g.title";
  $genres = pg_fetch_all(pg_query($db, $SQL));
  foreach($genres as $genre)
  {
	  echo "<A HREF = 'index.php?what=moviesingenre&amp;genre=".$genre['id']."'>".$genre['genre']." (".$genre['movies'].")<BR>\n";
  }
}
else
{
  noPermission();
}
?>