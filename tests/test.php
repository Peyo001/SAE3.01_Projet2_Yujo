<?php
// Runner de tests pour les classes Model et DAO (version console)
// Exécution : php tests/test.php

require_once __DIR__ . '/../include.php';

$pdo = Database::getInstance()->getConnection();

$tests = [];
$pass = 0;
$fail = 0;

function recordTest(string $name, bool $ok, string $message = ''): void {
    global $tests, $pass, $fail;
    $tests[] = [$name, $ok, $message];
    $ok ? $pass++ : $fail++;
}

function formatValue(mixed $value, int $depth = 0): string {
    if ($depth > 1) {
        return '...';
    }

    if (is_array($value)) {
        $parts = [];
        $count = 0;
        foreach ($value as $k => $v) {
            if ($count >= 3) {
                $parts[] = '...';
                break;
            }
            $parts[] = $k . '=>' . formatValue($v, $depth + 1);
            $count++;
        }
        return '[' . implode(', ', $parts) . ']';
    }

    if (is_object($value)) {
        $props = get_object_vars($value);
        if (empty($props)) {
            return get_class($value);
        }

        $parts = [];
        $count = 0;
        foreach ($props as $k => $v) {
            if ($count >= 3) {
                $parts[] = '...';
                break;
            }
            $parts[] = $k . '=' . formatValue($v, $depth + 1);
            $count++;
        }
        return get_class($value) . '{' . implode(', ', $parts) . '}';
    }

    return (string) $value;
}

function dumpSample(string $label, mixed $value): void {
    echo $label . ' -> ' . formatValue($value) . "\n";
}

echo "================= TESTS MODELS =================\n";
try { $u = new Utilisateur('Dupont', 'Jean', '2000-01-01', 'M', 'jdupont', 'jean@email.com', password_hash('pass', PASSWORD_DEFAULT), 'normal', false, date('Y-m-d'), 0); recordTest('Model Utilisateur', true); dumpSample('Model Utilisateur', $u); } catch (Throwable $e) { recordTest('Model Utilisateur', false, $e->getMessage()); }
try { $o = new Objet(null, 'Objet cool', '/model.obj', 50.0, null); recordTest('Model Objet', true); dumpSample('Model Objet', $o); } catch (Throwable $e) { recordTest('Model Objet', false, $e->getMessage()); }
try { $r = new Room(null, 'Room Test', 'public', date('Y-m-d'), 0, null, 'theme'); recordTest('Model Room', true); dumpSample('Model Room', $r); } catch (Throwable $e) { recordTest('Model Room', false, $e->getMessage()); }
try { $p = new Post(null, 'Contenu', 'texte', date('Y-m-d H:i:s'), 1, 1); recordTest('Model Post', true); dumpSample('Model Post', $p); } catch (Throwable $e) { recordTest('Model Post', false, $e->getMessage()); }
try { $rep = new Reponse(null, date('Y-m-d H:i:s'), 'Reponse', 1, 1); recordTest('Model Reponse', true); dumpSample('Model Reponse', $rep); } catch (Throwable $e) { recordTest('Model Reponse', false, $e->getMessage()); }
try { $av = new Avatar('Avatar1', 'M', date('Y-m-d'), 'rose', 'brun', 'tshirt', 'lunettes', 1); recordTest('Model Avatar', true); dumpSample('Model Avatar', $av); } catch (Throwable $e) { recordTest('Model Avatar', false, $e->getMessage()); }
try { $g = new Groupe('Groupe Test', 'Description', date('Y-m-d')); recordTest('Model Groupe', true); dumpSample('Model Groupe', $g); } catch (Throwable $e) { recordTest('Model Groupe', false, $e->getMessage()); }
try { $m = new Message(null, 'Contenu msg', date('Y-m-d H:i:s'), 1, 1); recordTest('Model Message', true); dumpSample('Model Message', $m); } catch (Throwable $e) { recordTest('Model Message', false, $e->getMessage()); }
try { $a = new Ami(1, 2, date('Y-m-d H:i:s')); recordTest('Model Ami', true); dumpSample('Model Ami', $a); } catch (Throwable $e) { recordTest('Model Ami', false, $e->getMessage()); }
try { $ach = new Achat(1, date('Y-m-d H:i:s'), 1); recordTest('Model Achat', true); dumpSample('Model Achat', $ach); } catch (Throwable $e) { recordTest('Model Achat', false, $e->getMessage()); }
try { $aj = new Ajouter(1, 1, date('Y-m-d H:i:s')); recordTest('Model Ajouter', true); dumpSample('Model Ajouter', $aj); } catch (Throwable $e) { recordTest('Model Ajouter', false, $e->getMessage()); }
try { $sig = new Signalement(1, 'Raison'); recordTest('Model Signalement', true); dumpSample('Model Signalement', $sig); } catch (Throwable $e) { recordTest('Model Signalement', false, $e->getMessage()); }
try { $san = new Sanction(1, 'Raison'); recordTest('Model Sanction', true); dumpSample('Model Sanction', $san); } catch (Throwable $e) { recordTest('Model Sanction', false, $e->getMessage()); }


