<?php
require 'config/db.php';

if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO books (isbn, title, author, publisher, yearPublished, category, copiesAvailable) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['isbn'], $_POST['title'], $_POST['author'], $_POST['publisher'], $_POST['yearPublished'], $_POST['category'], $_POST['copiesAvailable']]);
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE books SET isbn=?, title=?, author=?, publisher=?, yearPublished=?, category=?, copiesAvailable=? WHERE bookID=?");
    $stmt->execute([$_POST['isbn'], $_POST['title'], $_POST['author'], $_POST['publisher'], $_POST['yearPublished'], $_POST['category'], $_POST['copiesAvailable'], $_POST['bookID']]);
}

if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE books SET bookStat=0 WHERE bookID=?");
    $stmt->execute([$_GET['deactivate']]);
}
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE books SET bookStat=1 WHERE bookID=?");
    $stmt->execute([$_GET['activate']]);
}

$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE bookID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$books = $conn->query("SELECT * FROM books")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Books</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Books</h2>

<form method="post">
    <input type="hidden" name="bookID" value="<?= $editRow['bookID'] ?? '' ?>">
    ISBN: <input type="text" name="isbn" value="<?= $editRow['isbn'] ?? '' ?>"><br><br>
    Title: <input type="text" name="title" value="<?= htmlspecialchars($editRow['title'] ?? '') ?>" required><br><br>
    Author: <input type="text" name="author" value="<?= htmlspecialchars($editRow['author'] ?? '') ?>"><br><br>
    Publisher: <input type="text" name="publisher" value="<?= htmlspecialchars($editRow['publisher'] ?? '') ?>"><br><br>
    Year Published: <input type="number" name="yearPublished" value="<?= $editRow['yearPublished'] ?? '' ?>"><br><br>
    Category: <input type="text" name="category" value="<?= htmlspecialchars($editRow['category'] ?? '') ?>"><br><br>
    Copies Available: <input type="number" name="copiesAvailable" value="<?= $editRow['copiesAvailable'] ?? 0 ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Year</th><th>Copies</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($books as $b): ?>
<tr>
    <td><?= $b['bookID'] ?></td>
    <td><?= htmlspecialchars($b['title']) ?></td>
    <td><?= htmlspecialchars($b['author']) ?></td>
    <td><?= htmlspecialchars($b['category']) ?></td>
    <td><?= $b['yearPublished'] ?></td>
    <td><?= $b['copiesAvailable'] ?></td>
    <td><?= $b['bookStat'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?edit=<?= $b['bookID'] ?>">Edit</a>
        <?php if ($b['bookStat']): ?>
            | <a href="?deactivate=<?= $b['bookID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $b['bookID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
