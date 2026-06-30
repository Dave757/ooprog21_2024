<?php
include 'config.php';

// Add position
if (isset($_POST['add'])) {
    $posName = mysqli_real_escape_string($conn, $_POST['posName']);
    $numOfPositions = (int)$_POST['numOfPositions'];
    $sql = "INSERT INTO positions (posName, numOfPositions, posStat) VALUES ('$posName', $numOfPositions, 1)";
    mysqli_query($conn, $sql);
    header("Location: positions.php");
    exit();
}

// Update position
if (isset($_POST['update'])) {
    $posID = (int)$_POST['posID'];
    $posName = mysqli_real_escape_string($conn, $_POST['posName']);
    $numOfPositions = (int)$_POST['numOfPositions'];
    $sql = "UPDATE positions SET posName='$posName', numOfPositions=$numOfPositions WHERE posID=$posID";
    mysqli_query($conn, $sql);
    header("Location: positions.php");
    exit();
}

// Deactivate position
if (isset($_GET['deactivate'])) {
    $posID = (int)$_GET['deactivate'];
    mysqli_query($conn, "UPDATE positions SET posStat=0 WHERE posID=$posID");
    header("Location: positions.php");
    exit();
}

// Activate position
if (isset($_GET['activate'])) {
    $posID = (int)$_GET['activate'];
    mysqli_query($conn, "UPDATE positions SET posStat=1 WHERE posID=$posID");
    header("Location: positions.php");
    exit();
}

// Load record for editing
$editRow = null;
if (isset($_GET['edit'])) {
    $posID = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM positions WHERE posID=$posID");
    $editRow = mysqli_fetch_assoc($result);
}

$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY posID");
?>
<!DOCTYPE html>
<html>
<head>
<title>Positions Management</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Positions Management</h2>

<h3><?php echo $editRow ? "Update Position" : "Add Position"; ?></h3>
<form method="post" action="positions.php">
    <?php if ($editRow): ?>
        <input type="hidden" name="posID" value="<?php echo $editRow['posID']; ?>">
    <?php endif; ?>
    Position Name: <input type="text" name="posName" value="<?php echo $editRow ? htmlspecialchars($editRow['posName']) : ''; ?>" required><br><br>
    Number of Positions: <input type="number" name="numOfPositions" value="<?php echo $editRow ? $editRow['numOfPositions'] : ''; ?>" required><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
        <a href="positions.php">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<h3>Positions List</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Position Name</th>
        <th>No. of Positions</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($positions)): ?>
    <tr>
        <td><?php echo $row['posID']; ?></td>
        <td><?php echo htmlspecialchars($row['posName']); ?></td>
        <td><?php echo $row['numOfPositions']; ?></td>
        <td><?php echo $row['posStat'] ? 'Active' : 'Inactive'; ?></td>
        <td>
            <a href="positions.php?edit=<?php echo $row['posID']; ?>">Edit</a>
            <?php if ($row['posStat']): ?>
                | <a href="positions.php?deactivate=<?php echo $row['posID']; ?>" onclick="return confirm('Deactivate this position?')">Deactivate</a>
            <?php else: ?>
                | <a href="positions.php?activate=<?php echo $row['posID']; ?>">Activate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
