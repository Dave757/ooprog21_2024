<?php
require 'config/db.php';

// ADD
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO positions (posName, numOfPositions) VALUES (?, ?)");
    $stmt->execute([$_POST['posName'], $_POST['numOfPositions']]);
}

// UPDATE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE positions SET posName=?, numOfPositions=? WHERE posID=?");
    $stmt->execute([$_POST['posName'], $_POST['numOfPositions'], $_POST['posID']]);
}

// DEACTIVATE
if (isset($_GET['deactivate'])) {
    $stmt = $conn->prepare("UPDATE positions SET posStat=0 WHERE posID=?");
    $stmt->execute([$_GET['deactivate']]);
}

// ACTIVATE (optional convenience)
if (isset($_GET['activate'])) {
    $stmt = $conn->prepare("UPDATE positions SET posStat=1 WHERE posID=?");
    $stmt->execute([$_GET['activate']]);
}

// Load record for editing
$editRow = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM positions WHERE posID=?");
    $stmt->execute([$_GET['edit']]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
}

$positions = $conn->query("SELECT * FROM positions")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Manage Positions</title></head>
<body>
<a href="index.php">&laquo; Back to Menu</a>
<h2>Manage Positions</h2>

<form method="post">
    <input type="hidden" name="posID" value="<?= $editRow['posID'] ?? '' ?>">
    Position Name: <input type="text" name="posName" value="<?= $editRow['posName'] ?? '' ?>" required><br><br>
    Number of Positions: <input type="number" name="numOfPositions" value="<?= $editRow['numOfPositions'] ?? '' ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="5">
<tr><th>ID</th><th>Position</th><th>Slots</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($positions as $p): ?>
<tr>
    <td><?= $p['posID'] ?></td>
    <td><?= htmlspecialchars($p['posName']) ?></td>
    <td><?= $p['numOfPositions'] ?></td>
    <td><?= $p['posStat'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?edit=<?= $p['posID'] ?>">Edit</a>
        <?php if ($p['posStat']): ?>
            | <a href="?deactivate=<?= $p['posID'] ?>">Deactivate</a>
        <?php else: ?>
            | <a href="?activate=<?= $p['posID'] ?>">Activate</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
