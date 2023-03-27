<?php
menu();
$SQL = "SELECT g.title as genre, count(gm.movie) as movies, g.id FROM movies.genre g, movies.genre_movie gm, movies.user_movie um WHERE gm.movie = um.movie AND g.id = gm.genre AND g.title <> 'Adult' GROUP BY g.title, g.id ORDER BY g.title";
$genres = pg_fetch_all(pg_query($db, $SQL));
foreach($genres as $genre)
{
	echo "<A HREF = 'index.php?what=moviesingenre&amp;genre=".$genre['id']."&amp;all=all'>".$genre['genre']." (".$genre['movies'].")<BR>\n";
}
?>