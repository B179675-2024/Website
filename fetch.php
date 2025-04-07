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

    echo 'You have chosen to fetch $protein_family $taxonomic_group proteins!';

    // Set up a PDO connection to the PSAW mySQL database: $pdo
    include 'sql_connect.php';

    // Prepare NCBI esearch query
    $query = urlencode("$protein_family[Protein Name] AND $taxonomic_group[Organism]");
    $esearchURL = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=protein&term=$query&retmax=10&usehistory=y&api_key=$NCBI_key";

    $searchResult = file_get_contents($esearchURL);
    preg_match('/<WebEnv>(\S+)<\/WebEnv>/', $searchResult, $webEnv); //get easearch session data
    preg_match('/<QueryKey>(\d+)<\/QueryKey>/', $searchResult, $queryKey); //get query tracker for the session

    if (!$webEnv || !$queryKey) { //if any of the session/query trackers are missing/empty, something went wrong
        echo "<p>No results found or error in search.</p>";
        exit;
    }
}
?>
</body>
<html>
