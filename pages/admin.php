<?php require __DIR__.'/_auth.php'; ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header class="header">
    <a href="/">🏠 Accueil</a> • <a href="/pages/logout.php">Se déconnecter</a>
  </header>

  <main class="wrap">
    <h1>Espace admin</h1>
    <p>Bienvenue 👋 Vous êtes connecté.</p>

    <!-- Ici, tu ajouteras tes outils (modifs DB, etc.) -->
    <ul>
      <li><a href="/books/">Gestion des livres (exemple)</a></li>
    </ul>
  </main>
</body>
</html>
