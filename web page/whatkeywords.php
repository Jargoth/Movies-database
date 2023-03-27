<?php
if ($_COOKIE['username'] AND $_COOKIE['password'])
{
  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
  $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
	$others = pg_fetch_all(pg_query($db, $SQL));
  menu();
  $SQL = "SELECT k.title as keyword, count(km.movie) as movies, k.id FROM movies.keyword k, movies.keyword_movie km, movies.user_movie um WHERE km.movie = um.movie AND k.id = km.keyword AND km.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND (um.\"user\" = ".$user[0]['id'];
	if ($others)
	{
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
	}
  $SQL = $SQL.") GROUP BY k.title, k.id ORDER BY k.title";
	$keywords = pg_fetch_all(pg_query($db, $SQL));
	foreach($keywords as $keyword)
	{
	  echo "<A HREF = 'index.php?what=movieswithkeyword&amp;keyword=".$keyword['id']."'>".$keyword['keyword']." (".$keyword['movies'].")<BR>\n";
	}
}
else
{
  noPermission();
}
?>