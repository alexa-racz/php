<?php

include_once('teamStorage.php');
include_once('matchStorage.php');
include_once('userStorage.php');
include_once('commentStorage.php');
include_once('auth.php');
include_once('helper.php');

if (isset($_GET['matchid'])) {
    $matchid = $_GET['matchid'];
    $matchStorage = new MatchStorage();
    $matchStorage->delete($matchid);
}
elseif (isset($_GET['commentid'])) {
    $commentid = $_GET['commentid'];
    $commentStorage = new CommentStorage();
    $commentStorage->delete($commentid);
}

$previous = "reszletek.php?team=" . $_COOKIE['teamid'];
redirect($previous);

?>