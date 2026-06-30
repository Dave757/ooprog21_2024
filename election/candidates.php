<?php
require 'config/db.php';

if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO candidates (candFName, candMName, candLName, posID) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['candFName'], $_POST['candMName'], $_POST['candLName'], $_POST['posID']]);
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE candidates SET candFName=?, candMName=?, candLName=?, posID=? WHERE candID=?");
    $stmt->execute([$_POST['candFName'], $_POST['candMName'], $_POST['candLName'], $_POST['posID'], $_POST['candID']]);
}

if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE candidates SET candStat=0 WHERE candID=?");
    $stmt->execute([$_GET['deactivate']]);
}
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE candidates SET candStat=1 WHERE candID=?");
    $stmt->execute([$_GET['activate']]);
}

$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE candID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$positions = $conn->query("SELECT * FROM positions WHERE posStat=1")->fetchAll(PDO::FETCH_ASSOC);

$candidates = $conn->query("
    SELECT c.*, p.posName FROM candidates c
    JOIN positions p ON c.posID = p.posID
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Candidates</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Candidates</h2>

<form method="post">
    <input type="hidden" name="candID" value="<?= $editRow['candID'] ?? '' ?>">
    First Name: <input type="text" name="candFName" value="<?= $editRow['candFName'] ?? '' ?>" required><br><br>
    Middle Name: <input type="text" name="candMName" value="<?= $editRow['candMName'] ?? '' ?>"><br><br>
    Last Name: <input type="text" name="candLName" value="<?= $editRow['candLName'] ?? '' ?>" required><br><br>
    Position:
    <select name="posID" required>
        <option value="">-- Select --</option>
        <?php foreach ($positions as $p): ?>
            <option value="<?= $p['posID'] ?>" <?= (isset($editRow) && $editRow['posID'] == $p['posID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['posName']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Position</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($candidates as $c): ?>
<tr>
    <td><?= $c['candID'] ?></td>
    <td><?= htmlspecialchars($c['candLName'] . ', ' . $c['candFName'] . ' ' . $c['candMName']) ?></td>
    <td><?= htmlspecialchars($c['posName']) ?></td>
    <td><?= $c['candStat'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?edit=<?= $c['candID'] ?>">Edit</a>
        <?php if ($c['candStat']): ?>
            | <a href="?deactivate=<?= $c['candID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $c['candID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
