<?php
// ───────────────────────────────
// Admin GJPB — PHP + MySQL (PDO)
// ───────────────────────────────
require __DIR__.'/_auth.php';          // protège l'accès
require __DIR__.'/../config.php';      // $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS

// ----- PDO -----
function pdo(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;
  global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
  $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

// ----- Helpers JSON -----
function json($data, int $code = 200) {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
function read_json() {
  $raw = file_get_contents('php://input');
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}

// ===================================================================
// API (routes simples via ?api=...)
// ===================================================================
if (isset($_GET['api'])) {
  $api = $_GET['api'];
  try {
    // -------------------- JOUEURS --------------------
    if ($api === 'joueurs.list') {
      $stmt = pdo()->query("SELECT id, nom FROM joueurs ORDER BY nom ASC");
      json(['ok'=>true,'data'=>$stmt->fetchAll()]);
    }
    if ($api === 'joueurs.add') {
      $d = read_json();
      $nom = trim($d['nom'] ?? '');
      if ($nom==='') json(['ok'=>false,'msg'=>'Nom requis'], 400);
      $q = pdo()->prepare("INSERT INTO joueurs(nom) VALUES(:n)");
      $q->execute([':n'=>$nom]);
      json(['ok'=>true,'id'=>pdo()->lastInsertId()]);
    }
    if ($api === 'joueurs.del') {
      $d = read_json();
      $id = (int)($d['id'] ?? 0);
      if ($id<=0) json(['ok'=>false,'msg'=>'id invalide'], 400);
      // si des actions référencent un nom, on ne peut pas cascade. Ici on supprime juste le joueur
      $q = pdo()->prepare("DELETE FROM joueurs WHERE id=:id");
      $q->execute([':id'=>$id]);
      json(['ok'=>true]);
    }

    // -------------------- PROGRAMMATIONS --------------------
    if ($api === 'progs.list') {
      $stmt = pdo()->query("SELECT * FROM programmations ORDER BY date ASC, equipe ASC");
      json(['ok'=>true,'data'=>$stmt->fetchAll()]);
    }
    if ($api === 'progs.get') {
      $id = (int)($_GET['id'] ?? 0);
      $q = pdo()->prepare("SELECT * FROM programmations WHERE id=:id");
      $q->execute([':id'=>$id]);
      json(['ok'=>true,'data'=>$q->fetch()]);
    }
    if ($api === 'progs.save') {
      $d = read_json();
      $id  = (int)($d['id'] ?? 0);
      $row = [
        'date'=>$d['date'] ?? null,
        'equipe'=>$d['equipe'] ?? null,
        'adversaire'=>$d['adversaire'] ?? null,
        'lieu'=>$d['lieu'] ?? null,
        'competition'=>$d['competition'] ?? null,
        'heure'=>$d['heure'] ?? null,
      ];
      if (!$row['date'] || !$row['equipe'] || !$row['adversaire']) json(['ok'=>false,'msg'=>'date/equipe/adversaire requis'], 400);

      if ($id>0) {
        $q = pdo()->prepare("UPDATE programmations SET date=:date,equipe=:equipe,adversaire=:adv,lieu=:lieu,competition=:comp,heure=:h WHERE id=:id");
        $q->execute([':date'=>$row['date'],':equipe'=>$row['equipe'],':adv'=>$row['adversaire'],':lieu'=>$row['lieu'],':comp'=>$row['competition'],':h'=>$row['heure'],':id'=>$id]);
        json(['ok'=>true,'id'=>$id,'mode'=>'update']);
      } else {
        $q = pdo()->prepare("INSERT INTO programmations(date,equipe,adversaire,lieu,competition,heure) VALUES(:date,:equipe,:adv,:lieu,:comp,:h)");
        $q->execute([':date'=>$row['date'],':equipe'=>$row['equipe'],':adv'=>$row['adversaire'],':lieu'=>$row['lieu'],':comp'=>$row['competition'],':h'=>$row['heure']]);
        json(['ok'=>true,'id'=>pdo()->lastInsertId(),'mode'=>'insert']);
      }
    }
    if ($api === 'progs.del') {
      $d = read_json();
      $id = (int)($d['id'] ?? 0);
      if ($id<=0) json(['ok'=>false,'msg'=>'id invalide'], 400);
      $q = pdo()->prepare("DELETE FROM programmations WHERE id=:id");
      $q->execute([':id'=>$id]);
      json(['ok'=>true]);
    }
    if ($api === 'progs.adversairesFor') {
      $date   = $_GET['date']   ?? '';
      $equipe = $_GET['equipe'] ?? '';
      if (!$date || !$equipe) json(['ok'=>true,'data'=>[]]);
      $q = pdo()->prepare("SELECT DISTINCT adversaire FROM programmations WHERE date=:d AND equipe=:e ORDER BY adversaire");
      $q->execute([':d'=>$date, ':e'=>$equipe]);
      json(['ok'=>true,'data'=>array_column($q->fetchAll(),'adversaire')]);
    }

    // -------------------- MATCHS + ACTIONS --------------------
    // Liste avec agrégats buteurs/passeurs (GROUP_CONCAT)
    if ($api === 'matchs.list') {
      $sql = "
        SELECT m.*,
          COALESCE(GROUP_CONCAT(CASE WHEN a.type='Goal'   THEN a.nom END ORDER BY a.id SEPARATOR ', '),'')   AS buteurs,
          COALESCE(GROUP_CONCAT(CASE WHEN a.type='Assist' THEN a.nom END ORDER BY a.id SEPARATOR ', '),'')   AS passeurs
        FROM matchs m
        LEFT JOIN actions a ON a.match_id=m.id
        GROUP BY m.id
        ORDER BY m.date DESC, m.id DESC";
      $stmt = pdo()->query($sql);
      json(['ok'=>true,'data'=>$stmt->fetchAll()]);
    }

    // Détail d’un match + actions (pour éditer)
    if ($api === 'matchs.get') {
      $id = (int)($_GET['id'] ?? 0);
      $q = pdo()->prepare("SELECT * FROM matchs WHERE id=:id");
      $q->execute([':id'=>$id]);
      $m = $q->fetch();
      if (!$m) json(['ok'=>false,'msg'=>'introuvable'],404);
      $qa = pdo()->prepare("SELECT type, nom FROM actions WHERE match_id=:id ORDER BY id");
      $qa->execute([':id'=>$id]);
      $acts = $qa->fetchAll();
      json(['ok'=>true,'data'=>['match'=>$m,'actions'=>$acts]]);
    }

    // Save (insert/update) + actions
    if ($api === 'matchs.save') {
      $d = read_json();
      $id  = (int)($d['id'] ?? 0);
      $m = [
        'date'=>$d['date'] ?? null,
        'equipe'=>$d['equipe'] ?? null,
        'adversaire'=>$d['adversaire'] ?? null,
        'lieu'=>$d['lieu'] ?? null,
        'competition'=>$d['competition'] ?? null,
        'buts_gjpb'=>(int)($d['buts_gjpb'] ?? 0),
        'buts_adversaire'=>(int)($d['buts_adversaire'] ?? 0),
        'resultat'=>$d['resultat'] ?? null,
      ];
      if (!$m['date'] || !$m['equipe'] || !$m['adversaire']) json(['ok'=>false,'msg'=>'date/equipe/adversaire requis'],400);

      $pdo = pdo();
      $pdo->beginTransaction();
      try {
        if ($id>0) {
          $q = $pdo->prepare("UPDATE matchs SET date=:date,equipe=:equipe,adversaire=:adv,lieu=:lieu,competition=:comp,buts_gjpb=:bg,buts_adversaire=:ba,resultat=:res WHERE id=:id");
          $q->execute([':date'=>$m['date'],':equipe'=>$m['equipe'],':adv'=>$m['adversaire'],':lieu'=>$m['lieu'],':comp'=>$m['competition'],':bg'=>$m['buts_gjpb'],':ba'=>$m['buts_adversaire'],':res'=>$m['resultat'],':id'=>$id]);
          $pdo->prepare("DELETE FROM actions WHERE match_id=:id")->execute([':id'=>$id]);
        } else {
          $q = $pdo->prepare("INSERT INTO matchs(date,equipe,adversaire,lieu,competition,buts_gjpb,buts_adversaire,resultat) VALUES(:date,:equipe,:adv,:lieu,:comp,:bg,:ba,:res)");
          $q->execute([':date'=>$m['date'],':equipe'=>$m['equipe'],':adv'=>$m['adversaire'],':lieu'=>$m['lieu'],':comp'=>$m['competition'],':bg'=>$m['buts_gjpb'],':ba'=>$m['buts_adversaire'],':res'=>$m['resultat']]);
          $id = (int)$pdo->lastInsertId();
        }

        // Insertion des actions (noms bruts selon ton schéma)
        $buteurs   = is_array($d['buteurs'] ?? null)   ? $d['buteurs']   : [];
        $passeurs  = is_array($d['passeurs'] ?? null)  ? $d['passeurs']  : [];
        if ($buteurs) {
          $ins = $pdo->prepare("INSERT INTO actions(match_id, nom, type) VALUES(:mid,:nom,'Goal')");
          foreach ($buteurs as $nom) if (trim($nom)!=='') $ins->execute([':mid'=>$id,':nom'=>$nom]);
        }
        if ($passeurs) {
          $ins = $pdo->prepare("INSERT INTO actions(match_id, nom, type) VALUES(:mid,:nom,'Assist')");
          foreach ($passeurs as $nom) if (trim($nom)!=='') $ins->execute([':mid'=>$id,':nom'=>$nom]);
        }

        // On supprime la programmation correspondante (même date + equipe + adversaire) si elle existe
        $delProg = $pdo->prepare("DELETE FROM programmations WHERE date=:d AND equipe=:e AND adversaire=:a");
        $delProg->execute([':d'=>$m['date'], ':e'=>$m['equipe'], ':a'=>$m['adversaire']]);

        $pdo->commit();
        json(['ok'=>true,'id'=>$id]);
      } catch(Throwable $e) {
        $pdo->rollBack();
        json(['ok'=>false,'msg'=>$e->getMessage()],500);
      }
    }

    if ($api === 'matchs.del') {
      $d = read_json();
      $id = (int)($d['id'] ?? 0);
      if ($id<=0) json(['ok'=>false,'msg'=>'id invalide'],400);
      $pdo = pdo();
      $pdo->beginTransaction();
      $pdo->prepare("DELETE FROM actions WHERE match_id=:id")->execute([':id'=>$id]);
      $pdo->prepare("DELETE FROM matchs WHERE id=:id")->execute([':id'=>$id]);
      $pdo->commit();
      json(['ok'=>true]);
    }

    // 404 API inconnue
    json(['ok'=>false,'msg'=>'API inconnue'],404);

  } catch(Throwable $e) {
    json(['ok'=>false,'msg'=>$e->getMessage()],500);
  }
}

// ===================================================================
// HTML + JS (appelent les API ci-dessus)
// ===================================================================
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin — GJPB (MySQL)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* (styles principaux repris de ton ancien fichier, simplifiés ici) */
    body{font-family:system-ui,Segoe UI,Inter,Arial,sans-serif;background:#f5f7fb;margin:0;color:#223}
    header{background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);position:sticky;top:0;z-index:10}
    .header-content{display:flex;align-items:center;justify-content:space-between;height:64px;padding:0 16px;max-width:1100px;margin:0 auto}
    .container{max-width:1100px;margin:20px auto;padding:0 16px}
    .admin-header{background:#1e3a5f;color:#fff;padding:14px 16px;border-radius:10px}
    .card{background:#fff;border:1px solid #eef2f7;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.06);padding:14px;margin-top:16px}
    .row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .group{display:flex;flex-direction:column;gap:6px}
    input,select{padding:8px;border:1px solid #e5e7eb;border-radius:8px}
    .btn{border:0;border-radius:8px;padding:8px 12px;cursor:pointer}
    .btn-primary{background:#3b82f6;color:#fff}
    .btn-secondary{background:#111;color:#fff}
    .table-wrap{overflow:auto}
    table{width:100%;border-collapse:collapse;min-width:820px}
    th,td{border-bottom:1px solid #eee;padding:8px;text-align:left}
    .admin-tabs{display:flex;gap:8px;margin:10px 0}
    .tab-btn{border:1px solid #e5e7eb;background:#fff;border-radius:999px;padding:6px 10px;cursor:pointer}
    .tab-btn.active{background:#3b82f6;border-color:#3b82f6;color:#fff}
    .tab-section{display:none}
    .tab-section.active{display:block}
    #msg,#progMsg,#playerMsg{color:#0a7f3f;font-weight:600}
    @media(max-width:768px){.row{grid-template-columns:1fr}}
  </style>
</head>
<body>

<header>
  <div class="header-content">
    <a href="/index.php" style="text-decoration:none;color:#1e3a5f;font-weight:700">🏠 GJPB</a>
    <nav style="display:flex;gap:10px">
      <a class="btn" href="/pages/logout.php">Se déconnecter</a>
    </nav>
  </div>
</header>

<main class="container">
  <section class="admin-header"><h1>Admin — Matchs / Programmations / Joueurs</h1></section>

  <div class="admin-tabs" role="tablist">
    <button class="tab-btn active" data-tab="matchs" aria-selected="true">Matchs</button>
    <button class="tab-btn" data-tab="programmations" aria-selected="false">Programmations</button>
    <button class="tab-btn" data-tab="joueurs" aria-selected="false">Joueurs</button>
  </div>

  <!-- MATCHS -->
  <section id="tab-matchs" class="tab-section active">
    <form id="form" class="card" data-match-id="">
      <div class="row">
        <div class="group">
          <label for="date">Date</label>
          <input type="date" id="date">
        </div>
        <div class="group">
          <label for="equipe">Équipe</label>
          <select id="equipe">
            <option>18A</option><option>17</option><option>18B</option>
          </select>
        </div>
        <div class="group">
          <label for="adversaire">Adversaire (issu des programmations si dispo)</label>
          <select id="adversaire">
            <option value="">— choisir —</option>
          </select>
        </div>
        <div class="group">
          <label for="lieu">Lieu</label>
          <select id="lieu"><option>DOM</option><option>EXT</option></select>
        </div>
        <div class="group">
          <label for="competition">Compétition</label>
          <select id="competition">
            <option>Phase 1</option><option>Phase 2</option><option>Phase 3</option>
            <option>Amical</option><option>Coupe</option>
          </select>
        </div>
        <div class="group">
          <label for="resultat">Résultat</label>
          <select id="resultat"><option value="V">V</option><option value="N">N</option><option value="D">D</option></select>
        </div>
        <div class="group">
          <label>Score</label>
          <div style="display:flex;gap:6px;align-items:center">
            <input type="number" id="buts_gjpb" min="0" placeholder="GJPB" style="width:110px">
            <span>—</span>
            <input type="number" id="buts_adv" min="0" placeholder="ADV" style="width:110px">
          </div>
        </div>
      </div>

      <div class="card">
        <div class="group">
          <label><b>Nombre de buteurs</b></label>
          <input type="number" id="nbButeurs" min="0" max="14" value="0">
          <div id="buteurs-container"></div>
        </div>
        <div class="group" style="margin-top:12px">
          <label><b>Nombre de passeurs</b></label>
          <input type="number" id="nbPasseurs" min="0" max="14" value="0">
          <div id="passeurs-container"></div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
          <button type="button" id="saveBtn" class="btn btn-primary">Enregistrer</button>
          <button type="button" id="resetBtn" class="btn btn-secondary">Réinitialiser</button>
          <div id="msg"></div>
        </div>
      </div>
    </form>

    <div class="card">
      <h3>⚽ Matchs enregistrés</h3>
      <div class="table-wrap">
        <table id="matchsTable">
          <thead>
            <tr>
              <th>Actions</th>
              <th>Date</th><th>Équipe</th><th>Adversaire</th><th>Lieu</th>
              <th>Compétition</th><th>Score</th><th>Résultat</th><th>Buteurs</th><th>Passeurs</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- PROGRAMMATIONS -->
  <section id="tab-programmations" class="tab-section">
    <form id="progForm" class="card" data-prog-id="">
      <div class="row">
        <div class="group"><label>Date</label><input type="date" id="prog_date"></div>
        <div class="group"><label>Équipe</label>
          <select id="prog_equipe"><option>18A</option><option>17</option><option>18B</option></select>
        </div>
        <div class="group"><label>Adversaire</label><input type="text" id="prog_adversaire" placeholder="Nom adversaire"></div>
        <div class="group"><label>Lieu</label><select id="prog_lieu"><option>DOM</option><option>EXT</option></select></div>
        <div class="group"><label>Compétition</label>
          <select id="prog_competition"><option>Phase 1</option><option>Phase 2</option><option>Phase 3</option><option>Amical</option><option>Coupe</option></select>
        </div>
        <div class="group"><label>Heure</label><input type="time" id="prog_heure"></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
        <button type="button" id="progSaveBtn" class="btn btn-primary">Enregistrer</button>
        <button type="button" id="progResetBtn" class="btn btn-secondary">Réinitialiser</button>
        <div id="progMsg"></div>
      </div>
    </form>

    <div class="card">
      <h3>Programmations</h3>
      <div class="table-wrap">
        <table id="progsTable">
          <thead><tr>
            <th>Actions</th><th>Date</th><th>Équipe</th><th>Adversaire</th><th>Lieu</th><th>Compétition</th><th>Heure</th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- JOUEURS -->
  <section id="tab-joueurs" class="tab-section">
    <form id="playerForm" class="card">
      <div class="row">
        <div class="group" style="grid-column:1/-1">
          <label>Nom du joueur</label>
          <input type="text" id="playerName" placeholder="Ex: Jean Dupont">
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
        <button type="button" id="playerAddBtn" class="btn btn-primary">Ajouter</button>
        <div id="playerMsg"></div>
      </div>
    </form>

    <div class="card">
      <h3>Joueurs</h3>
      <div class="table-wrap">
        <table id="playersTable">
          <thead><tr><th>Actions</th><th>Nom</th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

</main>

<script>
const $ = s => document.querySelector(s);
const msg = (t, err=false) => { const el = $('#msg'); if(el){ el.textContent = t||''; el.style.color = err ? '#b00020' : '#0a7f3f'; } };

function api(path, data) {
  const opt = data ? {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)} : {};
  return fetch(`admin.php?api=${encodeURIComponent(path)}`, opt).then(r=>r.json());
}

// ------ Joueurs ------
async function chargerJoueursListe() {
  const tb = $('#playersTable tbody');
  const res = await api('joueurs.list');
  tb.innerHTML = '';
  (res.data||[]).forEach(j=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><button data-id="${j.id}" class="del-player">Del</button></td><td>${j.nom}</td>`;
    tb.appendChild(tr);
  });
}
async function enregistrerJoueur() {
  const name = ($('#playerName')?.value||'').trim();
  if(!name){ $('#playerMsg').textContent='Nom requis'; return; }
  const r = await api('joueurs.add', {nom:name});
  if(r.ok){ $('#playerMsg').textContent='Joueur ajouté'; $('#playerName').value=''; await chargerJoueursListe(); await chargerJoueurs(); }
  else $('#playerMsg').textContent='Erreur';
}
async function supprimerJoueur(id) {
  if(!confirm('Supprimer ce joueur ?')) return;
  await api('joueurs.del',{id});
  await chargerJoueursListe(); await chargerJoueurs();
}

// Remplir selects de buteurs/passeurs
let joueurs = [];
async function chargerJoueurs() {
  const r = await api('joueurs.list');
  joueurs = (r.data||[]).map(x=>x.nom);
}
function genererChamps(containerId, prefix, count) {
  const cont = document.getElementById(containerId);
  cont.innerHTML = '';
  const n = Number(count)||0;
  for (let i=0;i<n;i++) {
    const sel = document.createElement('select');
    sel.id = `${prefix}_${i}`;
    joueurs.forEach(nom=>{
      const opt = document.createElement('option');
      opt.value = nom; opt.textContent = nom; sel.appendChild(opt);
    });
    cont.appendChild(sel);
  }
}

// ------ Programmations ------
async function chargerProgrammations() {
  const tb = $('#progsTable tbody');
  const r = await api('progs.list');
  tb.innerHTML = '';
  (r.data||[]).forEach(p=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>
      <button class="edit-prog" data-id="${p.id}">Edit</button>
      <button class="del-prog" data-id="${p.id}">Del</button>
    </td>
    <td>${p.date||''}</td><td>${p.equipe||''}</td><td>${p.adversaire||''}</td>
    <td>${p.lieu||''}</td><td>${p.competition||''}</td><td>${p.heure||''}</td>`;
    tb.appendChild(tr);
  });
}
async function enregistrerProgrammation() {
  const d = {
    id: $('#progForm').dataset.progId || 0,
    date: $('#prog_date').value,
    equipe: $('#prog_equipe').value,
    adversaire: $('#prog_adversaire').value,
    lieu: $('#prog_lieu').value,
    competition: $('#prog_competition').value,
    heure: $('#prog_heure').value
  };
  const r = await api('progs.save', d);
  $('#progMsg').textContent = r.ok ? 'Programmation enregistrée' : 'Erreur';
  $('#progForm').dataset.progId = '';
  $('#progForm').reset();
  chargerProgrammations();
}
async function chargerProgrammationDansForm(id){
  const r = await api('progs.get&'+new URLSearchParams({id}));
  if(!r.ok) return;
  const p = r.data||{};
  const f = $('#progForm'); f.dataset.progId=p.id;
  $('#prog_date').value=p.date||''; $('#prog_equipe').value=p.equipe||'18A';
  $('#prog_adversaire').value=p.adversaire||''; $('#prog_lieu').value=p.lieu||'DOM';
  $('#prog_competition').value=p.competition||'Phase 1'; $('#prog_heure').value=p.heure||'';
}
async function supprimerProgrammation(id){
  if(!confirm('Supprimer cette programmation ?')) return;
  await api('progs.del',{id});
  chargerProgrammations();
}
async function chargerAdversairesDepuisProgrammations() {
  const date = $('#date').value, equipe = $('#equipe').value;
  const sel = $('#adversaire'); sel.innerHTML = '<option value="">— choisir —</option>';
  if(!date || !equipe) return;
  const r = await fetch('admin.php?api=progs.adversairesFor&'+new URLSearchParams({date,equipe}));
  const js = await r.json();
  (js.data||[]).forEach(n=>{
    const o=document.createElement('option'); o.value=o.textContent=n; sel.appendChild(o);
  });
}

// ------ Matchs ------
async function chargerMatchs() {
  const tb = $('#matchsTable tbody');
  const r = await api('matchs.list');
  tb.innerHTML = '';
  (r.data||[]).forEach(m=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>
      <button class="edit" data-id="${m.id}">✏️</button>
      <button class="del" data-id="${m.id}">🗑️</button>
    </td>
    <td>${m.date||''}</td><td>${m.equipe||''}</td><td>${m.adversaire||''}</td>
    <td>${m.lieu||''}</td><td>${m.competition||''}</td>
    <td>${Number(m.buts_gjpb)||0} - ${Number(m.buts_adversaire)||0}</td>
    <td>${m.resultat||''}</td><td>${m.buteurs||''}</td><td>${m.passeurs||''}</td>`;
    tb.appendChild(tr);
  });
}
async function chargerMatchDansForm(id){
  const r = await fetch('admin.php?api=matchs.get&'+new URLSearchParams({id}));
  const js = await r.json();
  if(!js.ok) return;
  const m = js.data.match, acts = js.data.actions||[];
  $('#form').dataset.matchId = m.id;
  $('#date').value=m.date||''; $('#equipe').value=m.equipe||'18A';
  $('#adversaire').innerHTML = `<option>${m.adversaire||''}</option>`;
  $('#lieu').value=m.lieu||'DOM'; $('#competition').value=m.competition||'Phase 1';
  $('#buts_gjpb').value=Number(m.buts_gjpb)||0; $('#buts_adv').value=Number(m.buts_adversaire)||0;
  $('#resultat').value=m.resultat||'V';

  const buts = acts.filter(a=>a.type==='Goal').map(a=>a.nom);
  const pas  = acts.filter(a=>a.type==='Assist').map(a=>a.nom);
  $('#nbButeurs').value=buts.length; $('#nbPasseurs').value=pas.length;
  genererChamps('buteurs-container','buteur',buts.length);
  genererChamps('passeurs-container','passeur',pas.length);
  buts.forEach((n,i)=>{ const s=$(`#buteur_${i}`); if(s) s.value=n; });
  pas.forEach((n,i)=>{ const s=$(`#passeur_${i}`); if(s) s.value=n; });
  msg('Mode édition activé');
}
async function enregistrerMatch() {
  const d = {
    id: $('#form').dataset.matchId || 0,
    date: $('#date').value,
    equipe: $('#equipe').value,
    adversaire: $('#adversaire').value,
    lieu: $('#lieu').value,
    competition: $('#competition').value,
    buts_gjpb: Number($('#buts_gjpb').value||0),
    buts_adversaire: Number($('#buts_adv').value||0),
    resultat: $('#resultat').value,
    buteurs: Array.from(document.querySelectorAll('#buteurs-container select')).map(s=>s.value),
    passeurs: Array.from(document.querySelectorAll('#passeurs-container select')).map(s=>s.value),
  };
  const r = await api('matchs.save', d);
  if(r.ok){ msg('Enregistré ✅'); $('#form').dataset.matchId=''; $('#form').reset();
    $('#buteurs-container').innerHTML=''; $('#passeurs-container').innerHTML='';
    await chargerMatchs(); await chargerProgrammations();
  } else msg('Erreur', true);
}
async function supprimerMatch(id){
  if(!confirm('Supprimer ce match ?')) return;
  await api('matchs.del',{id}); await chargerMatchs();
}

// ------ Tabs + Events ------
function switchTab(name){
  document.querySelectorAll('.tab-btn').forEach(b=>{
    const on=b.dataset.tab===name; b.classList.toggle('active',on); b.setAttribute('aria-selected',String(on));
  });
  document.querySelectorAll('.tab-section').forEach(s=>{
    s.classList.toggle('active', s.id===`tab-${name}`);
  });
}

document.addEventListener('DOMContentLoaded', async ()=>{
  // dyn champs
  $('#nbButeurs').addEventListener('input', e=>genererChamps('buteurs-container','buteur', e.target.value));
  $('#nbPasseurs').addEventListener('input', e=>genererChamps('passeurs-container','passeur', e.target.value));

  // boutons matchs
  $('#saveBtn').addEventListener('click', enregistrerMatch);
  $('#resetBtn').addEventListener('click', ()=>{ $('#form').reset(); $('#form').dataset.matchId=''; $('#buteurs-container').innerHTML=''; $('#passeurs-container').innerHTML=''; msg(''); });

  // adversaires dyn
  $('#date').addEventListener('change', chargerAdversairesDepuisProgrammations);
  $('#equipe').addEventListener('change', chargerAdversairesDepuisProgrammations);

  // table matchs deleg
  $('#matchsTable').addEventListener('click', ev=>{
    const b = ev.target.closest('button'); if(!b) return;
    const id = b.dataset.id;
    if(b.classList.contains('edit')) chargerMatchDansForm(id);
    if(b.classList.contains('del'))  supprimerMatch(id);
  });

  // tabs
  document.querySelectorAll('.tab-btn').forEach(b=>b.addEventListener('click', ()=>switchTab(b.dataset.tab)));

  // programmations
  $('#progSaveBtn').addEventListener('click', enregistrerProgrammation);
  $('#progResetBtn').addEventListener('click', ()=>{ $('#progForm').reset(); $('#progForm').dataset.progId=''; $('#progMsg').textContent=''; });
  $('#progsTable').addEventListener('click', ev=>{
    const b = ev.target.closest('button'); if(!b) return;
    const id=b.dataset.id;
    if(b.classList.contains('edit-prog')) chargerProgrammationDansForm(id);
    if(b.classList.contains('del-prog'))  supprimerProgrammation(id);
  });

  // joueurs
  $('#playerAddBtn').addEventListener('click', enregistrerJoueur);
  $('#playersTable').addEventListener('click', ev=>{
    const b = ev.target.closest('button'); if(!b) return;
    if(b.classList.contains('del-player')) supprimerJoueur(Number(b.dataset.id)||0);
  });

  // initial loads
  await chargerJoueurs();            // pour listes buteurs/passeurs
  await chargerMatchs();
  await chargerProgrammations();
  await chargerJoueursListe();
  await chargerAdversairesDepuisProgrammations();
});
</script>
</body>
</html>

