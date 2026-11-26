<?php
require_once '../config.php';

$search = trim($_GET['q'] ?? '');

if (strlen($search) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare('
    SELECT t.id, t.title, s.name as subject_name, u.name as professor_name
    FROM topics t JOIN subjects s ON t.subject_id = s.id
    JOIN users u ON t.professor_id = u.id
    WHERE t.title LIKE ? OR t.description LIKE ?
    LIMIT 5
');
$stmt->execute(['%'.$search.'%', '%'.$search.'%']);
$results = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($results);