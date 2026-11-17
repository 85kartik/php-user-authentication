<?php
require __DIR__ . '/../app/bootstrap.php';
if (!isset($_SESSION['user'])) {
    $_SESSION['notice'] = 'Sign in to access the dashboard.';
    go('/login.php');
}
$user = $_SESSION['user'];
$title = 'Dashboard';
require __DIR__ . '/../app/header.php';
?>
<h1>Dashboard</h1>
<p class="intro">Welcome, <?= escape($user['name']) ?>.</p>
<dl class="profile">
    <dt>Account email</dt>
    <dd><?= escape($user['email']) ?></dd>
</dl>
<form action="/logout.php" method="post">
    <?= csrfField() ?>
    <button>Sign out</button>
</form>
<?php require __DIR__ . '/../app/footer.php'; ?>
