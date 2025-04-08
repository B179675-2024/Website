<html>
<title>PSAW Motifs</title>
<head>
<link rel="icon" href="psaw2.png">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>
<h1><a href="https://prosite.expasy.org/">PROSITE</a> motif searching</h1>


<?php
if (!isset($_POST['proset'])) die("No combination selected.");

list($protein_family, $taxonomic_group) = explode('|', $_POST['proset']);

include 'sql_connect.php'; //create pdo connection to the mysql database ($conn)

// Fetch protein selection from database
$stmt = $conn->prepare("SELECT accession, sequence FROM protein_sequences WHERE protein_family = :protein_family AND taxonomic_group = :taxonomic_group");
$stmt->execute([':protein_family' => $protein_family, ':taxonomic_group' => $taxonomic_group]);
$sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($sequences) === 0) {
    die("No sequences found for that combination.");
}

//// Write sequences to FASTA files in the data directory and generate patmatmotifs output for each selected sequence
chdir('data');
$fastapath = "_motseq.fa";
$motifpath = "_patmotifs.txt";
$motifs=[]; //initiate motif array to store motif results while parsing patmatmotif outputs later

//Quick clean-up of the data directory for relevant files
$toclean = ["$fastapath","$motifpath"];
foreach ($toclean as $tc) {
    foreach (glob("*$tc") as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

//FASTA and motif file creation, followed by motif parsing 
foreach ($sequences as $row) {
    $fasta = '';
    $accession = trim($row['accession']);
    $sequence = $row['sequence'];
    $fasta .= ">{$accession}\r\n";
    $fasta .= chunk_split($sequence, 60, "\r\n");

    $fastafile = "$accession" . "$fastapath";
    $motiffile = "$accession" . "$motifpath";
    file_put_contents($fastafile, $fasta);
    shell_exec("patmatmotifs -sequence $fastafile -outfile $motiffile -rformat2 simple -raccshow2 Y -full Y -auto");

    // Parse result
    $lines = file($motiffile);
    $start = $end = $motif = null; //initialise variables that capture key motif output from the files

    foreach ($lines as $line) {
        if (preg_match('/^Start:\s+(\d+)/', $line, $m)) $start = $m[1];
        if (preg_match('/^End:\s+(\d+)/', $line, $m)) $end = $m[1];
        if (preg_match('/^Motif:\s+(\S+)/', $line, $m)) $motif = $m[1];

        if ($start && $end && $motif) {
            $motifs[] = [
                'accession' => $accession,
                'start' => $start,
                'end' => $end,
                'motif' => $motif
            ];
            $start = $end = $motif = null; //once the output set for one motif has been collected, reset
        }
    }
}
$conn = null;
?>

<h2>PROSITE Motif Results for <?= htmlspecialchars($protein_family) ?> - <?= htmlspecialchars($taxonomic_group) ?></h2>
    <?php if (empty($motifs)): ?>
        <p>No motifs found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>NCBI Accession</th>
                <th>Start</th>
                <th>End</th>
                <th>Motif</th>
            </tr>
            <?php foreach ($motifs as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['accession']) ?></td>
                    <td><?= htmlspecialchars($m['start']) ?></td>
                    <td><?= htmlspecialchars($m['end']) ?></td>
                    <td><?= htmlspecialchars($m['motif']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<p><a href="motifs.php"> Back</a></p>
</body>
</html>
