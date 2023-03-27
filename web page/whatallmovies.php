<?php
menu();
$SQL = "SELECT m.title, m.id, m.year, m.grade FROM movies.movie m, movies.user_movie um WHERE m.id NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND um.movie = m.id  ORDER BY title";
$movies = pg_fetch_all(pg_query($db, $SQL));
echo "<TABLE BORDER = '1'>\n";
echo "<TR>\n";
echo "<TD>Title</TD>\n";
echo "<TD>Year</TD>\n";
echo "<TD>Grade</TD>\n";
echo "</TR>\n";
foreach ($movies as $movie)
{
	echo "<TR>\n";
  echo "<TD><A HREF = 'index.php?what=movie&amp;movie=".$movie['id']."'>".utf8_decode($movie['title'])."</A></TD>\n";
	echo "<TD>".$movie['year']."</TD>\n";
	echo "<TD>".$movie['grade']."</TD>\n";
	echo "</TR>\n";
}
echo "</TABLE>\n";
?>