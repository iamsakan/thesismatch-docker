<?php
require_once 'config.php';
require_once 'header.php';

$search = trim($_GET['search'] ?? '');
$subject_id = $_GET['subject'] ?? '';

$sql = '
    SELECT t.*, s.name as subject_name, u.name as professor_name,
    (SELECT COUNT(*) FROM applications WHERE topic_id = t.id) as app_count
    FROM topics t JOIN subjects s ON t.subject_id = s.id
    JOIN users u ON t.professor_id = u.id WHERE 1=1
    ';
$params = [];

if (!empty($search)) {
    $sql .= ' AND (t.title LIKE ? OR t.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if (!empty($subject_id)) {
    $sql .= ' AND t.subject_id = ?';
    $params[] = $subject_id;
}

$sql .= ' ORDER BY t.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$topics = $stmt->fetchAll();

$subjects = $pdo->query('SELECT * FROM subjects')->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0">Teme</h3>
        <p class="text-muted mb-0" style="font-size:14px">Poišči svojo diplomsko ali seminarsko temo</p>
    </div>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'profesor'): ?>
        <a href="topic_create.php" class="btn btn-primary">
            <i class="bi bi-plus"></i> Objavi temo
        </a>
    <?php endif; ?>
</div>

<form method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" 
                placeholder="Išči po naslovu ali opisu..." 
                value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="subject" class="form-select">
                <option value="">Vsa področja</option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $subject_id == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Išči</button>
        </div>
    </div>
</form>



<?php if (empty($topics)): ?>
    <div class="alert alert-info">Ni tem za prikaz.</div>
<?php else: ?>
    <?php foreach ($topics as $topic): ?>
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
                    <p class="text-muted small mb-2">
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($topic['subject_name']) ?></span>
                        · prof. <?= htmlspecialchars($topic['professor_name']) ?>
                        · <?= $topic['app_count'] ?> <?= $topic['app_count'] === 1 ? 'prijava' : 
                        ($topic['app_count'] > 1 && $topic['app_count'] < 5 ? 'prijave' : 'prijav') ?>
                    </p>

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