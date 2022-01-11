<?php
include_once('helper.php');
include_once('teamStorage.php');

session_start();
$playedMatches = $_SESSION['playedMatches'];

$resp = '{';

//$last_matches = array_slice($playedMatches, -5);
foreach($playedMatches as $match) {
    $resp .= '"' . $match['id'] . '": {'
        . '"dated": "' . $match['date'] . '",'
        . '"home": {'
            . '"name": "' . getTeamName($match['home']['id']) . '",'
            . '"score": ' . $match['home']['score']
        . '},'
        . '"away": {'
            . '"name": "' . getTeamName($match['away']['id']) . '",'
            . '"score": "' . $match['away']['score'] . '"'
        . '}'
    . '},';
}

$resp = substr_replace($resp ,'', -1);
$resp .= '}';
json_encode($resp);
print_r($resp);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>