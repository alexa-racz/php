<?php

function redirect($page) {
    header("Location: ${page}");
    exit();
}  

function getTeamName($id) {
  $teamStorage = new TeamStorage();
  $t = $teamStorage->findById($id);
  return $t['name'];
}
?>