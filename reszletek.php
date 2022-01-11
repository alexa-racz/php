<?php

include_once('teamStorage.php');
include_once('matchStorage.php');
include_once('userStorage.php');
include_once('commentStorage.php');
include_once('auth.php');
include_once('helper.php');

$teamid = $_GET['team'];
setcookie('teamid', $teamid);

//Beolvasás
$teamStorage = new TeamStorage();
$team = $teamStorage->findById($teamid);

$userStorage = new UserStorage();
$user = $userStorage->findAll();

$matchStorage = new MatchStorage();
$matches = $matchStorage->findMany(function ($match) use ($teamid) {
    return $match['home']['id'] === $teamid || $match['away']['id'] === $teamid;
});
array_multisort(array_column($matches, 'date'), SORT_ASC, $matches);

$commentStorage = new CommentStorage();
$comments = $commentStorage->findMany(function ($comments) use ($teamid) {
    return $comments['teamid'] == $teamid;
});

function matchResult($home, $away) {
    if ($home === $away) return "yellow";
    elseif($home < $away) return "red";
    else return "green";
}

function getUserName($id, $userStorage) {
    $u = $userStorage->findById($id);
    return $u['username'];
}

session_start();
$auth = new Auth(new UserStorage());

function validate($post, &$data, &$errors) {
    global $auth;
    if (!isset($post['comment']) || $post['comment'] === '') {
        $errors['comment'] = "Kérlek töltsd ki a mezőt!";
    }
    else if (!$auth->is_authenticated()) {
        $errors['comment'] = "Kérlek regisztrálj vagy jelentkezz be!";
    }
    $data['text'] = $post['comment'];
    return count($errors) === 0;
}

$data = [];
$errors = [];

if (count($_POST) > 0) { 

    if(validate($_POST, $data, $errors)) {

    $authenticated_user = $auth->authenticated_user();
    $data['author'] = $authenticated_user['id'];
    $data['teamid'] = $teamid;
    $data['date'] = date("Y-m-d");

    $commentStorage->add($data);

    header('Location: index.php'); exit();
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
    <title>Csapatrészletek | <?= $team['name'] ?></title>
</head>
<body>
    <h1><?= $team['name'] ?></h1>

    <h2>Meccsek:</h2>
    <ul>
        <?php foreach($matches as $match) : ?>
            <?php if($match['home']['score'] === '') : ?>
                <li><?= getTeamName($match['home']['id'], $teamStorage) ?> - <?= getTeamName($match['away']['id'], $teamStorage) ?> : A meccset <?= $match['date'] ?>-án játsszák.
            <?php else : ?>
                <li>
                    <span><?= $match['date'] ?></span>
                    <span style="color:<?= matchResult($match['home']['score'], $match['away']['score']) ?>;"><?= getTeamName($match['home']['id'], $teamStorage) ?></span> - <span style="color:<?= matchResult($match['away']['score'], $match['home']['score']) ?>;"><?= getTeamName($match['away']['id'], $teamStorage) ?></span> : <?= $match['home']['score'] ?> - <?= $match['away']['score'] ?>
            <?php endif ?>
            <?php if($auth->authorize(["admin"])) : ?>
                <a href="modify.php?matchid=<?= $match['id'] ?>">Módosítás</a>
                <a href="delete.php?matchid=<?= $match['id'] ?>">Törlés</a>
            <?php endif ?>
            </li>
        <?php endforeach ?>
        </ul>

    <h2>Hozzászólások:</h2>
    <ul>
        <?php foreach($comments as $comment) : ?>
            <li><?= getUserName($comment['author'], $userStorage) ?> : <?= $comment['text'] ?> (<?= $comment['date'] ?>)
            <?php if($auth->authorize(["admin"])) : ?>
                <a href="delete.php?commentid=<?= $comment['id'] ?>">Törlés</a>
            <?php endif ?>
            </li>
        <?php endforeach ?>
    </ul>

    <form action="" method="post" novalidate>
        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
        <button type="submit">Elküld</button> <br>
        <?php if(count($_POST) > 0 && $errors['comment'] !== '') : ?>
            <span><?= $errors['comment'] ?> <br>
            <button><a href="login.php">Bejelentkezés</button><button><a href="registration.php">Regisztráció</button></span>
        <?php endif ?>
    </form>
</body>
</html>