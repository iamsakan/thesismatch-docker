<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'profesor') {
    $app_id = $_POST['app_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($app_id && in_array($action, ['accepted', 'rejected'])) {
        $stmt = $pdo->prepare('
            SELECT a.*, t.professor_id 
            FROM applications a 
            JOIN topics t ON a.topic_id = t.id 
            WHERE a.id = ?
        ');
        $stmt->execute([$app_id]);
        $app = $stmt->fetch();

        if ($app && $app['professor_id'] == $_SESSION['user_id']) {
            $stmt = $pdo->prepare('UPDATE applications SET status = ? WHERE id = ?');
            $stmt->execute([$action, $app_id]);

            if ($action === 'accepted') {
                $stmt = $pdo->prepare('UPDATE topics SET status = "taken" WHERE id = ?');
                $stmt->execute([$app['topic_id']]);
            }

            $success_msg = $action === 'accepted' ? 'Prijava sprejeta!' : 'Prijava zavrnjena.';
        }
    }
}

if ($_SESSION['role'] === 'student') {
    $stmt = $pdo->prepare('
        SELECT a.*, t.title as topic_title, t.id as topic_id
        FROM applications a
        JOIN topics t ON a.topic_id = t.id
        WHERE a.student_id = ?
        ORDER BY a.created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('
        SELECT a.*, t.title as topic_title, t.id as topic_id, u.name as student_name
        FROM applications a
        JOIN topics t ON a.topic_id = t.id
        JOIN users u ON a.student_id = u.id
        WHERE t.professor_id = ?
        ORDER BY a.created_at DESC
    ');
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
}
?>

<h3 class="mb-4">
    <?= $_SESSION['role'] === 'student' ? 'Moje prijave' : 'Prijave na moje teme' ?>
</h3>

<?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
<?php endif; ?>

<?php if (empty($applications)): ?>
    <div class="alert alert-info">Ni prijav za prikaz.</div>
<?php else: ?>
    <?php foreach ($applications as $app): ?>
        <a href="topic_detail.php?id=<?= $app['topic_id'] ?>" class="text-decoration-none">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">
                                    <?= htmlspecialchars($app['topic_title']) ?>
                            </h5>
                            <?php if ($_SESSION['role'] === 'profesor'): ?>
                                <p class="text-muted mb-1">Študent: <?= htmlspecialchars($app['student_name']) ?></p>
                            <?php endif; ?>
                            <small class="text-muted">Oddano: <?= $app['created_at'] ?></small>
                        </div>
                        <span class="badge <?= 
                            $app['status'] === 'pending' ? 'bg-warning text-dark' : 
                            ($app['status'] === 'accepted' ? 'bg-success' : 'bg-danger') ?>">
                            <?= $app['status'] === 'pending' ? 'V čakanju' : 
                                ($app['status'] === 'accepted' ? 'Sprejeto' : 'Zavrnjeno') ?>
                        </span>
                    </div>

                    <?php if ($_SESSION['role'] === 'profesor' && $app['status'] === 'pending'): ?>
                        <div class="mt-3 d-flex gap-2">
                            <form method="POST">
                                <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                <input type="hidden" name="action" value="accepted">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check"></i> Sprejmi
                                </button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x"></i> Zavrni
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once 'footer.php'; ?>