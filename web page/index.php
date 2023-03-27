<?php
include "functions/menu.php";
include "functions/noPermission.php";
include "../www/db.php";
if ($_GET['login'] == 'login')
{
  include "loginlogin.php";
}
if ($_GET['login'] == 'logout')
{
  include "loginlogout.php";
}
if ($_GET['what'] == 'editpersonaldata' AND $_COOKIE['username'] AND $_COOKIE['password'])
{
  if ($_POST['pwd'])
  {
	  if ($_POST['pwd'] == $_POST['pwd2'])
	  {
	    $SQL = "UPDATE web.users SET pwd = md5('".$_POST['pwd']."') WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	    pg_query($db, $SQL);
      $SQL = "SELECT md5('".$_POST['pwd']."')";
	    $pwd = pg_fetch_all(pg_query($db, $SQL));
	    setcookie('password', $pwd[0]['md5'], time()+60*60*24*365*5);
	  }
  }
}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo "<HTML>\n";
echo "<HEAD>\n";
if ($_GET['login'] == 'login' OR $_GET['login'] == 'logout')
{
  $php_self = split('/', $_SERVER['PHP_SELF']);
  echo "<meta http-equiv='Refresh' content='0; url=http://".$_SERVER['SERVER_NAME']."/".$php_self[1]."'>\n";
}
echo '    <meta http-equiv = "Content-Style-Type" content = "text/css"/>
    <link
      rel = "stylesheet"
      media = "all"
      type = "text/css"
      href = "cssmenu.css"/>';
