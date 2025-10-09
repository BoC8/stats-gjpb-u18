// <?php
// /*******************************
//  *  Minimal Books App (PHP+MySQL)
//  *  - Formulaire d’ajout (titre, auteur, année)
//  *  - Liste en dessous
//  *  - PDO + requêtes préparées
//  *******************************/

// // ⚠️ utilise les identifiants injectés par le workflow (config.php généré depuis tes secrets GitHub)
// require __DIR__ . '/config.php';

// // Connexion PDO
// try {
//   $pdo = new PDO(
//     "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
//     $DB_USER,
//     $DB_PASS,
//     [
//       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     ]
//   );
// } catch (Throwable $e) {
//   http_response_code(500);
//   die("Erreur de connexion MySQL : " . htmlspecialchars($e->getMessage()));
// }

// // Création auto de la table si absente
// $pdo->exec("
//   CREATE TABLE IF NOT EXISTS books (
//     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     title VARCHAR(255) NOT NULL,
//     author VARCHAR(255) NOT NULL,
//     year SMALLINT UNSIGNED,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
// ");

// // Helpers
// function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// // Traitement POST (ajout)
// $errors = [];
// $okMsg  = null;

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   $title  = trim($_POST['title']  ?? '');
//   $author = trim($_POST['author'] ?? '');
//   $year   = trim($_POST['year']   ?? '');

//   if ($title === '')  $errors[] = "Le nom (titre) est obligatoire.";
//   if ($author === '') $errors[] = "L’auteur est obligatoire.";
//   if ($year !== '') {
//     if (!ctype_digit($year)) $errors[] = "L’année doit être un entier.";
//     elseif ((int)$year < 0 || (int)$year > (int)date('Y')+1) $errors[] = "Année invalide.";
//   } else {
//     $year = null;
//   }

//   if (!$errors) {
//     $stmt = $pdo->prepare("INSERT INTO books (title, author, year) VALUES (:t, :a, :y)");
//     $stmt->execute([
//       ':t' => $title,
//       ':a' => $author,
//       ':y' => $year === null ? null : (int)$year,
//     ]);
//     // PRG
//     header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?added=1");
//     exit;
//   }
// }

// if (isset($_GET['added'])) {
//   $okMsg = "Livre ajouté avec succès.";
// }

// // Récupération liste (avec recherche)
// $search = trim($_GET['q'] ?? '');
// if ($search !== '') {
//   $stmt = $pdo->prepare("SELECT * FROM books
//                          WHERE title LIKE :q OR author LIKE :q OR CAST(year AS CHAR) LIKE :q
//                          ORDER BY created_at DESC, id DESC");
//   $stmt->execute([':q' => "%$search%"]);
// } else {
//   $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC, id DESC");
// }
// $books = $stmt->fetchAll();
// ?>
// <!doctype html>
// <html lang="fr">
// <head>
//   <meta charset="utf-8">
//   <title>Ma Bibliothèque</title>
//   <meta name="viewport" content="width=device-width, initial-scale=1">
//   <style>
//     :root { --bg:#0f172a; --card:#111827; --muted:#9ca3af; --text:#e5e7eb; --acc:#22d3ee; }
//     * { box-sizing:border-box; }
//     body { margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; background:linear-gradient(180deg,#0b1020,#0f172a); color:var(--text); }
//     .wrap { max-width:900px; margin:40px auto; padding:0 16px; }
//     .card { background:linear-gradient(180deg,#0b1020,#0f172a); border:1px solid #1f2937; border-radius:16px; padding:18px; box-shadow:0 10px 30px rgba(0,0,0,.3); }
//     h1 { font-size:28px; margin:0 0 14px; }
//     p.muted { color:var(--muted); margin-top:4px; }
//     form.grid { display:grid; grid-template-columns:1fr 1fr 140px 120px; gap:10px; }
//     input, button { width:100%; padding:12px 10px; border-radius:12px; border:1px solid #334155; background:#0b1220; color:var(--text); }
//     input::placeholder { color:#64748b; }
//     button { cursor:pointer; border-color:#0ea5b7; background:linear-gradient(180deg,#0ea5b7,#0891b2); font-weight:600; }
//     button:hover { filter:brightness(1.05); }
//     .msg { margin:12px 0; padding:10px 12px; border-radius:12px; }
//     .ok  { background:#052e2b; border:1px solid #0f766e; }
//     .err { background:#3b0a0a; border:1px solid #7f1d1d; }
//     .list { margin-top:20px; }
//     .row { display:grid; grid-template-columns:60px 1fr 1fr 120px 160px; gap:12px; padding:12px; border-bottom:1px solid #1f2937; align-items:center; }
//     .head { color:#93c5fd; font-weight:600; background:#0b1220; position:sticky; top:0; }
//     .badge { padding:4px 8px; border-radius:999px; background:#0b1220; border:1px solid #334155; color:#e5e7eb; text-align:center; }
//     .search { display:flex; gap:10px; margin-top:12px; }
//     .foot { margin-top:30px; color:#94a3b8; font-size:13px; text-align:center; }
//     @media (max-width:720px){
//       form.grid { grid-template-columns:1fr; }
//       .row { grid-template-columns:1fr; gap:6px; }
//       .head { display:none; }
//     }
//   </style>
// </head>
// <body>
//   <div class="wrap">
//     <div class="card">
//       <h1>📚 Ma Bibliothèque</h1>
//       <p class="muted">Ajoute un livre et vois la base afficher en dessous. (PHP + MySQL)</p>

//       <?php if ($okMsg): ?>
//         <div class="msg ok"><?= h($okMsg) ?></div>
//       <?php endif; ?>

//       <?php if ($errors): ?>
//         <div class="msg err">
//           <?php foreach ($errors as $e): ?>• <?= h($e) ?><br><?php endforeach; ?>
//         </div>
//       <?php endif; ?>

//       <form class="grid" method="post">
//         <input type="text"   name="title"  placeholder="Titre du livre" required>
//         <input type="text"   name="author" placeholder="Auteur" required>
//         <input type="text"   name="year"   placeholder="Année (ex: 2020)">
//         <button type="submit">➕ Ajouter</button>
//       </form>

//       <form class="search" method="get">
//         <input type="text" name="q" value="<?= h($search) ?>" placeholder="Rechercher titre, auteur, année...">
//         <button type="submit">🔎 Rechercher</button>
//         <a href="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>" style="align-self:center;text-decoration:none;">
//           <span class="badge">Réinitialiser</span>
//         </a>
//       </form>

//       <div class="list">
//         <div class="row head">
//           <div>#</div><div>Titre</div><div>Auteur</div><div>Année</div><div>Ajouté le</div>
//         </div>
//         <?php if (!$books): ?>
//           <div class="row"><div class="badge" style="grid-column:1/-1;text-align:center;">Aucun livre pour le mome


  

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

