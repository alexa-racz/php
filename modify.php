<?php

include_once('teamStorage.php');
include_once('matchStorage.php');
include_once('userStorage.php');
include_once('commentStorage.php');
include_once('auth.php');
include_once('helper.php');

$matchid = $_GET['matchid'];

$matchStorage = new MatchStorage();
$match = $matchStorage->findById($matchid);

$data = $match;

function validate($post, &$data, &$errors) {
    if (!isset($post['date'])) {
      $errors['date'] = 'A dátum kitöltése kötelező!';
    }
    else if (!filter_var($post['date'], FILTER_VALIDATE_REGEXP,
        ['options' => ['regexp' => '/\\d\\d\\d\\d-\\d\\d-\\d\\d/i']])) {
            $errors['date'] = 'A dátum formátuma nem megfelelő!';
        }
    $data['date'] = $post['date'];

    if (!isset($post['result'])) {
      $errors['result'] = 'Az eredmény kitöltése kötelező!';
    }
    else if (!filter_var(
        $post['result'],
        FILTER_VALIDATE_REGEXP,
        ['options' => ['regexp' => '/[0-9]+-[0-9]+/i']]
        )) {
            $errors['result'] = 'Az eredmény formátuma nem megfelelő!';
        }
    else {
        $s = explode('-', $post['result']);
        $data['home']['score'] = $s[0];
        $data['away']['score'] = $s[1];
    }
    return count($errors) === 0;
}

if(count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
      $matchStorage->update($matchid, $data);
      $previous = "reszletek.php?team=" . $_COOKIE['teamid'];
      redirect($previous);
    }
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Módosítás</title>
</head>
<body>
<h1><?= getTeamName($match['home']['id']) ?> - <?= getTeamName($match['away']['id']) ?></h1>
<form action="" method="post" novalidate>
  <div>
    <label for="date">Dátum: </label><br>
    <input type="text" name="date" id="date" value="<?= $_POST['date'] ?? "" ?>">
    <?php if (isset($errors['date'])) : ?>
      <span class="error"><?= $errors['date'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="result">Eredmény: </label><br>
    <input type="text" name="result" id="result" value="<?= $_POST['result'] ?? "" ?>">
    <?php if (isset($errors['result'])) : ?>
      <span class="error"><?= $errors['result'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <button type="submit">Mentés</button>
  </div>
</form>
</body>
</html>