echo "</HEAD>\n";
echo "<BODY>\n";
include "../www/db.php";
if (isset($_GET['what']))
{
  if ($_GET['what'] == 'movies')
  {
    include "whatmovies.php";
  }
  elseif ($_GET['what'] == 'allmovies')
  {
    include "whatallmovies.php";
  }
  elseif ($_GET['what'] == 'genres')
  {
    include "whatgenres.php";
  }
  elseif ($_GET['what'] == 'allgenres')
  {
    include "whatallgenres.php";
  }
  elseif ($_GET['what'] == 'moviesingenre')
  {
    include "whatmoviesingenre.php";
  }
  elseif ($_GET['what'] == 'keywords')
  {
    include "whatkeywords.php";
  }
  elseif ($_GET['what'] == 'allkeywords')
  {
    include "whatallkeywords.php";
  }
  elseif ($_GET['what'] == 'movieswithkeyword')
  {
    include "whatmovieswithkeyword.php";
  }
  elseif ($_GET['what'] == 'directors' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
    $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
	$others = pg_fetch_all(pg_query($db, $SQL));
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 1 AND (um.\"user\" = ".$user[0]['id'];
	if ($others)
	{
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
	}
	$SQL = $SQL.") GROUP BY p.name, p.id ORDER BY p.name";
	$directors = pg_fetch_all(pg_query($db, $SQL));
	foreach ($directors as $director)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$director['id']."'>".$director['name']." (".$director['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'alldirectors')
  {
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 1 GROUP BY p.name, p.id ORDER BY p.name";
	$directors = pg_fetch_all(pg_query($db, $SQL));
	foreach ($directors as $director)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$director['id']."'>".$director['name']." (".$director['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'writers' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
    $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
	$others = pg_fetch_all(pg_query($db, $SQL));
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 2 AND (um.\"user\" = ".$user[0]['id'];
	if ($others)
	{
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
	}
	$SQL = $SQL.") GROUP BY p.name, p.id ORDER BY p.name";
	$writers = pg_fetch_all(pg_query($db, $SQL));
	foreach ($writers as $writer)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$writer['id']."'>".$writer['name']." (".$writer['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'allwriters')
  {
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 2 GROUP BY p.name, p.id ORDER BY p.name";
	$writers = pg_fetch_all(pg_query($db, $SQL));
	foreach ($writers as $writer)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$writer['id']."'>".$writer['name']." (".$writer['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'actors' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
    $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
	$others = pg_fetch_all(pg_query($db, $SQL));
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 3 AND (um.\"user\" = ".$user[0]['id'];
	if ($others)
	{
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
	}
	$SQL = $SQL.") GROUP BY p.name, p.id ORDER BY p.name";
	$actors = pg_fetch_all(pg_query($db, $SQL));
	foreach ($actors as $actor)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$actor['id']."'>".$actor['name']." (".$actor['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'allactors')
  {
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 3 GROUP BY p.name, p.id ORDER BY p.name";
	$actors = pg_fetch_all(pg_query($db, $SQL));
	foreach ($actors as $actor)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$actor['id']."'>".$actor['name']." (".$actor['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'producers' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
    $SQL = "SELECT owner FROM movies.accessrights WHERE permission = TRUE AND \"view\" = TRUE AND \"user\" = ".$user[0]['id'];
	$others = pg_fetch_all(pg_query($db, $SQL));
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 4 AND (um.\"user\" = ".$user[0]['id'];
	if ($others)
	{
	  foreach ($others as $other)
	  {
	    $SQL = $SQL." OR um.\"user\" = ".$other['owner']; 
	  }
	}
	$SQL = $SQL.") GROUP BY p.name, p.id ORDER BY p.name";
	$producers = pg_fetch_all(pg_query($db, $SQL));
	foreach ($producers as $producer)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$producer['id']."'>".$producer['name']." (".$producer['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'allproducers')
  {
    menu();
    $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm, movies.user_movie um WHERE um.movie = pm.movie AND p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.type = 4 GROUP BY p.name, p.id ORDER BY p.name";
	$producers = pg_fetch_all(pg_query($db, $SQL));
	foreach ($producers as $producer)
	{
	  echo "<A HREF = 'index.php?what=person&amp;person=".$producer['id']."'>".$producer['name']." (".$producer['movies'].")</A><BR>\n";
	}
  }
  elseif ($_GET['what'] == 'person')
  {
    if (isset($_GET['person']))
	{
	  menu();
	  $personTypes = array(array('Director', 1), array('Writer', 2), array('Actor', 3), array('Producer', 4));
	  foreach ($personTypes as $personType)
	  {
	    if ($_COOKIE['username'] AND $_COOKIE['password'])
	    {
          $SQL = "SELECT id, \"user\" FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	      $user = pg_fetch_all(pg_query($db, $SQL));
          $SQL = "SELECT a.owner, u.\"user\" FROM movies.accessrights a, web.users u WHERE u.id = a.owner AND a.permission = TRUE AND a.\"view\" = TRUE AND a.\"user\" = ".$user[0]['id'];
	      $res = pg_query($db, $SQL);
	      $others = pg_fetch_all($res);
	      $rows = pg_num_rows($res);
	      $SQL = "SELECT m.title, m.year, m.id, m.grade, pm.description, um.\"user\" FROM movies.movie m, movies.person_movie pm, movies.user_movie um WHERE um.movie = m.id AND m.id = pm.movie AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND pm.person = ".$_GET['person']."AND type = ".$personType[1]." AND (um.\"user\" = ".$user[0]['id'];
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
	    }
	    else
	    {
	      $SQL = "SELECT m.title, m.id, m.year, m.grade FROM movies.movie m, movies.genre_movie gm, movies.user_movie um WHERE m.id = gm.movie AND m.id NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND gm.movie = um.movie AND gm.genre = ".$_GET['genre']." ORDER BY m.title";
	    }
		$movies = pg_fetch_all(pg_query($db, $SQL));
		if ($movies)
		{
		  $lastMovie[0] = 0;
		  echo "<BR>".$personType[0]."<BR>\n";
	      echo "<TABLE BORDER = '1'>\n";
	      echo "<TR>\n";
	      echo "<TD>Title</TD>\n";
	      echo "<TD>Year</TD>\n";
	      echo "<TD>Grade</TD>\n";
	      echo "<TD>What</TD>\n";
	      if ($_COOKIE['username'] AND $_COOKIE['password'])
	      {
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
			    echo "<TD>".$movie['description']."</TD>\n";
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
	      }
	      else
	      {
	        echo "</TR>\n";
		  }
		  echo "</TABLE>\n";
		}
	  }
	}
  }
  elseif ($_GET['what'] == 'movie')
  {
    if (isset($_GET['movie']))
	{
	  menu();
      $SQL = "SELECT m.title, m.grade, m.year, m.\"numberOfVotes\" FROM movies.movie m WHERE m.id = ".$_GET['movie'];
	  $movie = pg_fetch_all(pg_query($db, $SQL));
	  echo "<H2>".utf8_decode($movie[0]['title'])." (".$movie[0]['year'].")</H2><BR>\n";
	  if ($movie[0]['grade'])
	  {
	    echo "Grade: ".$movie[0]['grade']." (".$movie[0]['numberOfVotes']." votes)<BR><BR>\n";
	  }
	  $SQL = "SELECT g.title, g.id FROM movies.genre g, movies.genre_movie gm WHERE g.id = gm.genre AND gm.movie = ".$_GET['movie']." ORDER BY g.title";
	  $genres = pg_fetch_all(pg_query($db, $SQL));
	  $i = 0;
	  if ($genres)
	  {
	    foreach ($genres as $genre)
	    {
	      if ($i > 0)
		  {
		    echo ", ";
		  }
		  echo "<A HREF = 'index.php?what=moviesingenre&amp;genre=".$genre['id']."'>".$genre['title']."</A>";
		  $i = $i +1;
	    }
	    echo "<BR><BR>\n";
	  }
	  $SQL = "SELECT k.title, k.id FROM movies.keyword k, movies.keyword_movie km WHERE k.id = km.keyword AND km.movie = ".$_GET['movie']." ORDER BY k.title";
	  $keywords = pg_fetch_all(pg_query($db, $SQL));
	  $i = 0;
	  if ($keywords)
	  {
	    foreach ($keywords as $keyword)
	    {
	      if ($i > 0)
		  {
		    echo ", ";
		  }
		  echo "<A HREF = 'index.php?what=movieswithkeyword&amp;keyword=".$keyword['id']."'>".$keyword['title']."</A>";
		  $i = $i +1;
	    }
	    echo "<BR><BR>\n";
	  }
	  $SQL = "SELECT p.content, u.name FROM movies.\"plotSummary\" p, movies.\"user\" u WHERE p.\"user\" = u.id AND p.movie = ".$_GET['movie'];
	  $plots = pg_fetch_all(pg_query($db, $SQL));
	  if ($plots)
	  {
	    foreach ($plots as $plot)
	    {
	      echo "<DIV>".$plot['content']."<BR>Written by: ".$plot['name']."</DIV><BR>\n";
	    }
	  }
	  $personTypes = array(array('Director', 1), array('Writer', 2), array('Actor', 3), array('Producer', 4));
	  foreach ($personTypes as $personType)
	  {
	    $SQL = "SELECT p.name, p.id, pm.description FROM movies.person p, movies.person_movie pm WHERE p.id = pm.person AND pm.movie = ".$_GET['movie']."AND type = ".$personType[1]." ORDER BY pm.\"order\", p.name";
	    $persons = pg_fetch_all(pg_query($db, $SQL));
		if ($persons)
		{
		  echo "<BR>".$personType[0]."<BR><BR>\n";
	      foreach ($persons as $person)
	      {
		    if (substr($person['description'], 0, 7) == 'nothing')
			{
			  $person['description'] = '';
			}
	        echo "<A HREF = 'index.php?what=person&amp;person=".$person['id']."'>".$person['name']."</A> ".$person['description']."<BR>\n";
	      }
		}
	  }
	}
  }
  elseif ($_GET['what'] == 'search')
  {
    if ($_POST['submit'] == 'find' AND $_POST['search1'])
	{
      menu();
	  $_POST['search1'] = utf8_encode($_POST['search1']);
	  if ($_COOKIE['username'] AND $_COOKIE['password'])
	  {
        $SQL = "SELECT id, \"user\" FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	    $user = pg_fetch_all(pg_query($db, $SQL));
        $SQL = "SELECT a.owner, u.\"user\" FROM movies.accessrights a, web.users u WHERE u.id = a.owner AND a.permission = TRUE AND a.\"view\" = TRUE AND a.\"user\" = ".$user[0]['id'];
	    $res = pg_query($db, $SQL);
	    $others = pg_fetch_all($res);
	    $rows = pg_num_rows($res);
        $SQL = "SELECT m.title, m.id, m.year, m.grade, um.\"user\" FROM movies.movie m, movies.user_movie um WHERE um.movie = m.id AND id NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND title ILIKE '%".$_POST['search1']."%' AND (um.\"user\" = ".$user[0]['id'];
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
	  }
	  else
	  {
        $SQL = "SELECT title, id, year, grade FROM movies.movie WHERE id NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND title ILIKE '%".$_POST['search1']."%' ORDER BY title";
	  }
      $movies = pg_fetch_all(pg_query($db, $SQL));
	  if ($movies)
	  {
	    echo "Movies\n";
	    echo "<TABLE BORDER = '1'>\n";
	    echo "<TR>\n";
	    echo "<TD>Title</TD>\n";
	    echo "<TD>Year</TD>\n";
	    echo "<TD>Grade</TD>\n";
	    if ($_COOKIE['username'] AND $_COOKIE['password'])
	    {
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
	    }
	    else
	    {
	      echo "</TR>\n";
          foreach ($movies as $movie)
          {
	        echo "<TR>\n";
            echo "<TD><A HREF = 'index.php?what=movie&amp;movie=".$movie['id']."'>".utf8_decode($movie['title'])."</A></TD>\n";
	        echo "<TD>".$movie['year']."</TD>\n";
	        echo "<TD>".$movie['grade']."</TD>\n";
	        echo "</TR>\n";
          }
	    }
	    echo "</TABLE>\n";
	  }
      $SQL = "SELECT g.title as genre, count(gm.movie) as movies, g.id FROM movies.genre g, movies.genre_movie gm WHERE g.id = gm.genre AND g.title <> 'Adult' AND g.title ILIKE '%".$_POST['search1']."%' GROUP BY g.title, g.id ORDER BY g.title";
	  $genres = pg_fetch_all(pg_query($db, $SQL));
	  if ($genres)
	  {
	    echo "<BR>Genres<BR>\n";
	    foreach($genres as $genre)
	    {
	    echo "<A HREF = 'index.php?what=moviesingenre&amp;genre=".$genre['id']."'>".$genre['genre']."</A> (".$genre['movies'].")<BR>\n";
	    }
	  }
      $SQL = "SELECT k.title as keyword, count(km.movie) as movies, k.id FROM movies.keyword k, movies.keyword_movie km WHERE k.id = km.keyword AND km.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND k.title ILIKE '%".$_POST['search1']."%' GROUP BY k.title, k.id ORDER BY k.title";
	  $keywords = pg_fetch_all(pg_query($db, $SQL));
	  if ($keywords)
	  {
	    echo "<BR>Keywords<BR>\n";
	    foreach($keywords as $keyword)
	    {
	      echo "<A HREF = 'index.php?what=movieswithkeyword&amp;keyword=".$keyword['id']."'>".$keyword['keyword']."</A> (".$keyword['movies'].")<BR>\n";
	    }
	  }
      $SQL = "SELECT p.name, p.id, count(pm.movie) AS movies FROM movies.person p, movies.person_movie pm WHERE p.id = pm.person AND pm.movie NOT IN (SELECT gm.movie FROM movies.genre_movie gm, movies.genre g WHERE g.title = 'Adult' AND g.id = gm.genre) AND p.name ILIKE '%".$_POST['search1']."%' GROUP BY p.name, p.id ORDER BY p.name";
	  $persons = pg_fetch_all(pg_query($db, $SQL));
	  if ($persons)
	  {
	    echo "<BR>Persons<BR>\n";
	    foreach ($persons as $person)
	    {
	      echo "<A HREF = 'index.php?what=person&amp;person=".$person['id']."'>".$person['name']." (".$person['movies'].")</A><BR>\n";
	    }
	  }
	}
  }
  elseif ($_GET['what'] == 'addmovies' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    menu();
	if (!isset($_GET['by']))
	{
		$SQL = "SELECT movies_newmovie FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
		$temp = pg_fetch_all(pg_query($db, $SQL));
		$_GET['by'] = $temp[0]['movies_newmovie'];
	}
	/*echo "<TABLE WIDTH = 200pt><TR><TD><A HREF = 'index.php?what=addmovies&amp;by=name'>By name</A></TD><TD><A HREF = 'index.php?what=addmovies&amp;by=imdb'>By imdb#</A></TD></TD></TABLE>\n";*/
    if ($_POST['submit'] == 'submit' AND $_GET['by'] == 'imdb')
	{
	  if (substr($_POST['imdb'], 0, 28) == 'http://www.imdb.com/title/tt' AND is_numeric(substr($_POST['imdb'], 28, 7)) AND substr($_POST['imdb'], 35, 1) == '/')
	  {
        $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	    $user = pg_fetch_all(pg_query($db, $SQL));
	    $SQL = "SELECT id FROM movies.movie WHERE imdb = '".$_POST['imdb']."'";
		$movie = pg_fetch_all(pg_query($db, $SQL));
		if ($movie[0]['id'])
		{
		  $SQL = "SELECT movie FROM movies.user_movie WHERE \"user\" = ".$user[0]['id']." AND movie = ".$movie[0]['id'];
		  $res = pg_fetch_all(pg_query($db, $SQL));
		  if ($res[0]['movie'])
		  {
		    echo "<DIV CLASS = 'error'>This movie already exists in your collection.</DIV>\n";
		  }
		  else
		  {
		    $SQL = "INSERT INTO movies.user_movie (\"user\", movie) VALUES (".$user[0]['id'].", ".$movie[0]['id'].")";
	        pg_query($db, $SQL);
		    echo "<DIV CLASS = 'message'>Another user already has this movie in his/hers collection, and it was now added to your collection as well.</DIV>\n";
		  }
		}
		else
		{
		  if ($_POST['title'] != '')
		  {
		    $SQL = "INSERT INTO movies.movie (title, imdb) VALUES ('".utf8_encode($_POST['title'])."', '".utf8_encode($_POST['imdb'])."')";
			pg_query($db, $SQL);
			$SQL = "INSERT INTO movies.user_movie (\"user\", movie) VALUES (".$user[0]['id'].", currval('movies.movie_id_seq'))";
			pg_query($db, $SQL);
			echo "<DIV CLASS = 'message'>The movie did't exist in our database, but it has now been addedd. Both to your collection and the database.</DIV>\n";
		  }
		  else
		  {
		    echo "<DIV CLASS = 'error'>No title entered</DIV>\n";
		  }
		}
	  }
	  else
	  {
	    echo "<DIV CLASS = 'error'>Please enter a complete imdb addres like this: http://www.imdb.com/title/tt0068646/</DIV>\n";
	  }
	}
    elseif ($_POST['submit'] == 'submit' AND $_GET['by'] == 'name')
	{
		$SQL = "SELECT title, \"year\", id FROM movies.movie WHERE title ILIKE '%".utf8_encode($_POST['title'])."%' ORDER BY title, \"year\"";
		$movies = pg_fetch_all(pg_query($db, $SQL));
		echo "<TABLE><FORM ACTION = 'index.php?what=addmovies&amp;by=name' METHOD = 'post'>\n";
		if ($movies)
		{
			foreach ($movies as $movie)
			{
				echo "<TR><TD>\n";
				echo "<INPUT TYPE = 'radio' VALUE = '".$movie['id']."' NAME = 'movie'>\n";
				echo "</TD><TD>\n";
				echo "<TABLE BORDER = 1pt><TR><TD>";
				echo "<TABLE>";
				echo "<TR><TD>".utf8_decode($movie['title']);
				if ($movie['year'])
				{
					echo " (".$movie['year'].")";
				}
				echo "</TD></TR>\n";
				$SQL = "SELECT p.name FROM movies.person_movie pm, movies.person p WHERE pm.movie = ".$movie['id']." AND pm.\"type\" = 1 AND pm.person = p.id";
				$directors = pg_fetch_all(pg_query($db, $SQL));
				if ($directors)
				{
					$d = 0;
					echo "<TR><TD><TABLE>\n";
					foreach ($directors as $director)
					{
						echo "<TR><TD>";
						if ($d == 0)
						{
							echo "Director: ";
							$d = 1;
						}
						echo "</TD><TD>".utf8_decode($director['name'])."</TD></TR>\n";
					}
					echo "</TABLE></TD></TR>\n";
				}
				$SQL = "SELECT p.name FROM movies.person_movie pm, movies.person p WHERE pm.movie = ".$movie['id']." AND pm.\"type\" = 3 AND pm.person = p.id ORDER BY pm.\"order\", p.name LIMIT 3";
				$actors = pg_fetch_all(pg_query($db, $SQL));
				if ($actors)
				{
					$d = 0;
					echo "<TR><TD><TABLE>\n";
					foreach ($actors as $actor)
					{
						echo "<TR><TD>";
						if ($d == 0)
						{
							echo "Actor: ";
							$d = 1;
						}
						echo "</TD><TD>".utf8_decode($actor['name'])."</TD></TR>\n";
					}
					echo "</TABLE></TD></TR>\n";
				}
				echo "</TABLE>";
				echo "</TD></TR></TABLE>";
				echo "</TD></TR>";
			}
		}
		echo "<TR>\n";
		echo "<TD>\n";
		echo "<INPUT TYPE = 'radio' VALUE = 'new' NAME = 'movie' CHECKED>\n";
		echo "</TD><TD>\n";
		echo "<TABLE BORDER = 1pt><TR><TD>";
		echo "<INPUT TYPE = 'text' VALUE = '".$_POST['title']."' NAME = 'title' ID = 'title' SIZE = '60'><BR>\n";
		echo "<INPUT TYPE = 'text' VALUE = 'year' NAME = 'year' ID = 'year' SIZE = '4'><BR>\n";
		echo "<INPUT TYPE = 'text' VALUE = 'director' NAME = 'director' ID = 'director' SIZE = '60'><BR>\n";
		echo "<INPUT TYPE = 'text' VALUE = 'actor' NAME = 'actor1' ID = 'actor1' SIZE = '60'><BR>\n";
		echo "<INPUT TYPE = 'text' VALUE = 'actor' NAME = 'actor2' ID = 'actor2' SIZE = '60'><BR>\n";
		echo "<INPUT TYPE = 'text' VALUE = 'actor' NAME = 'actor3' ID = 'actor3' SIZE = '60'><BR>\n";
		echo "</TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "<INPUT TYPE = 'submit' NAME = 'submit2' ID = 'submit2' VALUE = 'submit'>\n";
		echo "</FORM></TABLE>";
	}
    elseif ($_POST['submit2'] == 'submit' AND $_GET['by'] == 'name')
	{
		if ($_POST['movie'] != 'new')
		{
			$SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
			$user = pg_fetch_all(pg_query($db, $SQL));
			if ($user)
			{
				$SQL = "SELECT \"user\" FROM movies.movie WHERE \"user\" = ".$user[0]['id']." AND movie = ".$_POST['movie'];
				$movie = pg_fetch_all(pg_query($db, $SQL));
				if ($movie)
				{
					$SQL = "INSERT INTO movies.user_movie (\"user\", movie) VALUES (".$user[0]['id'].", ".$_POST['movie'].")";
					pg_query($db, $SQL);
				}
			}
		}
		else
		{
			$SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
			$user = pg_fetch_all(pg_query($db, $SQL));
			if ($user)
			{
				$SQL = "INSERT INTO movies.new_movie (title, year, director, actor1, actor2, actor3, \"user\") VALUES ('".utf8_encode($_POST['title'])."', '".utf8_encode($_POST['year'])."', '".utf8_encode($_POST['director'])."', '".utf8_encode($_POST['actor1'])."', '".utf8_encode($_POST['actor2'])."', '".utf8_encode($_POST['actor3'])."', ".$user[0]['id'].")";
				pg_query($db, $SQL) OR die();
				echo "Successfully submited ".$_POST['title'].". Please allow a few days for it to be added to your collection.";
			}
		}
	}
	if ($_GET['by'] == 'imdb')
	{
		echo "<FORM ACTION = 'index.php?what=addmovies&amp;by=imdb' METHOD = 'post'>\n";
		echo "Title: <INPUT TYPE = 'text' NAME = 'title' ID = 'title'>\n";
		echo "IMDB: <INPUT TYPE = 'text' NAME = 'imdb' ID = 'imdb'>\n";
		echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'submit'>\n";
		echo "</FORM>\n";
	}
	elseif ($_GET['by'] == 'name' AND !isset($_POST['submit']))
	{
		echo "<FORM ACTION = 'index.php?what=addmovies&amp;by=name' METHOD = 'post'>\n";
		echo "Movie Title: <INPUT TYPE = 'text' NAME = 'title' ID = 'title'>\n";
		echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'submit'>\n";
		echo "</FORM>\n";
	}
  }
  elseif ($_GET['what'] == 'delmovies' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    menu();
	$first = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'OTHER');
	foreach ($first as $letter)
	{
	  echo "<A HREF = 'index.php?what=delmovies&amp;letter=".$letter."'>".$letter."</A> \n";
	}
	if (isset($_GET['letter']))
	{
      $SQL = "SELECT id FROM web.users WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	  $user = pg_fetch_all(pg_query($db, $SQL));
	  if ($user[0]['id'] > 0 AND isset($_POST['movie']))
	  {
	    $SQL = "DELETE FROM movies.user_movie WHERE \"user\" = ".$user[0]['id']." AND movie = ".$_POST['movie'];
		pg_query($SQL);
		$SQL = "SELECT title FROM movies.movie WHERE id = ".$_POST['movie'];
		$res = pg_fetch_all(pg_query($db, $SQL));
		echo "<DIV CLASS = 'message'>".utf8_decode($res[0]['title'])." was removed from your collection.</DIV>\n";
	  }
	  if ($_GET['letter'] == 'OTHER')
	  {
	    $SQL = "SELECT m.title, m.id FROM movies.movie m, movies.user_movie um WHERE um.movie = m.id AND um.\"user\" = ".$user[0]['id']." AND m.title NOT SIMILAR TO '(a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z)%' ORDER BY m.title";
	  }
	  else
	  {
	    $SQL = "SELECT m.title, m.id FROM movies.movie m, movies.user_movie um WHERE um.movie = m.id AND um.\"user\" = ".$user[0]['id']." AND m.title ILIKE '".$_GET['letter']."%' ORDER BY m.title";
	  }
	  $movies = pg_fetch_all(pg_query($db, $SQL));
	  echo "<FORM ACTION = 'index.php?what=delmovies&amp;letter=".$_GET['letter']."' METHOD = 'post'>\n";
      echo "<SELECT NAME = 'movie' ID = 'movie'>\n";
	  foreach ($movies as $movie)
	  {
	    echo "<OPTION VALUE = '".$movie['id']."'>".utf8_decode($movie['title'])."\n";
	  }
	  echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'DELETE'>\n";
	  echo "</FORM>\n";
	}
  }
  elseif ($_GET['what'] == 'editpersonaldata' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    menu();
	$SQL = "SELECT fname, lname FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	$user = pg_fetch_all(pg_query($db, $SQL));
	if ($_POST['fname'] != utf8_decode($user[0]['fname']) AND $_POST['fname'])
	{
	  $SQL = "UPDATE web.users SET fname = '".utf8_encode($_POST['fname'])."' WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	  pg_query($db, $SQL);
	  echo "<DIV CLASS = 'message'>Updated first name.</DIV>\n";
	  $user[0]['fname'] = $_POST['fname'];
	}
	if ($_POST['lname'] != utf8_decode($user[0]['lname']) AND $_POST['lname'])
	{
	  $SQL = "UPDATE web.users SET lname = '".utf8_encode($_POST['lname'])."' WHERE \"user\" = '".$_COOKIE['username']."' AND pwd = '".$_COOKIE['password']."'";
	  pg_query($db, $SQL);
	  echo "<DIV CLASS = 'message'>Updated last name.</DIV>\n";
	  $user[0]['lname'] = $_POST['lname'];
	}
	if ($_POST['pwd'])
	{
	  if ($_POST['pwd'] == $_POST['pwd2'])
	  {
	    echo "<DIV CLASS = 'message'>Updated password.</DIV>\n";
	  }
	  else
	  {
	    echo "<DIV CLASS = 'error'>Repeat the password exactly. (Case Sensitive)</DIV>\n";
	  }
	}
	echo "<FORM ACTION = 'index.php?what=editpersonaldata' METHOD = 'post'>\n";
	echo "First Name: <INPUT TYPE = 'text' NAME = 'fname' ID = 'fname' VALUE = '".utf8_decode($user[0]['fname'])."'>\n";
	echo "Last Name: <INPUT TYPE = 'text' NAME = 'lname' ID = 'lname' VALUE = '".utf8_decode($user[0]['lname'])."'>\n";
	echo "Password: <INPUT TYPE = 'password' NAME = 'pwd' ID = 'pwd'>\n";
	echo "Again: <INPUT TYPE = 'password' NAME = 'pwd2' ID = 'pwd2'>\n";
	echo "<INPUT TYPE = 'submit' NAME = 'submit' ID = 'submit' VALUE = 'SUBMIT'>\n";
	echo "</FORM>\n";
  }
  elseif ($_GET['what'] == 'grantaccess' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    menu();
	if ($_GET['grant'] == 'grant' AND isset($_GET['user']))
	{
	  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	  $user = pg_fetch_all(pg_query($db, $SQL));
	  $SQL = "SELECT owner FROM movies.accessrights WHERE owner = ".$user[0]['id']." AND \"user\" = ".$_GET['user'];
	  $res = pg_fetch_all(pg_query($db, $SQL));
	  if ($res[0]['owner'] > 0)
	  {
	    $SQL = "UPDATE movies.accessrights SET permission = TRUE WHERE owner = ".$user[0]['id']." AND \"user\" = ".$_GET['user'];
	    pg_query($db, $SQL);
	  }
	  else
	  {
	    $SQL = "INSERT INTO movies.accessrights(owner, \"user\", permission) VALUES (".$user[0]['id'].", ".$_GET['user'].", TRUE)";
	    pg_query($db, $SQL);
	  }
	}
	if ($_GET['grant'] == 'revoke' AND isset($_GET['user']))
	{
	  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	  $user = pg_fetch_all(pg_query($db, $SQL));
	  $SQL = "UPDATE movies.accessrights SET permission = FALSE WHERE owner = ".$user[0]['id']." AND \"user\" = ".$_GET['user'];
	  pg_query($db, $SQL);
	}
	$SQL = "SELECT u.id, u.fname, u.lname, u.\"user\" FROM web.users u, movies.accessrights a WHERE a.\"user\" = u.id AND a.owner IN (SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."') AND a.permission = TRUE AND u.movies = TRUE ORDER BY u.lname, u.fname, u.\"user\"";
	$users = pg_fetch_all(pg_query($db, $SQL));
	if ($users)
	{
	  echo "<DIV CLASS = 'rubrik3'>These users has the right to view your collection. To revoke the right simply click on the person.</DIV>\n";
	  foreach ($users as $user)
	  {
	    echo "<DIV><A HREF = 'index.php?what=grantaccess&amp;grant=revoke&amp;user=".$user['id']."'>".utf8_decode($user['lname']).", ".utf8_decode($user['fname'])." (".utf8_decode($user['user']).")</A></DIV>\n";
	  }
	}
	$SQL = "SELECT u.id, u.fname, u.lname, u.\"user\" FROM web.users u WHERE u.id NOT IN (SELECT u.id FROM web.users u, movies.accessrights a WHERE a.\"user\" = u.id AND a.owner IN (SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."') AND a.permission = TRUE AND u.movies = TRUE) AND u.id NOT IN (SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."') AND u.movies = TRUE ORDER BY u.lname, u.fname, u.\"user\"";
	$users = pg_fetch_all(pg_query($db, $SQL));
	if ($users)
	{
	  echo "<DIV CLASS = 'rubrik3'>These users doesn't have the right to view your collection. To grant the right simply click on the person.</DIV>\n";
	  foreach ($users as $user)
	  {
	    echo "<DIV><A HREF = 'index.php?what=grantaccess&amp;grant=grant&amp;user=".$user['id']."'>".utf8_decode($user['lname']).", ".utf8_decode($user['fname'])." (".utf8_decode($user['user']).")</A></DIV>\n";
	  }
	}
  }
  elseif ($_GET['what'] == 'useaccess' AND $_COOKIE['username'] AND $_COOKIE['password'])
  {
    menu();
	if ($_GET['use'] == 'yes' AND isset($_GET['user']))
	{
	  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	  $user = pg_fetch_all(pg_query($db, $SQL));
	  $SQL = "UPDATE movies.accessrights SET \"view\" = TRUE WHERE \"user\" = ".$user[0]['id']." AND owner = ".$_GET['user'];
	  pg_query($db, $SQL);
	}
	if ($_GET['use'] == 'no' AND isset($_GET['user']))
	{
	  $SQL = "SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."'";
	  $user = pg_fetch_all(pg_query($db, $SQL));
	  $SQL = "UPDATE movies.accessrights SET \"view\" = FALSE WHERE \"user\" = ".$user[0]['id']." AND owner = ".$_GET['user'];
	  pg_query($db, $SQL);
	}
	$SQL = "SELECT u.id, u.fname, u.lname, u.\"user\" FROM web.users u, movies.accessrights a WHERE a.owner = u.id AND a.permission = TRUE AND \"view\" = TRUE AND a.\"user\" IN (SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."') ORDER BY u.lname, u.fname, u.\"user\"";
	$users = pg_fetch_all(pg_query($db, $SQL));
	if ($users)
	{
	  echo "<DIV CLASS = 'rubrik3'>You are viewing these users collections. Click on a user to remove him/her from the list.</DIV>\n";
	  foreach ($users as $user)
	  {
	    echo "<DIV><A HREF = 'index.php?what=useaccess&amp;use=no&amp;user=".$user['id']."'>".utf8_decode($user['lname']).", ".utf8_decode($user['fname'])." (".utf8_decode($user['user']).")</A></DIV>\n";
	  }
	}
	$SQL = "SELECT u.id, u.fname, u.lname, u.\"user\" FROM web.users u, movies.accessrights a WHERE a.owner = u.id AND a.permission = TRUE AND \"view\" = FALSE AND a.\"user\" IN (SELECT id FROM web.users WHERE \"user\" = '".utf8_encode($_COOKIE['username'])."' AND pwd = '".$_COOKIE['password']."') ORDER BY u.lname, u.fname, u.\"user\"";
	$users = pg_fetch_all(pg_query($db, $SQL));
	if ($users)
	{
	  echo "<DIV CLASS = 'rubrik3'>You have the right to view these user's lists. Click on a user to add him/her to the list.</DIV>\n";
	  foreach ($users as $user)
	  {
	    echo "<DIV><A HREF = 'index.php?what=useaccess&amp;use=yes&amp;user=".$user['id']."'>".utf8_decode($user['lname']).", ".utf8_decode($user['fname'])." (".utf8_decode($user['user']).")</A></DIV>\n";
	  }
	}
  }
  else
  {
    noPermission();
  }
}
else
{
  menu();
  if (!$_COOKIE['username'] OR !$_COOKIE['password'])
  {
    echo "<DIV CLASS = 'message'>Please go to <A HREF = 'http://www.jargoth.se/www/signup.php'>www.jargoth.se</A> to get a useraccount to this place.</DIV>\n";
  }
}
echo "</DIV>\n";
echo "</BODY>\n";
echo "</HTML>";
?>