<?php
menu();
$SQL = "SELECT k.title as keyword, count(km.movie) as movies, k.id FROM movies.keyword k, movies.keyword_movie km, movies.user_movie um WHERE km.movie = um.movie AND k.id = km.keyword AND km.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) GROUP BY k.title, k.id ORDER BY k.title";
$keywords = pg_fetch_all(pg_query($db, $SQL));
foreach($keywords as $keyword)
{
	echo "<A HREF = 'index.php?what=movieswithkeyword&amp;keyword=".$keyword['id']."&amp;all=all'>".$keyword['keyword']." (".$keyword['movies'].")<BR>\n";
}
?>