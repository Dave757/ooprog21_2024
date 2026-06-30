<?php
require 'config/db.php';

if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO members (memberID, memberPass, memberFName, memberLName, contactNo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['memberID'], $_POST['memberPass'], $_POST['memberFName'], $_POST['memberLName'], $_POST['contactNo']]);
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE members SET memberPass=?, memberFName=?, memberLName=?, contactNo=? WHERE memberID=?");
    $stmt->execute([$_POST['memberPass'], $_POST['memberFName'], $_POST['memberLName'], $_POST['contactNo'], $_POST['memberID']]);
}

if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE members SET memberStat=0 WHERE memberID=?");
    $stmt->execute([$_GET['deactivate']]);
}
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE members SET memberStat=1 WHERE memberID=?");
    $stmt->execute([$_GET['activate']]);
}

$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE memberID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$members = $conn->query("SELECT * FROM members")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Members</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Members</h2>

<form method="post">
    Member ID: <input type="text" name="memberID" value="<?= $editRow['memberID'] ?? '' ?>" <?= $editRow ? 'readonly' : 'required' ?>><br><br>
    Password: <input type="text" name="memberPass" value="<?= $editRow['memberPass'] ?? '' ?>" required><br><br>
    First Name: <input type="text" name="memberFName" value="<?= htmlspecialchars($editRow['memberFName'] ?? '') ?>" required><br><br>
    Last Name: <input type="text" name="memberLName" value="<?= htmlspecialchars($editRow['memberLName'] ?? '') ?>" required><br><br>
    Contact No: <input type="text" name="contactNo" value="<?= htmlspecialchars($editRow['contactNo'] ?? '') ?>"><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>Member ID</th><th>Name</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($members as $m): ?>
<tr>
    <td><?= $m['memberID'] ?></td>
    <td><?= htmlspecialchars($m['memberLName'] . ', ' . $m['memberFName']) ?></td>
    <td><?= htmlspecialchars($m['contactNo']) ?></td>
    <td><?= $m['memberStat'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?edit=<?= $m['memberID'] ?>">Edit</a>
        <?php if ($m['memberStat']): ?>
            | <a href="?deactivate=<?= $m['memberID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $m['memberID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
