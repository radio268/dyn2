<?php
$pdo = new PDO("mysql:host=localhost;dbname=maresm_websiteproject", "maresm", "1234567");

// Return list of site IDs
if (isset($_GET['list'])) {
    $stmt = $pdo->query("SELECT id FROM sites");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($rows);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT name, html, css, js FROM sites WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    if ($data)
    {
        echo json_encode($data);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id >= 1 && $id <= 10) {
        echo "Admin-only site. Cannot edit.";
        exit;
    }

    $name = $_POST['name'] ?? '';
    $html = $_POST['html'] ?? '';
    $css  = $_POST['css'] ?? '';
    $js   = $_POST['js'] ?? '';

    $exists = $pdo->prepare("SELECT id FROM sites WHERE id = ?");
    $exists->execute([$id]);

    if ($exists->fetch()) {
        $stmt = $pdo->prepare("UPDATE sites SET name = ?, html = ?, css = ?, js = ? WHERE id = ?");
        $stmt->execute([$name, $html, $css, $js, $id]);
        echo "Site updated.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO sites (id, name, html, css, js) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $name, $html, $css, $js]);
        echo "Site created.";
    }
    exit;
}
?>
