<?php
require_once 'config.php';
require_once 'header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) $errors[] = 'Email je obvezen.';
    if (empty($password)) $errors[] = 'Geslo je obvezno.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Napačen email ali geslo.';
        } elseif ($user['confirmed'] == 0) {
            $errors[] = 'Tvoj račun še ni potrjen. Počakaj na potrditev admina.';
        } else {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header('Location: topics.php');
            exit;
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card p-4 p-md-5" style="border-radius:16px">

            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1">Dobrodošli nazaj</h3>
                <p class="text-muted">Prijavite se v vaš račun</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-500 mb-1">Email</label>
                    <input type="email" name="email" required class="form-control"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="vas@fri.uni-lj.si">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-500 mb-1">Geslo</label>
                    <input type="password" name="password" required class="form-control"
                        placeholder="••••••••">
                </div>
                <button type="submit" class="btn w-100 fw-bold" 
                    style="background:#1a1a1a;color:white;border-radius:10px;font-size:1rem">
                    Prijava
                </button>
            </form>

            <div class="text-center mt-4 text-muted">
                Nimate računa? <a href="register.php" class="fw-bold text-decoration-none register-underline">Registracija</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>