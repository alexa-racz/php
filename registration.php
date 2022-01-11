<?php

include('auth.php');
include('userStorage.php');
include('helper.php');

function validate($post, &$data, &$errors) {
  if(!isset($post['username'])) {
    $errors['username'] = "A felhasználónév megadása kötelező!";
  }
  if(!isset($post['email'])) {
    $errors['email'] = "Az e-mail megadása kötelező!";
  }
  else if(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false) {
    $errors['email'] = "Az e-mail formátuma nem megfelelő!";
  }
  if(!isset($post['password'])) {
    $errors['password'] = "Jelszó megadása kötelező!";
  }
  else if($post['password'] !== $post['passwordAgain']) {
    $errors['passwordAgain'] = "A jelszavak nem egyeznek!";
  }
  $data = $post;

  return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['username'] = "Ez a felhasználónév már foglalt!";
    }
    else if ($auth->user_exists($data['email'])) {
      $errors['email'] = "Ezzel az e-mail címmel már regisztráltak!";
    } else {
      $auth->register($data);
      redirect('login.php');
    } 
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
    <title>Regisztráció</title>
</head>
<body>
  <h1>Regisztráció</h1>

  <form action="" method="post" novalidate>
    <div>
      <label for="username">Felhasználónév: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>" required>
      <?php if (isset($errors['username'])) : ?>
        <span class="error"><?= $errors['username'] ?></span>
      <?php endif; ?>
    </div>
    <div>
    <label for="email">E-mail: </label><br>
      <input type="email" name="email" value="<?= $_POST['email'] ?? "" ?>" required> <br>
      <?php if (isset($errors['email'])) : ?>
        <span class="error"><?= $errors['email'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="password">Jelszó: </label><br>
      <input type="password" name="password" id="password" required>
      <?php if (isset($errors['password'])) : ?>
        <span class="error"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="passwordAgain">Jelszó ismét: </label><br>
      <input type="password" name="passwordAgain" id="passwordAgain" required>
      <?php if (isset($errors['passwordAgain'])) : ?>
        <span class="error"><?= $errors['passwordAgain'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <button type="submit">Regisztráció</button>
    </div>
  </form>

</body>
</html>