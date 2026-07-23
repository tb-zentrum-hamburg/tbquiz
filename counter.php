<?php
/**
 * Einfacher Aufrufzähler für das Tuberkulose-Quiz.
 * Speichert ausschließlich eine Zahl in counter.dat – keine IP-Adressen,
 * keine Cookies, keine personenbezogenen Daten.
 *
 * Aufruf:  counter.php?mode=hit  -> zählt hoch und gibt den Stand zurück
 *          counter.php?mode=get  -> gibt nur den Stand zurück
 * Antwort: {"count":1234}
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$file = __DIR__ . '/counter.dat';
$mode = (isset($_GET['mode']) && $_GET['mode'] === 'get') ? 'get' : 'hit';

$fp = @fopen($file, 'c+');
if ($fp === false) {
    http_response_code(500);
    echo json_encode(['error' => 'storage']);
    exit;
}

flock($fp, LOCK_EX);
$count = (int) stream_get_contents($fp);

if ($mode === 'hit') {
    $count++;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $count);
    fflush($fp);
}

flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['count' => $count]);
