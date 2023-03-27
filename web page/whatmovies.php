<?php
if ($_COOKIE['username'] AND $_COOKIE['password'])
{
  $SQL = "SELECT id, \"user\" FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
  $user = pg_fetch_all(pg_query($db, $SQL));
  $SQL = "SELECT a.owner, u.\"user\" FROM movies.accessrights a, web.users u WHERE a.owner = u.id AND a.permission = TRUE AND a.\"view\" = TRUE AND a.\"user\" = ".$user[0]['id']." ORDER BY u.id";
  $res = pg_query($db, $SQL);
  $others = pg_fetch_all($res);
  $rows = pg_num_rows($res);
  menu();
  $SQL = "SELECT m.title, m.id, m.year, m.grade, um.\"user\" FROM movies.movie m, movies.user_movie um WHERE m.id NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND um.movie = m.id AND (um.\"user\" = ".$user[0]['id'];
  if ($others)
  {
	  $others2 = array();
	  $own = 0;
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner'];
      if ($other['owner'] > $user[0]['id'])
	    {
		    $others2 = array_merge($others2, array(array(owner => $user[0]['id'], user => $user[0]['user'])), array($other));
		    $own = 1;
	    }
      else
	    {
		    $others2 = array_merge($others2, array($other));
	    }
	  }
	  if (!$own)
	  {
	    $others2 = array_merge($others2, array(array(owner => $user[0]['id'], user => $user[0]['user'])));
	  }
  }
  else
  {
	  $others2 = array(array(owner => $user[0]['id'], user => $user[0]['user']));
  }
  $others = $others2;
  $SQL = $SQL.") ORDER BY m.title, um.\"user\"";
  $movies = pg_fetch_all(pg_query($db, $SQL));
  echo "<TABLE BORDER = '1'>\n";
  echo "<TR>\n";
  echo "<TD>Title</TD>\n";
  echo "<TD>Year</TD>\n";
  echo "<TD>Grade</TD>\n";
  foreach ($others as $other)
  {
	  echo "<TD CLASS = 'user'>".$other['user']."</TD>\n";
  }
  echo "</TR>\n";
  $first = 1;
  foreach ($movies as $movie)
  {
	  if (!$first AND $lastMovie[0] != $movie['id'])
	  {
	    while ($rows2 < $rows + 1)
      {
		    echo "<TD></TD>\n";
		    $rows2 = $rows2 + 1;
	    }
	    echo "</TR>\n";
	  }
	  if ($lastMovie[0] == $movie['id'])
	  {
	    $rows2 = $lastMovie[1];
	    $done = 0;
      if (!$done)
	    {
	      foreach ($others as $other)
	      {
	        if ($movie['user'] == $other['owner'])
	        {
	          echo "<TD>X</TD>\n";
			      $done = 1;
			      $rows2 = $rows2 + 1;
	        }
	        if ($done)
		      {
			      break;
		      }
	      }
	    }
	  }
	  else
	  {
	    $rows2 = 0;
	    $first = 0;
	    echo "<TR>\n";
      echo "<TD><A HREF = 'index.php?what=movie&amp;movie=".$movie['id']."'>".utf8_decode($movie['title'])."</A></TD>\n";
	    echo "<TD>".$movie['year']."</TD>\n";
	    echo "<TD>".$movie['grade']."</TD>\n";
		  $done = 0;
		  if (!$done)
		  {
	      foreach ($others as $other)
	      {
	        if ($movie['user'] == $other['owner'])
	        {
	          echo "<TD>X</TD>\n";
			      $done = 1;
		        $rows2 = $rows2 + 1;
	        }
	        else
	        {
	          echo "<TD></TD>\n";
		        $rows2 = $rows2 + 1;
	        }
			    if ($done)
			    {
			      break;
			    }
	      }
		  }
	  }
	  $lastMovie = array($movie['id'], $rows2);
  }
	while ($rows2 < $rows + 1)
	{
	  echo "<TD></TD>\n";
	  $rows2 = $rows2 + 1;
	}
	echo "</TR>\n";
	echo "</TABLE>\n";
}
else
{
  noPermission();
}
?>