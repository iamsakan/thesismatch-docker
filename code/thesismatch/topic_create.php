<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'profesor') {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $required_skills = trim($_POST['required_skills'] ?? '');
    $subject_id = $_POST['subject_id'] ?? '';

    if (empty($title)) $errors[] = 'Naslov je obvezen.';
    if (empty($description)) $errors[] = 'Opis je obvezen.';
    if (empty($subject_id)) $errors[] = 'Področje je obvezno.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO topics (title, description, required_skills, subject_id, professor_id) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$title, $description, $required_skills, $subject_id, $_SESSION['user_id']]);
        header('Location: topics.php');
        exit;
    }
}

$subjects = $pdo->query('SELECT * FROM subjects')->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card p-4 mt-4">
            <h3 class="mb-4">Objavi novo temo</h3>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Naslov teme *</label>
                    <input type="text" name="title" required class="form-control" 
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Področje *</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">Izberi področje...</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= ($_POST['subject_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Opis *</label>
                    <textarea name="description" required class="form-control" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Zahtevano znanje <span class="text-muted">(neobvezno)</span></label>
                    <input type="text" name="required_skills" class="form-control" 
                        placeholder="npr. Python, SQL, PHP..."
                        value="<?= htmlspecialchars($_POST['required_skills'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Objavi temo</button>
                <a href="topics.php" class="btn btn-outline-secondary ms-2">Prekliči</a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>