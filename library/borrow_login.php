<?php
session_start();
require 'config/db.php';
$error = '';

if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE memberID=? AND memberPass=?");
    $stmt->execute([$_POST['memberID'], $_POST['memberPass']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        $error = "Invalid Member ID or Password.";
    } elseif (!$member['memberStat']) {
        $error = "Your membership is inactive. You cannot borrow.";
    } else {
        // Check for overdue books (active borrow records past due date with no return)
        $stmt = $conn->prepare("
            SELECT * FROM borrowing
            WHERE memberID = ? AND returnDate IS NULL AND dueDate < CURDATE() AND borrowStat = 1
        ");
        $stmt->execute([$member['memberID']]);
        $overdue = $stmt->fetchAll();

        if (count($overdue) > 0) {
            $error = "You have overdue book(s). Please return them before borrowing again.";
        } else {
            $_SESSION['memberID'] = $member['memberID'];
            header("Location: borrow.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Member Login</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Member Login (Borrow a Book)</h2>
<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<form method="post">
    Member ID: <input type="text" name="memberID" required><br><br>
    Password: <input type="password" name="memberPass" required><br><br>
    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