echo "\n================= TESTS DAOs (instantiation) =================\n";
try { new UtilisateurDao($pdo); recordTest('DAO Utilisateur', true); } catch (Throwable $e) { recordTest('DAO Utilisateur', false, $e->getMessage()); }
try { new ObjetDao($pdo); recordTest('DAO Objet', true); } catch (Throwable $e) { recordTest('DAO Objet', false, $e->getMessage()); }
try { new RoomDao($pdo); recordTest('DAO Room', true); } catch (Throwable $e) { recordTest('DAO Room', false, $e->getMessage()); }
try { new PostDao($pdo); recordTest('DAO Post', true); } catch (Throwable $e) { recordTest('DAO Post', false, $e->getMessage()); }
try { new ReponseDao($pdo); recordTest('DAO Reponse', true); } catch (Throwable $e) { recordTest('DAO Reponse', false, $e->getMessage()); }
try { new AvatarDao($pdo); recordTest('DAO Avatar', true); } catch (Throwable $e) { recordTest('DAO Avatar', false, $e->getMessage()); }
try { new GroupeDao($pdo); recordTest('DAO Groupe', true); } catch (Throwable $e) { recordTest('DAO Groupe', false, $e->getMessage()); }
try { new MessageDAO($pdo); recordTest('DAO Message', true); } catch (Throwable $e) { recordTest('DAO Message', false, $e->getMessage()); }
try { new AmiDao($pdo); recordTest('DAO Ami', true); } catch (Throwable $e) { recordTest('DAO Ami', false, $e->getMessage()); }
try { new AchatDao($pdo); recordTest('DAO Achat', true); } catch (Throwable $e) { recordTest('DAO Achat', false, $e->getMessage()); }
try { new AjouterDao($pdo); recordTest('DAO Ajouter', true); } catch (Throwable $e) { recordTest('DAO Ajouter', false, $e->getMessage()); }
try { new SignalementDao($pdo); recordTest('DAO Signalement', true); } catch (Throwable $e) { recordTest('DAO Signalement', false, $e->getMessage()); }
try { new SanctionDao($pdo); recordTest('DAO Sanction', true); } catch (Throwable $e) { recordTest('DAO Sanction', false, $e->getMessage()); }


echo "\n================= TESTS findAll =================\n";
$daoList = [
    'UtilisateurDao->findAll()' => fn() => (new UtilisateurDao($pdo))->findAll(),
    'ObjetDao->findAll()'       => fn() => (new ObjetDao($pdo))->findAll(),
    'RoomDao->findAll()'        => fn() => (new RoomDao($pdo))->findAll(),
    'PostDao->findAll()'        => fn() => (new PostDao($pdo))->findAll(),
    'ReponseDao->findAll()'     => fn() => (new ReponseDao($pdo))->findAll(),
    'AvatarDao->findAll()'      => fn() => (new AvatarDao($pdo))->findAll(),
    'GroupeDao->findAll()'      => fn() => (new GroupeDao($pdo))->findAll(),
    'MessageDAO->findAll()'     => fn() => (new MessageDAO($pdo))->findAll(),
    'AmiDao->findAll()'         => fn() => (new AmiDao($pdo))->findAll(),
    'AchatDao->findAll()'       => fn() => (new AchatDao($pdo))->findAll(),
    'AjouterDao->findAll()'     => fn() => (new AjouterDao($pdo))->findAll(),
    'SignalementDao->findAll()' => fn() => (new SignalementDao($pdo))->findAll(),
    'SanctionDao->findAll()'    => fn() => (new SanctionDao($pdo))->findAll(),
];

