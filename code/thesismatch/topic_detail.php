<?php
require_once 'config.php';
require_once 'header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: topics.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT t.*, s.name as subject_name, u.name as professor_name
    FROM topics t JOIN subjects s ON t.subject_id = s.id
    JOIN users u ON t.professor_id = u.id WHERE t.id = ?
');

$stmt->execute([$id]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: topics.php');
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) as total FROM applications WHERE topic_id = ?');
$stmt->execute([$id]);
$app_count = $stmt->fetch()['total'];

$already_applied = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $stmt = $pdo->prepare('SELECT id FROM applications WHERE student_id = ? AND topic_id = ?');
    $stmt->execute([$_SESSION['user_id'], $id]);
    $already_applied = $stmt->fetch() ? true : false;
}

$stmt = $pdo->prepare('
    SELECT c.*, u.name as user_name, u.role
    FROM comments c JOIN users u ON c.user_id = u.id
    WHERE c.topic_id = ? ORDER BY c.created_at ASC
');
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

$errors = [];
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_POST['action'] === 'delete' && $_SESSION['role'] === 'profesor') {
    if ($topic['professor_id'] == $_SESSION['user_id']) {
        $stmt = $pdo->prepare('DELETE FROM topics WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: topics.php');
        exit;
        }
    }

    if ($_POST['action'] === 'apply' && $_SESSION['role'] === 'student') {
        if ($already_applied) {
            $errors[] = 'Že si oddal interes za to temo.';
        } elseif ($topic['status'] === 'taken') {
            $errors[] = 'Ta tema je že zasedena.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO applications (student_id, topic_id) VALUES (?, ?)');
            $stmt->execute([$_SESSION['user_id'], $id]);
            $success_msg = 'Interes uspešno oddan!';
            $already_applied = true;
        }
    }

    if ($_POST['action'] === 'comment') {
        $text = trim($_POST['text'] ?? '');
        if (empty($text)) {
            $errors[] = 'Komentar ne sme biti prazen.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO comments (text, user_id, topic_id) VALUES (?, ?, ?)');
            $stmt->execute([$text, $_SESSION['user_id'], $id]);
            header('Location: topic_detail.php?id=' . $id);
            exit;
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="topics.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Nazaj
    </a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'profesor' && $topic['professor_id'] == $_SESSION['user_id']): ?>
        <form method="POST" onsubmit="return confirm('Res hočeš izbrisati to temo?')">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Izbriši temo
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h3><?= htmlspecialchars($topic['title']) ?></h3>
            <span class="badge <?= $topic['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?> fs-6">
                <?= $topic['status'] === 'open' ? 'Odprto' : 'Zasedeno' ?>
            </span>
        </div>
        <p class="text-muted">
            <span class="badge bg-light text-dark"><?= htmlspecialchars($topic['subject_name']) ?></span>
            · prof. <?= htmlspecialchars($topic['professor_name']) ?>
            · <?= $app_count?>
            <?= $app_count === 1 ? 'prijava' : ($app_count > 1 && $app_count < 5 ? 'prijave' : 'prijav') ?>
        </p>
        <p class="mt-3"><?= nl2br(htmlspecialchars($topic['description'])) ?></p>
        <?php if (!empty($topic['required_skills'])): ?>
            <p class="text-muted">
                <i class="bi bi-tools"></i> <?= htmlspecialchars($topic['required_skills']) ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student' && $topic['status'] === 'open'): ?>
            <hr>
            <?php if ($already_applied): ?>
                <button class="btn btn-success" disabled>
                    <i class="bi bi-check"></i> Interes oddan
                </button>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="apply">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Oddaj interes
                    </button>
                </form>
            <?php endif; ?>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <hr>
            <p class="text-muted">
                <a href="login.php">Prijavi se</a> za oddajo interesa.
            </p>
        <?php endif; ?>
    </div>
</div>

<h5 class="mb-3">Komentarji (<?= count($comments) ?>)</h5>

<?php foreach ($comments as $comment): ?>
    <div class="card mb-2">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2 mb-1">
                <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                <?php if ($comment['role'] === 'profesor'): ?>
                    <span class="badge bg-primary">Profesor</span>
                <?php endif; ?>
                <small class="text-muted"><?= date('j. n. Y', strtotime($comment['created_at'])) ?></small>
            </div>
            <p class="mb-0"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
        </div>
    </div>
<?php endforeach; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="card mt-3">
        <div class="card-body">
            <h6>Dodaj komentar</h6>
            <form method="POST">
                <input type="hidden" name="action" value="comment">
                <div class="mb-2">
                    <textarea name="text" class="form-control" rows="3" 
                        placeholder="Napiši komentar ali vprašanje..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Objavi</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <p class="text-muted mt-3">
        <a href="login.php">Prijavi se</a> za dodajanje komentarja.
    </p>
<?php endif; ?>

<?php require_once 'footer.php'; ?>