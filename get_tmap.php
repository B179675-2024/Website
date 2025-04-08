<html>
<title>PSAW T-mapping</title>

<header>
<link rel="icon" href="psaw2.png">
<link rel="stylesheet" href="styles.css">
</header>
<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>

<h1>Transmembrane segment <a href="https://pubmed.ncbi.nlm.nih.gov/8126732/">prediction</a></h1>

<?php
if (!isset($_POST['proset'])) die("No combination selected.");

list($protein_family, $taxonomic_group) = explode('|', $_POST['proset']);


// Create a connection to the mysql database ($conn)
include 'sql_connect.php';

// Fetch sequences from the database
$stmt = $conn->prepare("SELECT accession, sequence FROM protein_sequences WHERE protein_family = :protein_family and taxonomic_group = :taxonomic_group");
$stmt->execute([':protein_family' => $protein_family, ':taxonomic_group' => $taxonomic_group]);
$sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Put extracted sequences in FASTA format
$fasta = '';
foreach ($sequences as $seq) {
    $accession = trim($seq['accession']); 
    $sequence = $seq['sequence'];
    $fasta .= ">{$accession}\r\n";
    $fasta .= chunk_split($sequence, 60, "\r\n");
}

//change directory to the data directory for safe code execution and data handling
chdir('data');

// Temporary files
$seqfile = "seq.fa";
$alignedfile = "aligned_seq.fa";
$dendrofile = "seq.dnd";
$tmapreport = "tmapped_aligned_seq.tmap";
$tmapgraph = "tmap.1.png";

// Clean up temporary files
foreach ([$seqfile, $alignedfile, $dendrofile, $tmapreport, $tmapgraph] as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

// Write sequence FASTA file
file_put_contents($seqfile, $fasta);
$conn = null; //connection can be closed now

// Run EMBOSS emma on auto mode (Multiple Sequence Alignment)
$emma_output = shell_exec("emma -sequence $seqfile -outseq $alignedfile -dendoutfile $dendrofile -auto 2>&1");

if (!file_exists($alignedfile) || filesize($alignedfile) === 0) {
    echo "<p><strong>EMMA failed or produced empty output:</strong></p>";
    echo "<pre>" . htmlspecialchars($emma_output) . "</pre>";
    exit;
}

// Run EMBOSS tmap on the aligned sequence set
$tmap_output = shell_exec("tmap -sequences $alignedfile -graph png -outfile $tmapreport -auto");

// Parse the tmap report to extract the consensus summary

$tmapreport_content = file_get_contents($tmapreport);
$lines = explode("\n", $tmapreport_content);

$parse_token = false; //decides the window to collect the tmap summary data
$consensus = []; //array for the summary

foreach ($lines as $line) {
    // Begin parsing when the consensus summary is found
    if (preg_match('/^#\s*Sequence:\s*Consensus\b/', $line)) {
        $parse_token = true;
        continue;
    }

    // Stop parsing when we the next sequence summary is reached
    if ($parse_token && preg_match('/^#\s*Sequence:\s*(?!Consensus\b)/', $line)) {
        //$parse_token = false;
        break;
    }

    if ($parse_token) {
        // Match MSA summary lines: Start, End, TransMem, Sequence
        if (preg_match('/^\s*(\d+)\s+(\d+)\s+(\d+)\s+([^\s]+)/', $line, $matches)) {
            $consensus []= [
                'start' => $matches[1],
                'end' => $matches[2],
                'transmem' => $matches[3],
                'sequence' => $matches[4],
            ];
        }
    }
}

?>


<!-- RELAY OUTPUT -->
<h2><u>Transmembrane segment prediction for <?= htmlspecialchars($protein_family) ?> - <?= htmlspecialchars($taxonomic_group) ?></u></h2>

<h2>Transmembrane segments predicted in the MSA consensus</h2>

<table>
    <thead>
        <tr>
            <th>Start</th>
            <th>End</th>
            <th>Predicted t-segment #</th>
            <th>Sequence</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($consensus as $data): ?>
            <tr>
                <td><?php echo htmlspecialchars($data['start']); ?></td>
                <td><?php echo htmlspecialchars($data['end']); ?></td>
                <td><?php echo htmlspecialchars($data['transmem']); ?></td>
                <td><?php echo htmlspecialchars($data['sequence']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
echo "<h2>Tmap graph</h2>";

if (file_exists($tmapgraph)) {
    $graph_b64 = base64_encode(file_get_contents($tmapgraph));
    echo "<img src='data:image/png;base64,{$graph_b64}' alt='Tmap Graph'>";
} else {
    echo "<p><strong>Error:</strong> Tmap image missing.</p>";
}
?>

<p><a href="tmap.php"> Back</a></p>

</body>

</html>
