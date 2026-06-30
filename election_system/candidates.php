<?php
include 'config.php';

// Add candidate
if (isset($_POST['add'])) {
    $candFName = mysqli_real_escape_string($conn, $_POST['candFName']);
    $candMName = mysqli_real_escape_string($conn, $_POST['candMName']);
    $candLName = mysqli_real_escape_string($conn, $_POST['candLName']);
    $posID = (int)$_POST['posID'];
    $sql = "INSERT INTO candidates (candFName, candMName, candLName, posID, candStat) VALUES ('$candFName', '$candMName', '$candLName', $posID, 1)";
    mysqli_query($conn, $sql);
    header("Location: candidates.php");
    exit();
}

// Update candidate
if (isset($_POST['update'])) {
    $candID = (int)$_POST['candID'];
    $candFName = mysqli_real_escape_string($conn, $_POST['candFName']);
    $candMName = mysqli_real_escape_string($conn, $_POST['candMName']);
    $candLName = mysqli_real_escape_string($conn, $_POST['candLName']);
    $posID = (int)$_POST['posID'];
    $sql = "UPDATE candidates SET candFName='$candFName', candMName='$candMName', candLName='$candLName', posID=$posID WHERE candID=$candID";
    mysqli_query($conn, $sql);
    header("Location: candidates.php");
    exit();
}

// Deactivate candidate
if (isset($_GET['deactivate'])) {
    $candID = (int)$_GET['deactivate'];
    mysqli_query($conn, "UPDATE candidates SET candStat=0 WHERE candID=$candID");
    header("Location: candidates.php");
    exit();
}

// Activate candidate
if (isset($_GET['activate'])) {
    $candID = (int)$_GET['activate'];
    mysqli_query($conn, "UPDATE candidates SET candStat=1 WHERE candID=$candID");
    header("Location: candidates.php");
    exit();
}

// Load record for editing
$editRow = null;
if (isset($_GET['edit'])) {
    $candID = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM candidates WHERE candID=$candID");
    $editRow = mysqli_fetch_assoc($result);
}

$positions = mysqli_query($conn, "SELECT * FROM positions WHERE posStat=1 ORDER BY posName");

$candidates = mysqli_query($conn, "SELECT c.*, p.posName FROM candidates c
                                    JOIN positions p ON c.posID = p.posID
                                    ORDER BY c.candID");
?>
<!DOCTYPE html>
<html>
<head>
<title>Candidates Management</title>
</head>
<body>

<p><a href="index.php">Back to Menu</a></p>
<h2>Candidates Management</h2>

<h3><?php echo $editRow ? "Update Candidate" : "Add Candidate"; ?></h3>
<form method="post" action="candidates.php">
    <?php if ($editRow): ?>
        <input type="hidden" name="candID" value="<?php echo $editRow['candID']; ?>">
    <?php endif; ?>
    First Name: <input type="text" name="candFName" value="<?php echo $editRow ? htmlspecialchars($editRow['candFName']) : ''; ?>" required><br><br>
    Middle Name: <input type="text" name="candMName" value="<?php echo $editRow ? htmlspecialchars($editRow['candMName']) : ''; ?>"><br><br>
    Last Name: <input type="text" name="candLName" value="<?php echo $editRow ? htmlspecialchars($editRow['candLName']) : ''; ?>" required><br><br>
    Position:
    <select name="posID" required>
        <option value="">-- Select Position --</option>
        <?php
        mysqli_data_seek($positions, 0);
        while ($p = mysqli_fetch_assoc($positions)):
        ?>
        <option value="<?php echo $p['posID']; ?>" <?php echo ($editRow && $editRow['posID'] == $p['posID']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($p['posName']); ?>
        </option>
        <?php endwhile; ?>
    </select><br><br>
    <?php if ($editRow): ?>
        <button type="submit" name="update">Update</button>
        <a href="candidates.php">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>
</form>

<h3>Candidates List</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Position</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($candidates)): ?>
    <tr>
        <td><?php echo $row['candID']; ?></td>
        <td><?php echo htmlspecialchars($row['candFName'] . ' ' . $row['candMName'] . ' ' . $row['candLName']); ?></td>
        <td><?php echo htmlspecialchars($row['posName']); ?></td>
        <td><?php echo $row['candStat'] ? 'Active' : 'Inactive'; ?></td>
        <td>
            <a href="candidates.php?edit=<?php echo $row['candID']; ?>">Edit</a>
            <?php if ($row['candStat']): ?>
                | <a href="candidates.php?deactivate=<?php echo $row['candID']; ?>" onclick="return confirm('Deactivate this candidate?')">Deactivate</a>
            <?php else: ?>
                | <a href="candidates.php?activate=<?php echo $row['candID']; ?>">Activate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
