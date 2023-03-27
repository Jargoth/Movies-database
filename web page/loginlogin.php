<?php
if ($_POST['username'] AND $_POST['password'])
{
  $SQL = "SELECT id, movies, pwd FROM web.users WHERE \"user\" ILIKE '".$_POST['username']."' AND pwd = md5('".$_POST['password']."')";
  $user = pg_fetch_all(pg_query($db, $SQL));
  $user = $user[0];
  if ($user['movies'] == 't')
  {
    setcookie('username', $_POST['username'], time()+60*60*24*365*5);
    setcookie('password', $user['pwd'], time()+60*60*24*365*5);
  }
}
?>