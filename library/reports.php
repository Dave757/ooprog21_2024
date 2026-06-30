<?php
require 'config/db.php';

// Category summary: total books, borrowed (currently out), available
$catSql = "
    SELECT
        COALESCE(b.category, 'Uncategorized') AS category,
        SUM(b.copiesAvailable) AS available,
        COALESCE((
            SELECT COUNT(*) FROM borrowing br
            JOIN books b2 ON br.bookID = b2.bookID
            WHERE COALESCE(b2.category,'Uncategorized') = COALESCE(b.category,'Uncategorized')
            AND br.returnDate IS NULL AND br.borrowStat = 1
        ), 0) AS borrowed,
        COUNT(*) AS totalBooks
    FROM books b
    WHERE b.bookStat = 1
    GROUP BY COALESCE(b.category, 'Uncategorized')
";
$categories = $conn->query($catSql)->fetchAll(PDO::FETCH_ASSOC);

// Most borrowed books (all-time count, including returned)
$mostBorrowed = $conn->query("
    SELECT b.title, b.author, COUNT(br.borrowID) AS timesBorrowed
    FROM books b
    JOIN borrowing br ON b.bookID = br.bookID
    GROUP BY b.bookID
    ORDER BY timesBorrowed DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Library Reports</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Library Reports</h2>

<h3>Borrowing Summary by Category</h3>
<table border="1" cellpadding="5">
<tr><th>Category</th><th>Total Books</th><th>Borrowed</th><th>Available</th></tr>
<?php foreach ($categories as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['category']) ?></td>
    <td><?= $c['totalBooks'] ?></td>
    <td><?= $c['borrowed'] ?></td>
    <td><?= $c['available'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<br>
<h3>Most Borrowed Books</h3>
<table border="1" cellpadding="5">
<tr><th>Book Title</th><th>Author</th><th>Times Borrowed</th></tr>
<?php foreach ($mostBorrowed as $m): ?>
<tr>
    <td><?= htmlspecialchars($m['title']) ?></td>
    <td><?= htmlspecialchars($m['author']) ?></td>
    <td><?= $m['timesBorrowed'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
