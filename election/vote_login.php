<?php
session_start();
require 'config/db.php';
$error = '';

if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voterID=? AND voterPass=?");
    $stmt->execute([$_POST['voterID'], $_POST['voterPass']]);
    $voter = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voter) {
        $error = "Invalid Voter ID or Password.";
    } elseif (!$voter['voterStat']) {
        $error = "Your account is inactive. You cannot vote.";
    } elseif ($voter['voted']) {
        $error = "You have already voted.";
    } else {
        $_SESSION['voterID'] = $voter['voterID'];
        header("Location: vote.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Voter Login</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Voter Login</h2>
<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<form method="post">
    Voter ID: <input type="text" name="voterID" required><br><br>
    Password: <input type="password" name="voterPass" required><br><br>
    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
