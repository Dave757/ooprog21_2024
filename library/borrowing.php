<?php
require 'config/db.php';

if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO borrowing (bookID, memberID, borrowDate, dueDate) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['bookID'], $_POST['memberID'], $_POST['borrowDate'], $_POST['dueDate']]);
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE borrowing SET bookID=?, memberID=?, borrowDate=?, dueDate=? WHERE borrowID=?");
    $stmt->execute([$_POST['bookID'], $_POST['memberID'], $_POST['borrowDate'], $_POST['dueDate'], $_POST['borrowID']]);
}

if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE borrowing SET borrowStat=0 WHERE borrowID=?");
    $stmt->execute([$_GET['deactivate']]);
}
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE borrowing SET borrowStat=1 WHERE borrowID=?");
    $stmt->execute([$_GET['activate']]);
}

$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM borrowing WHERE borrowID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$books = $conn->query("SELECT * FROM books WHERE bookStat=1")->fetchAll(PDO::FETCH_ASSOC);
$members = $conn->query("SELECT * FROM members WHERE memberStat=1")->fetchAll(PDO::FETCH_ASSOC);

$records = $conn->query("
    SELECT br.*, b.title, m.memberFName, m.memberLName
    FROM borrowing br
    JOIN books b ON br.bookID = b.bookID
    JOIN members m ON br.memberID = m.memberID
    ORDER BY br.borrowID DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Borrowing Records</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Borrowing Records</h2>
<p>Note: to create a new borrowing transaction with automatic copy deduction, use <a href="borrow_login.php">Borrow a Book</a> instead. This page is for direct record management/correction.</p>

<form method="post">
    <input type="hidden" name="borrowID" value="<?= $editRow['borrowID'] ?? '' ?>">
    Book:
    <select name="bookID" required>
        <option value="">-- Select --</option>
        <?php foreach ($books as $b): ?>
            <option value="<?= $b['bookID'] ?>" <?= (isset($editRow) && $editRow['bookID'] == $b['bookID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['title']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    Member:
    <select name="memberID" required>
        <option value="">-- Select --</option>
        <?php foreach ($members as $m): ?>
            <option value="<?= $m['memberID'] ?>" <?= (isset($editRow) && $editRow['memberID'] == $m['memberID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['memberLName'] . ', ' . $m['memberFName']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    Borrow Date: <input type="date" name="borrowDate" value="<?= $editRow['borrowDate'] ?? '' ?>" required><br><br>
    Due Date: <input type="date" name="dueDate" value="<?= $editRow['dueDate'] ?? '' ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Book</th><th>Member</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($records as $r): ?>
<tr>
    <td><?= $r['borrowID'] ?></td>
    <td><?= htmlspecialchars($r['title']) ?></td>
    <td><?= htmlspecialchars($r['memberLName'] . ', ' . $r['memberFName']) ?></td>
    <td><?= $r['borrowDate'] ?></td>
    <td><?= $r['dueDate'] ?></td>
    <td><?= $r['returnDate'] ?? '-' ?></td>
    <td><?= $r['borrowStat'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?edit=<?= $r['borrowID'] ?>">Edit</a>
        <?php if ($r['borrowStat']): ?>
            | <a href="?deactivate=<?= $r['borrowID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $r['borrowID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
