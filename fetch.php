<html>
<title>PSAW Fetch</title>

<header>
<link rel="icon" href="psaw2.png">
</header>


<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>

<!-- Search keywords form -->
<h2> Which proteins should be fetched from NCBI? </h2>
  <form method="POST">
    <label>Protein Family:</label><br>
    <input type="text" name="protein_family" placeholder="glucose-6-phosphatase" required><br><br>

    <label>Taxonomic Group:</label><br>
    <input type="text" name="taxonomic_group" placeholder="Aves" required><br><br>

    <input type="submit" name="submit" value="Fetch Proteins">
  </form>


<!-- Fetch and store the requested search results -->
<?php
if (isset($_POST['submit'])) {
    $protein_family = htmlspecialchars($_POST['protein_family']);
    $taxonomic_group = htmlspecialchars($_POST['taxonomic_group']);
    $NCBI_key = 'cc896afc644679cb9d9201d3b4bc9414cb08';

    echo '<p>You have chosen to fetch ' . $protein_family . ' ' . $taxonomic_group . ' proteins!</p>';

    // Set up a PDO connection to the PSAW mySQL database: $conn
    include 'sql_connect.php';

    // Prepare NCBI esearch query
    $query = urlencode("$protein_family [Protein Name] AND $taxonomic_group [Organism]");
    $esearchURL = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=protein&term=$query&retmax=10&usehistory=y&api_key=$NCBI_key";

    $searchResult = file_get_contents($esearchURL);
    preg_match("/<WebEnv>(\S+)<\/WebEnv>/", $searchResult, $webEnv); //get easearch session data
    preg_match("/<QueryKey>(\d+)<\/QueryKey>/", $searchResult, $queryKey); //get query tracker for the session

    if (!$webEnv || !$queryKey) { //if any of the session/query trackers are missing/empty, something went wrong
        echo '<p>No results found or error in search.</p>';
        exit;
    }

    // Fetch the queried sequences in FASTA format
    $efetchURL = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' .
                 "db=protein&query_key={$queryKey[1]}&WebEnv={$webEnv[1]}&rettype=fasta&retmode=text&api_key=$NCBI_key";

    $fasta_fetched = file_get_contents($efetchURL);
    $entries = preg_split("/^>/m", $fasta_fetched, -1, PREG_SPLIT_NO_EMPTY); //separated all entries with a line starting with >, no limit on match number, exclude empty strings

    //Insert data into the mySQL database
        //use name placeholders to prepare the query early
    $sql = "INSERT INTO protein_sequences
        (accession, description, organism, sequence, protein_family, taxonomic_group) 
        VALUES (:accession, :description, :organism, :sequence, :protein_family, :taxonomic_group)
        ON DUPLICATE KEY UPDATE 
        description = VALUES(description), 
        organism = VALUES(organism),
        sequence = VALUES(sequence),
        protein_family = VALUES(protein_family),
        taxonomic_group = VALUES(taxonomic_group)";

    $stmt = $conn->prepare($sql);
    $inserted = 0;

    foreach ($entries as $entry) {
        $lines = explode("\n", $entry);
        $header = array_shift($lines);
        $sequence = implode('', $lines);

        if (preg_match("/^(\S+)\s+(.*)\[([^\]]+)\]/", $header, $matches)) { //check for three matched groups in the header: the accession text, the description, and the organism name between square brackets
            $accession = $matches[1];
            $description = $matches[2];
            $organism = $matches[3];

            $stmt->execute([
                ':accession' => $accession,
                ':description' => $description,
                ':organism' => $organism,
                ':sequence' => $sequence,
                ':protein_family' => $protein_family,
                ':taxonomic_group' => $taxonomic_group
            ]);

            $inserted++;
        }
    }

    echo "<p>Protein sequences fetched and $inserted stored successfully!</p>"; 
    echo "<p>For example, here are the first three rows of your query:</p>";
    
    // Display first few rows of the results
    $sql = "SELECT * FROM protein_sequences WHERE protein_family = :protein_family AND taxonomic_group = :taxonomic_group limit 3";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':protein_family' => $protein_family, 'taxonomic_group' => $taxonomic_group]);

    echo "<br>";
    echo "<table border='1'>";
    $first = true; //used to print headers before the first row
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($first) {
            echo "<tr>";
            foreach (array_keys($row) as $col) {
                echo "<th>" . htmlspecialchars($col) . "</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
echo "</table>";
$conn = null; //close connection
}
?>

</body>

<html>