foreach ($daoList as $label => $call) {
    try {
        $res = $call();
        recordTest($label, is_array($res), 'résultats: ' . (is_array($res) ? count($res) : 0));
        dumpSample($label, $res);
    } catch (Throwable $e) {
        recordTest($label, false, $e->getMessage());
    }
}


echo "\n================= TESTS find(id) & spé =================\n";
try { $u = (new UtilisateurDao($pdo))->find(1); recordTest('UtilisateurDao->find(1)', $u !== null, $u ? $u->getPseudo() : ''); } catch (Throwable $e) { recordTest('UtilisateurDao->find(1)', false, $e->getMessage()); }
try { $u = (new UtilisateurDao($pdo))->find(1); recordTest('UtilisateurDao->find(1)', $u !== null, $u ? $u->getPseudo() : ''); dumpSample('UtilisateurDao->find(1)', $u); } catch (Throwable $e) { recordTest('UtilisateurDao->find(1)', false, $e->getMessage()); }
try { $r = (new RoomDao($pdo))->find(1); recordTest('RoomDao->find(1)', $r !== null, $r ? $r->getNom() : ''); dumpSample('RoomDao->find(1)', $r); } catch (Throwable $e) { recordTest('RoomDao->find(1)', false, $e->getMessage()); }
try { $p = (new PostDao($pdo))->find(1); recordTest('PostDao->find(1)', is_array($p) || is_object($p), 'ok'); dumpSample('PostDao->find(1)', $p); } catch (Throwable $e) { recordTest('PostDao->find(1)', false, $e->getMessage()); }
try { $postsA = (new PostDao($pdo))->findPostsByAuteur(1); recordTest('PostDao->findPostsByAuteur(1)', is_array($postsA), 'count=' . (is_array($postsA) ? count($postsA) : 0)); dumpSample('PostDao->findPostsByAuteur(1)', $postsA); } catch (Throwable $e) { recordTest('PostDao->findPostsByAuteur(1)', false, $e->getMessage()); }
try { $postsR = (new PostDao($pdo))->findPostsByRoom(1); recordTest('PostDao->findPostsByRoom(1)', is_array($postsR), 'count=' . (is_array($postsR) ? count($postsR) : 0)); dumpSample('PostDao->findPostsByRoom(1)', $postsR); } catch (Throwable $e) { recordTest('PostDao->findPostsByRoom(1)', false, $e->getMessage()); }
try { $rep = (new ReponseDao($pdo))->findResponsesByPost(1); recordTest('ReponseDao->findResponsesByPost(1)', is_array($rep), 'count=' . (is_array($rep) ? count($rep) : 0)); dumpSample('ReponseDao->findResponsesByPost(1)', $rep); } catch (Throwable $e) { recordTest('ReponseDao->findResponsesByPost(1)', false, $e->getMessage()); }
try { $amis = (new AmiDao($pdo))->findAmis(1); recordTest('AmiDao->findAmis(1)', is_array($amis), 'count=' . (is_array($amis) ? count($amis) : 0)); dumpSample('AmiDao->findAmis(1)', $amis); } catch (Throwable $e) { recordTest('AmiDao->findAmis(1)', false, $e->getMessage()); }
try { $objRoom = (new RoomDao($pdo))->findObjetsByRoom(1); recordTest('RoomDao->findObjetsByRoom(1)', is_array($objRoom), 'count=' . (is_array($objRoom) ? count($objRoom) : 0)); dumpSample('RoomDao->findObjetsByRoom(1)', $objRoom); } catch (Throwable $e) { recordTest('RoomDao->findObjetsByRoom(1)', false, $e->getMessage()); }
try { $roomsPub = (new RoomDao($pdo))->findPublicRooms(); recordTest('RoomDao->findPublicRooms()', is_array($roomsPub), 'count=' . (is_array($roomsPub) ? count($roomsPub) : 0)); dumpSample('RoomDao->findPublicRooms()', $roomsPub); } catch (Throwable $e) { recordTest('RoomDao->findPublicRooms()', false, $e->getMessage()); }


// Résumé
$total = $pass + $fail;
echo "\n============= RÉSUMÉ =============\n";
echo "PASS : $pass\n";
echo "FAIL : $fail\n";
echo "TOTAL: $total\n";

foreach ($tests as [$name, $ok, $msg]) {
    $status = $ok ? '[PASS]' : '[FAIL]';
    echo "$status $name" . ($msg ? " - $msg" : '') . "\n";
}

echo "================================\n";
exit($fail > 0 ? 1 : 0);
