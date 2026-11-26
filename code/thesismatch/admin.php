<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? null;
    $topic_id = $_POST['topic_id'] ?? null;

    if ($action === 'confirm' && $user_id) {
        $stmt = $pdo->prepare('UPDATE users SET confirmed = 1 WHERE id = ?');
        $stmt->execute([$user_id]);
        $success_msg = 'Profesor potrjen!';
    }

    if ($action === 'delete_user' && $user_id) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND role != "admin"');
        $stmt->execute([$user_id]);
        $success_msg = 'Uporabnik izbrisan!';
    }

    if ($action === 'delete_topic' && $topic_id) {
        $stmt = $pdo->prepare('DELETE FROM topics WHERE id = ?');
        $stmt->execute([$topic_id]);
        $success_msg = 'Tema izbrisana!';
    }

    if ($action === 'add_subject') {
        $name = trim($_POST['subject_name'] ?? '');
        if (!empty($name)) {
            $stmt = $pdo->prepare('INSERT INTO subjects (name) VALUES (?)');
            $stmt->execute([$name]);
            $success_msg = 'Področje "' . htmlspecialchars($name) . '" dodano!';
        }
    }
}

$stmt = $pdo->query('SELECT COUNT(*) as total FROM users')->fetch();
$total_users = $stmt['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM topics')->fetch();
$total_topics = $stmt['total'];

$stmt = $pdo->query('SELECT COUNT(*) as total FROM users WHERE confirmed = 0')->fetch();
$pending_profs = $stmt['total'];

$stmt = $pdo->query('
    SELECT * FROM users 
    WHERE role = "profesor" AND confirmed = 0
');
$unconfirmed = $stmt->fetchAll();

$stmt = $pdo->query('
    SELECT u.*, 
    (SELECT COUNT(*) FROM topics WHERE professor_id = u.id) as topic_count
    FROM users u WHERE u.role != "admin"
    ORDER BY u.created_at DESC
');
$users = $stmt->fetchAll();

$stmt = $pdo->query('
    SELECT t.*, s.name as subject_name, u.name as professor_name
    FROM topics t JOIN subjects s ON t.subject_id = s.id
    JOIN users u ON t.professor_id = u.id
    ORDER BY t.created_at DESC
');
$topics = $stmt->fetchAll();
?>

<h3 class="mb-4">Admin panel</h3>

<?php if ($success_msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="display-5 fw-bold text-primary"><?= $total_users ?></div>
            <div class="text-muted">Uporabnikov</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="display-5 fw-bold text-primary"><?= $total_topics ?></div>
            <div class="text-muted">Tem</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="display-5 fw-bold 
                <?= $pending_profs > 0 ? 'text-danger' : 'text-primary' ?>">
                <?= $pending_profs ?>
            </div>
            <div class="text-muted">Čaka potrditev</div>
        </div>
    </div>
</div>

<?php if (!empty($unconfirmed)): ?>
    <h5 class="mb-3">Profesorji čakajo potrditev</h5>
    <div class="card mb-4">
        <div class="card-body">
            <?php foreach ($unconfirmed as $u): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong><?= htmlspecialchars($u['name']) ?></strong>
                        <span class="text-muted ms-2"><?= htmlspecialchars($u['email']) ?></span>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="confirm">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check"></i> Potrdi
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<h5 class="mb-3">Vsi uporabniki</h5>
<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Ime</th>
                    <th>Email</th>
                    <th>Vloga</th>
                    <th>Status</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge <?= $u['role'] === 'profesor' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'profesor'): ?>
                                <span class="badge <?= $u['confirmed'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= $u['confirmed'] ? 'Potrjen' : 'Čaka' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">Aktiven</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Res izbrišeš?')">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<h5 class="mb-3">Vse teme</h5>
<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Naslov</th>
                    <th>Področje</th>
                    <th>Profesor</th>
                    <th>Status</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topics as $t): ?>
                    <tr>
                        <td>
                            <a href="topic_detail.php?id=<?= $t['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($t['title']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($t['subject_name']) ?></td>
                        <td><?= htmlspecialchars($t['professor_name']) ?></td>
                        <td>
                            <span class="badge <?= $t['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $t['status'] === 'open' ? 'Odprto' : 'Zasedeno' ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Res izbrišeš temo?')">
                                <input type="hidden" name="action" value="delete_topic">
                                <input type="hidden" name="topic_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<h5 class="mb-3">Dodaj področje</h5>
<div class="card mb-4">
    <div class="card-body">
        <form method="POST" class="d-flex gap-2">
            <input type="hidden" name="action" value="add_subject">
            <input type="text" name="subject_name" class="form-control" placeholder="npr. Statistika, Matematika...">
            <button type="submit" class="btn btn-primary">Dodaj</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>