<?php
// Ouvre https://tonsite/pages/hash.php?p=MON_MOT_DE_PASSE
$pwd = $_GET['p'] ?? '';
if ($pwd==='') { die('Passe ?p=ton_mot_de_passe dans l’URL'); }
echo password_hash($pwd, PASSWORD_DEFAULT);
