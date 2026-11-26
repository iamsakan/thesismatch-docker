<?php
require_once 'config.php';
require_once 'header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $role = $_POST['role'] ?? 'student';

    if (empty($name)) 
        $errors[] = 'Ime je obvezno.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        $errors[] = 'Vnesite veljaven email.';
    if (strlen($password) < 8) 
        $errors[] = 'Geslo mora imeti vsaj 8 znakov.';
    if ($password !== $password2) 
        $errors[] = 'Gesli se ne ujemata.';
    if (!in_array($role, ['student', 'profesor'])) 
        $errors[] = 'Neveljavna vloga.';

    
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email je že zaseden.';
        } 
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $confirmed = $role === 'student' ? 1 : 0;
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, confirmed) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, $role, $confirmed]);
            $new_id = $pdo->lastInsertId();

            if ($role === 'student') {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $new_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = $role;
                header('Location: topics.php');
                exit;
            } else {
                $success = true;
            }
        }
    }
}

$selected_role = $_POST['role'] ?? 'student';
?>



<?php if ($success): ?>
    <div class="row justify-content-center mt-5">
        <div class="col-md-5 text-center">
            <div class="card p-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
                <h4 class="mt-3">Registracija uspešna!</h4>
                <?php if ($selected_role === 'profesor'): ?>
                    <p class="text-muted">Tvoj račun mora potrditi admin preden se lahko prijaviš.</p>
                <?php else: ?>
                    <p class="text-muted">Zdaj se lahko prijaviš.</p>
                <?php endif; ?>
                <a href="login.php" class="btn btn-primary mt-2">Prijavi se</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <div class="card p-4 p-md-5">
                <h4 class="mb-1 text-center">Ustvari račun</h4>
                <p class="text-muted text-center mb-3" style="font-size:14px">Pridruži se ThesIS v manj kot minuti.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $e): ?>
                            <div><?= htmlspecialchars($e) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- za izbiro vloge med študentom in prof -->
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <input type="radio" name="role" id="role-student" value="student" class="d-none role-radio" <?= $selected_role === 'student' ? 'checked' : '' ?>>
                            <label for="role-student" class="role-card w-100 p-2 border rounded-3"
                                title="Brskaj in se prijavljaj na teme">
                                <div class="fw-bold" style="text-align: center;">Študent</div>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" name="role" id="role-profesor" value="profesor" class="d-none role-radio" <?= $selected_role === 'profesor' ? 'checked' : '' ?>>
                            <label for="role-profesor" class="role-card w-100 p-2 border rounded-3 mb-2"
                            title="Objavljaj teme in pregleduj prijave">
                                <div class="fw-bold" style="text-align: center;">Profesor</div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1">Ime in priimek</label>
                        <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Janez Novak">
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1">Email</label>
                        <input type="email" name="email" required class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="vas@fri.uni-lj.si">
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1">Geslo</label>
                        <input type="password" name="password" required class="form-control" placeholder="••••••••">
                    </div>
                    <div class="mb-4">
                        <label class="form-label mb-1">Potrdi geslo</label>
                        <input type="password" name="password2" required class="form-control" placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn w-100 fw-bold" 
                    style="background:#1a1a1a;color:white;border-radius:10px;font-size:1rem">
                        Ustvari račun
                    </button>
                </form>
                <div class="text-center mt-3 text-muted">
                    Že imaš račun? <a href="login.php" class="fw-bold text-decoration-none register-underline">Prijava</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>