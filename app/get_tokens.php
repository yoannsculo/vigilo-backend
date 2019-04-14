<?php

require_once('./common.php');
require_once('./functions.php');

/* Token is mandatory */
if (!isset($_GET['token'])) {
  error_log('GET_TOKENS : Token not found');
  http_response_code(500);
  return;
}

$search_token = mysqli_real_escape_string($db, $_GET['token']);

if (!isset($_GET['radius'])) {
  $radius = 50;
} else {
  $radius = (int)mysqli_real_escape_string($db, $_GET['radius']);
}

$query = mysqli_query($db,"SELECT * FROM obs_list WHERE obs_token='$search_token' LIMIT 1");
$result = mysqli_fetch_array($query);
$token_gps_lat = $result['obs_coordinates_lat'];
$token_gps_lon = $result['obs_coordinates_lon'];

echo '<html lang="'.$vigilo_lang.'">';

?>

<head>
  <?php echo '<title>'.$vigilo_name.'</title>'; ?>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <link rel="icon" type="image/png" href="/style/favicon.png">
</head>
<body>
<?php
echo '<h4>Observation à moins de '.$radius.' mètres de '.$search_token.' Par ordre chronologique</h4>';
echo '<table>';
$query = mysqli_query($db, "SELECT * FROM obs_list ORDER by obs_time ASC");
while ($result = mysqli_fetch_array($query)) {
  // print_r($result);
  $token = $result['obs_token'];
  $coordinates_lat = $result['obs_coordinates_lat'];
  $coordinates_lon = $result['obs_coordinates_lon'];
 
  $distance = (int)distance($token_gps_lat, $token_gps_lon, $coordinates_lat, $coordinates_lon, $unit = 'm');
  if ($distance > $radius) {
    continue;
  }

  echo '<tr>';
  echo '<td>'.$distance .'m<br ./><br />';
  if ($token == $search_token) {
    echo '<font color="red"><strong>'.$token.'</strong></font></td>';
  } else { 
    echo '<strong>'.$token.'</strong></td>';
  }
  echo '<td><a target="_blank" href="http://'.$urlbase.'/generate_panel.php?token='.$token.'">';
  echo '    <img src="http://'.$urlbase.'/generate_panel.php?token='.$token.'&s=200" />';
  echo '  </a>';
  echo '</td>';
  echo '</tr>';
}
echo '</table>';

?>
</body>

</html>
