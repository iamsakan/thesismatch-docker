<?php
require_once 'config.php';
require_once 'header.php';

$stmt = $pdo->query('SELECT COUNT(*) as total FROM topics WHERE status = "open"');
$open_topics = $stmt->fetch()['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM users WHERE role = "profesor"');
$professors = $stmt->fetch()['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM users WHERE role = "student"');
$students = $stmt->fetch()['total'];

$stmt = $pdo->query('
    SELECT t.*, s.name as subject_name, u.name as professor_name 
    FROM topics t 
    JOIN subjects s ON t.subject_id = s.id 
    JOIN users u ON t.professor_id = u.id 
    WHERE t.status = "open" 
    ORDER BY t.created_at DESC 
    LIMIT 3
');
$latest_topics = $stmt->fetchAll();
?>

<div class="text-center py-5">
    <h1 class="mb-3">Najdi svojo diplomsko temo</h1>
    <p class="lead text-muted mb-4">Profesorji objavljajo teme, študenti se prijavljajo. Enostavno.</p>
    <a href="topics.php" class="btn btn-primary btn-lg me-2">Brskaj po temah</a>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn btn-outline-primary btn-lg">Pridruži se</a>
    <?php endif; ?>
</div>

<div class="row text-center mb-5">
    <div class="col-md-4">
        <div class="card p-4">
            <div class="display-4 fw-bold text-primary"><?= $open_topics ?></div>
            <div class="text-muted">Odprtih tem</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4">
            <div class="display-4 fw-bold text-primary"><?= $professors ?></div>
            <div class="text-muted">Profesorjev</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4">
            <div class="display-4 fw-bold text-primary"><?= $students ?></div>
            <div class="text-muted">Študentov</div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-3">Zadnje objavljene teme</h4>
    <a href="topics.php" class="btn btn-outline-secondary">Več tem</a>
</div>

<?php if (empty($latest_topics)): ?>
    <p class="text-muted">Še ni objavljenih tem.</p>
<?php else: ?>
    <?php foreach ($latest_topics as $topic): ?>
        <a href="topic_detail.php?id=<?= $topic['id'] ?>" class="text-decoration-none">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">
                            <?= htmlspecialchars($topic['title']) ?>
                        </h5>
                        <span class="badge <?= $topic['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $topic['status'] === 'open' ? 'Odprto' : 'Zasedeno' ?>
                        </span>
                    </div>
                    <p class="text-muted small"><?= htmlspecialchars($topic['subject_name']) ?> · prof. <?= htmlspecialchars($topic['professor_name']) ?></p>
                    <p class="card-text"><?= htmlspecialchars(substr($topic['description'], 0, 150)) ?>...</p>
                    <?php if (!empty($topic['required_skills'])): ?>
                        <p class="small text-muted">
                            <i class="bi bi-tools"></i> <?= htmlspecialchars($topic['required_skills']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once 'footer.php'; ?>