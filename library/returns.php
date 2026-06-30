<?php
require 'config/db.php';

$FINE_PER_DAY = 5; // PHP 5.00 per day overdue, adjust as needed

if (isset($_POST['returnBook'])) {
    $borrowID = $_POST['borrowID'];

    $stmt = $conn->prepare("SELECT * FROM borrowing WHERE borrowID=?");
    $stmt->execute([$borrowID]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record && !$record['returnDate']) {
        $conn->beginTransaction();
        $stmt = $conn->prepare("UPDATE borrowing SET returnDate = CURDATE() WHERE borrowID=?");
        $stmt->execute([$borrowID]);

        $stmt = $conn->prepare("UPDATE books SET copiesAvailable = copiesAvailable + 1 WHERE bookID=?");
        $stmt->execute([$record['bookID']]);
        $conn->commit();
    }
}

$records = $conn->query("
    SELECT br.*, b.title, m.memberFName, m.memberLName
    FROM borrowing br
    JOIN books b ON br.bookID = b.bookID
    JOIN members m ON br.memberID = m.memberID
    WHERE br.returnDate IS NULL AND br.borrowStat = 1
    ORDER BY br.dueDate ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Book Return & Overdue</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Book Return & Overdue</h2>

<table border="1" cellpadding="5">
<tr><th>Member</th><th>Book Title</th><th>Borrow Date</th><th>Due Date</th><th>Days Overdue</th><th>Fine</th><th>Action</th></tr>
<?php foreach ($records as $r):
    $due = new DateTime($r['dueDate']);
    $today = new DateTime();
    $daysOverdue = $today > $due ? $today->diff($due)->days : 0;
    $fine = $daysOverdue * $FINE_PER_DAY;
?>
<tr>
    <td><?= htmlspecialchars($r['memberFName'] . ' ' . $r['memberLName']) ?></td>
    <td><?= htmlspecialchars($r['title']) ?></td>
    <td><?= $r['borrowDate'] ?></td>
    <td><?= $r['dueDate'] ?></td>
    <td><?= $daysOverdue ?></td>
    <td>₱<?= number_format($fine, 2) ?></td>
    <td>
        <form method="post" style="margin:0;">
            <input type="hidden" name="borrowID" value="<?= $r['borrowID'] ?>">
            <button type="submit" name="returnBook">Mark as Returned</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
