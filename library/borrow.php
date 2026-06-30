<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['memberID'])) {
    header("Location: borrow_login.php");
    exit;
}
$memberID = $_SESSION['memberID'];

$stmt = $conn->prepare("SELECT * FROM members WHERE memberID=?");
$stmt->execute([$memberID]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member || !$member['memberStat']) {
    session_destroy();
    die("You are not eligible to borrow. <a href='borrow_login.php'>Back to login</a>");
}

$error = '';
$success = '';

if (isset($_POST['borrow'])) {
    $bookID = $_POST['bookID'];
    $dueDate = $_POST['dueDate'];

    // Re-check overdue status
    $stmt = $conn->prepare("SELECT COUNT(*) FROM borrowing WHERE memberID=? AND returnDate IS NULL AND dueDate < CURDATE() AND borrowStat=1");
    $stmt->execute([$memberID]);
    if ($stmt->fetchColumn() > 0) {
        $error = "You have overdue book(s). Cannot borrow.";
    } else {
        // Check copies available
        $stmt = $conn->prepare("SELECT copiesAvailable FROM books WHERE bookID=? AND bookStat=1");
        $stmt->execute([$bookID]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book || $book['copiesAvailable'] < 1) {
            $error = "No copies available for this book.";
        } else {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO borrowing (bookID, memberID, borrowDate, dueDate) VALUES (?, ?, CURDATE(), ?)");
            $stmt->execute([$bookID, $memberID, $dueDate]);

            $stmt = $conn->prepare("UPDATE books SET copiesAvailable = copiesAvailable - 1 WHERE bookID=?");
            $stmt->execute([$bookID]);
            $conn->commit();

            $success = "Book borrowed successfully. Due date: $dueDate";
        }
    }
}

$books = $conn->query("SELECT * FROM books WHERE bookStat=1 AND copiesAvailable > 0")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Borrow a Book</title></head>
<body>
<h2>Borrow a Book</h2>
<p>Member: <?= htmlspecialchars($member['memberFName'] . ' ' . $member['memberLName']) ?></p>
<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

<form method="post">
    Book:
    <select name="bookID" required>
        <option value="">-- Select --</option>
        <?php foreach ($books as $b): ?>
            <option value="<?= $b['bookID'] ?>">
                <?= htmlspecialchars($b['title']) ?> (<?= $b['copiesAvailable'] ?> available)
            </option>
        <?php endforeach; ?>
    </select><br><br>
    Due Date: <input type="date" name="dueDate" required><br><br>
    <button type="submit" name="borrow">Borrow</button>
</form>

<br><a href="index.php">Back to Menu</a>
</body>
</html>
