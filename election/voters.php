<?php
require 'config/db.php';

if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO voters (voterID, voterPass, voterFName, voterMName, voterLName) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['voterID'], $_POST['voterPass'], $_POST['voterFName'], $_POST['voterMName'], $_POST['voterLName']]);
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE voters SET voterPass=?, voterFName=?, voterMName=?, voterLName=? WHERE voterID=?");
    $stmt->execute([$_POST['voterPass'], $_POST['voterFName'], $_POST['voterMName'], $_POST['voterLName'], $_POST['voterID']]);
}

if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE voters SET voterStat=0 WHERE voterID=?");
    $stmt->execute([$_GET['deactivate']]);
}
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE voters SET voterStat=1 WHERE voterID=?");
    $stmt->execute([$_GET['activate']]);
}

$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voterID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$voters = $conn->query("SELECT * FROM voters")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Voters</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Voters</h2>

<form method="post">
    Voter ID: <input type="text" name="voterID" value="<?= $editRow['voterID'] ?? '' ?>" <?= $editRow ? 'readonly' : 'required' ?>><br><br>
    Password: <input type="text" name="voterPass" value="<?= $editRow['voterPass'] ?? '' ?>" required><br><br>
    First Name: <input type="text" name="voterFName" value="<?= $editRow['voterFName'] ?? '' ?>" required><br><br>
    Middle Name: <input type="text" name="voterMName" value="<?= $editRow['voterMName'] ?? '' ?>"><br><br>
    Last Name: <input type="text" name="voterLName" value="<?= $editRow['voterLName'] ?? '' ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>Voter ID</th><th>Name</th><th>Status</th><th>Voted</th><th>Actions</th></tr>
<?php foreach ($voters as $v): ?>
<tr>
    <td><?= $v['voterID'] ?></td>
    <td><?= htmlspecialchars($v['voterLName'] . ', ' . $v['voterFName'] . ' ' . $v['voterMName']) ?></td>
    <td><?= $v['voterStat'] ? 'Active' : 'Inactive' ?></td>
    <td><?= $v['voted'] ? 'Yes' : 'No' ?></td>
    <td>
        <a href="?edit=<?= $v['voterID'] ?>">Edit</a>
        <?php if ($v['voterStat']): ?>
            | <a href="?deactivate=<?= $v['voterID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $v['voterID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
