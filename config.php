<?php
  $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

  define('HOST', $url['host']);
  define('DBUSERNAME', $url['user']);
  define('DBPASSWORD', $url['pass']);
  define('DBNAME', substr($url['path'], 1));
?>
