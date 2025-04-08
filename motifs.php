<html>
<title>PSAW Motifs</title>

<header>
<link rel="icon" href="psaw2.png">
<link rel="stylesheet" href="styles.css">
</header>
<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>
<h1><a href="https://prosite.expasy.org/">PROSITE</a> motif searching</h1>

<?php
include 'sql_connect.php'; //Create a mySQL connection to the database ($conn)

// Get unique sequence queries from fetched data
$stmt = $conn->query("SELECT DISTINCT protein_family, taxonomic_group FROM protein_sequences ORDER BY protein_family, taxonomic_group");
$combinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Select a protein sequence set for motif searching</h2>
<form method="POST" action="get_motifs.php">
    <label for="proset">Sequence sets:</label>
    <select name="proset" id="proset">
        <?php foreach ($combinations as $row):
                $fam = htmlspecialchars($row['protein_family']);
                $tax = htmlspecialchars($row['taxonomic_group']);
                $value = "$fam|$tax";
            ?>
            <option value="<?= $value ?>"><?= "$fam FROM $tax" ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Run motif search</button>
</form>

<?php $conn = null; ?>
</body>
</html>
