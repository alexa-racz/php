<?php
include_once('teamStorage.php');
include_once('matchStorage.php');
include_once('userStorage.php');
include_once('auth.php');

//Beolvasás
$teamStorage = new TeamStorage();
$teams = $teamStorage->findAll();

$matchStorage = new MatchStorage();
$matches = $matchStorage->findAll();
array_multisort(array_column($matches, 'date'), SORT_ASC, $matches);

$s = "";
$playedMatches = $matchStorage->findMany(function ($matches) use ($s) {
    return $matches['home']['score'] != $s;
});
array_multisort(array_column($playedMatches, 'date'), SORT_ASC, $playedMatches);

session_start();
$auth = new Auth(new UserStorage());

//$count = $_GET['count'] ?? 5;
$_SESSION['playedMatches'] = array_slice($playedMatches, count($playedMatches)-5);

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Eötvös Loránd Stadion | Főoldal</title>
</head>
<body>
    <header>
        <?php if(!$auth->is_authenticated()) : ?>
            <button><a href="login.php">Bejelentkezés</a></button>
            <button><a href="registration.php">Regisztráció</a></button>
        <?php else : ?>
            <button><a href="logout.php">Kijelentkezés</a></button>
        <?php endif ?>
    </header>
    <main>
        <h1>Eötvös Loránd Stadion</h1>
        <h2>Üdvözlünk az Eötvös Loránd Stadion honlapján!</h2>
        <p>Nálunk könnyedén nyomon követheted kedvenc csapataid eredményeit és következő meccseit.
        </p>

        <h2>Csapatok</h2>
        <ul>
            <?php foreach($teams as $team) : ?>
                <li><?= $team['name'] ?> <a href="reszletek.php?team=<?= $team['id'] ?>">Csapatrészletek<a></li>
            <?php endforeach ?>
        </ul>

        <h2>Legutóbbi meccsek</h2>
        <ol id="matchlist" value="<?= $count ?>">
            <?php 
            $last_matches = array_slice($playedMatches, -5);
            foreach($last_matches as $match) : ?>
                    <li><?= $match['date'] ?> <?= $match['home']['id'] ?> - <?= $match['away']['id'] ?> : <?= $match['home']['score'] ?> - <?= $match['away']['score'] ?></li>
            <?php endforeach ?>
            <button id="more">További meccsek...</button>
        </ol>
    </main>
    <footer>
        <hr>
        Készítette: Rácz Alexandra - AT6XB4
    </footer>
    <script src="ajax.js"></script>
</body>
</html>