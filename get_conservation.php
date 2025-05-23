<html>
<title>PSAW Conservation</title>

<header>
<link rel="icon" href="psaw2.png">
<link rel="stylesheet" href="styles.css">
<script>
    // MSA visibility toggle function
    function toggleMSA() {
        var msaContainer = document.getElementById("msaContainer");
        if (msaContainer.style.display === "none") {
            msaContainer.style.display = "block";  // Show the MSA
        } else {
            msaContainer.style.display = "none";   // Hide the MSA
        }
    }
</script>
</header>
<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>

<h1>Conservation analysis (Multiple sequence alignment and conservation plotting)</h1>

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
//echo "<pre>$fasta</pre>";

//change directory to the data directory for safe code execution and data handling
chdir('data');

// Temporary files
$seqfile = "seq.fa";
$alignedfile = "aligned_seq.fa";
$dendrofile = "seq.dnd";
$plotfile = "conservation.1.png";

// Clean up temporary files
foreach ([$seqfile, $alignedfile, $dendrofile, $plotfile] as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

$plotfile = "conservation";

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

// Run EMBOSS plotcon, saving a png output (conservation plot)
shell_exec("plotcon -winsize 10 -scorefile EBLOSUM62 -sequence $alignedfile -graph png -goutfile $plotfile -auto");
?>


<!-- RELAY OUTPUT -->
<h2><u>Conservation Results for <?= htmlspecialchars($protein_family) ?> - <?= htmlspecialchars($taxonomic_group) ?></u></h2>
<h2>Multiple Sequence Alignment</h2>
<!-- Visibility toggle button -->
<button onclick="toggleMSA()">Show/Hide MSA</button>

<!-- MSA -->
<div id="msaContainer" style="display:none;">
    <pre><?php
if (file_exists($alignedfile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($alignedfile)) . "</pre>";
} else {
    echo "<p><strong>Error:</strong> EMMA alignment missing.</p>";
}
    ?></pre> <!-- Output MSA here -->
</div>

<!-- Plot -->
<?php
$plotfile="conservation.1.png";
echo "<h2>Conservation Plot</h2>";

if (file_exists($plotfile)) {
    $plot_b64 = base64_encode(file_get_contents($plotfile));
    echo "<img src='data:image/png;base64,{$plot_b64}' alt='Conservation Plot'>";
} else {
    echo "<p><strong>Error:</strong> PlotCon image missing.</p>";
}
?>

<p><a href="conservation.php"> Back</a></p>
</body>

</html>
