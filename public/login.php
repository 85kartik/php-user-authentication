<?php
require __DIR__ . '/../app/bootstrap.php';

if (isset($_SESSION['user'])) {
    go('/dashboard.php');
}

$title = 'Sign in';
$email = '';
$error = '';
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email = strtolower(trim(post('email')));
    $password = post('password');
    $error = 'Email or password is incorrect.';

    if (strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) <= 72) {
        try {
            $statement = database()->prepare('SELECT id, name, email, password FROM users WHERE email = ?');
            $statement->bind_param('s', $email);
            $statement->execute();
            $user = $statement->get_result()->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                unset($user['password']);
                $_SESSION = ['user' => $user];
                go('/dashboard.php');
            }
        } catch (mysqli_sql_exception $exception) {
            $error = 'Sign-in is temporarily unavailable. Try again later.';
        }
    }
}
require __DIR__ . '/../app/header.php';
?>
<h1>Sign in</h1>
<p class="intro">Access your private dashboard.</p>
<?php if ($notice !== ''): ?><p class="alert success" role="status"><?= escape($notice) ?></p><?php endif; ?>
<?php if ($error !== ''): ?><p class="alert error" role="alert"><?= escape($error) ?></p><?php endif; ?>
<form method="post">
    <?= csrfField() ?>
    <label>Email<input type="email" name="email" autocomplete="username" value="<?= escape($email) ?>" maxlength="254" required></label>
    <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
    <button>Sign in</button>
</form>
<p class="switch">New here? <a href="/register.php">Create an account</a></p>
<?php require __DIR__ . '/../app/footer.php'; ?>
