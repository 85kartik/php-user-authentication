<?php
require __DIR__ . '/../app/bootstrap.php';

if (isset($_SESSION['user'])) {
    go('/dashboard.php');
}

$title = 'Create an account';
$name = '';
$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $name = trim(post('name'));
    $email = strtolower(trim(post('email')));
    $password = post('password');

    if ($name === '' || strlen($name) > 100 || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a name and a valid email address.';
    } elseif (strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
        $error = 'Use a password between 8 and 72 bytes.';
    } elseif ($password !== post('confirm_password')) {
        $error = 'The passwords do not match.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $statement = database()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $statement->bind_param('sss', $name, $email, $hash);
            $statement->execute();
            $_SESSION['notice'] = 'Your account is ready. You can now sign in.';
            go('/login.php');
        } catch (mysqli_sql_exception $exception) {
            $error = $exception->getCode() === 1062
                ? 'An account with this email already exists.'
                : 'Registration is temporarily unavailable. Try again later.';
        }
    }
}
require __DIR__ . '/../app/header.php';
?>
<h1>Create an account</h1>
<p class="intro">Register to access your private dashboard.</p>
<?php if ($error !== ''): ?><p class="alert error" role="alert"><?= escape($error) ?></p><?php endif; ?>
<form method="post">
    <?= csrfField() ?>
    <label>Name<input name="name" autocomplete="name" value="<?= escape($name) ?>" maxlength="100" required></label>
    <label>Email<input type="email" name="email" autocomplete="email" value="<?= escape($email) ?>" maxlength="254" required></label>
    <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
    <label>Confirm password<input type="password" name="confirm_password" autocomplete="new-password" minlength="8" required></label>
    <button>Create account</button>
</form>
<p class="switch">Already registered? <a href="/login.php">Sign in</a></p>
<?php require __DIR__ . '/../app/footer.php'; ?>
