 

  <?php /* page d’accueil */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Accueil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<header class="topbar">
  <nav class="nav">
    <a class="brand" href="/">🏠 Accueil</a>

    <!-- Dropdown accessible avec <details> -->
    <details class="dropdown">
      <summary>Équipes ▾</summary>
      <div class="menu">
        <a href="/pages/18A.html">18A</a>
        <a href="/pages/17.html">17</a>
        <a href="/pages/18B.html">18B</a>
      </div>
    </details>

    <a class="right" href="/pages/login.php">Connexion</a>
  </nav>
</header>

<main class="wrap">
  <h1>Menu principal</h1>
  <p class="muted">Choisis une section :</p>

  <section class="cards">
    <a class="card" href="/pages/resultats.html">
      <h3>Résultats</h3>
      <p>Scores & feuilles de match.</p>
    </a>

    <a class="card" href="/pages/calendrier.html">
      <h3>Calendrier</h3>
      <p>Prochains matchs & événements.</p>
    </a>

    <a class="card" href="/pages/stats.html">
      <h3>Stats</h3>
      <p>Classements, stats individuelles, etc.</p>
    </a>
  </section>
</main>

<footer class="foot">© Ton site — navigation simple & responsive</footer>
</body>
</html>

