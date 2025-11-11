<?php
session_start();
require __DIR__.'/config_admin.php';

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = (string)($_POST['password'] ?? '');

  if (strcasecmp($email, $ADMIN_EMAIL) === 0 && password_verify($pass, $ADMIN_HASH)) {
    session_regenerate_id(true);
    $_SESSION['admin_ok'] = true;
    $dest = $_POST['from'] ?? '/pages/admin.php';
    header("Location: " . $dest);
    exit;
  } else {
    $err = "Identifiants incorrects.";
  }
}

$from = $_GET['from'] ?? '/pages/admin.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Connexion admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header class="header">
    <a href="/">🏠 Accueil</a>
  </header>

  <main style="text-align:center;margin-top:60px">
    <h1>Connexion</h1>
    <?php if ($err): ?><p style="color:#b91c1c"><?= htmlspecialchars($err) ?></p><?php endif; ?>
    <form method="post" style="display:flex;flex-direction:column;gap:10px;max-width:320px;margin:auto;">
      <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
      <input type="email"    name="email"    placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
    </form>
  </main>
</body>
</html>
