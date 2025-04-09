<html>
<title>PSAW</title>

<header>
<link rel="icon" href="psaw2.png">
<link rel="stylesheet" href="styles.css">
</header>
<body>

<!-- Display Menu -->
<div class="menu">
<?php include 'menu.php';?>
</div>

<h1>About</h1>
<h2>General</h2>
<p>PSAW is built on PHP 8.</p>
<p>This website is based on a series of php pages linked together by a menu (<code>menu.php</code>). All pages use a common CSS styling sheet that changes table formats. Analysis pages that deal with sequence data include a PDO connection to our database defined in a separate php file.

<p>Code for the PSAW website can be found in <a href="https://github.com/B179675-2024/Website">this GitHub repository</a>.</p>

<h2>mySQL</h2>
<p>
The mySQL database is defined with the following statement:
</p>

<pre>
CREATE TABLE protein_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accession VARCHAR(50) UNIQUE,
    description TEXT,
    organism VARCHAR(100),
    sequence TEXT,
    protein_family VARCHAR(100),
    taxonomic_group VARCHAR(100)
);
</pre>

<h2>Index, Help, About, Credits</h2>
<p>
These text-focused pages follow a simple html implementation using tags to format the text. They were made as php pages to include the php-based menu.
</p>

<h2>Fetch</h2>

<p>
Fetch uses a POST form to require input from the user define a set of protein sequences to be fetched from NCBI. 
Upon submission, the page begins the fetching process (php code). The query terms are URL-encoded and included in an esearch https request with retmax=10 and usehistory=y. Keeping the use history allows 
the page to then send a efetch https request to store the search results as they were stored on NCBI's servers thanks to the WebEnv and QueryKey values indicated in the esearch results. The results are parsed and stored in the sql database, with a counter keeping track of how many sequences get inserted in the database (if any sequence exists already, the existing entry gets updated with the query terms used).
</p>

<h2>Analysis pages</h2>
<p>
All analysis pages (conservation, motifs, t-mapping) follow a similar framework. Upon entering a page, the user gets greeted with a POST form drop-down menu listing the protein combinations available (enabled by a mySQL query). 
After making a choice, the user can start the analysis process, which will send the user to a secondary page <i>via</i> the 'action' form attribute (e.g. going from analysis.php to get_analysis.php). 
These pages perform the analysis expected from the corresponding pages on the selected protein set (using php code and bash commands calling relevant EMBOSS software). Any analysis results are stored in temporary files which are respectively cleaned before new relevant analyses. 
Each page will extract requested accession and sequences from the database and parse them to be stored in fasta files according to the input requirements of downstream analyses. 
Downstream analyses are performed, relevant text-based output is parsed and reported to the user, .png plots are encoded in base64 and displayed. 
All "get_" pages feature an extra "Back" button at the bottom to take the user back to the corresponding protein selection page. 
The following sections discuss these in more detail.
</p>

<h3>Conservation</h3>
<p>
Protein sequences are gathered in a single fasta file for multiple sequence alignment by EMBOSS emma. The MSA is relayed back to the user as an html 'div' html object containing php code to display the file's contents. As these files can be very long, a JavaScript function defined in the header 
is called in a button on click to toggle the visibility of the MSA (hidden by default in the style attribute of the 'div' tag). The plotcon graph is displayed below.
</p>

<h3>Motifs</h3>
<p>
The patmatmotifs tool used to find motifs requires single sequence files, so the sequences are parsed and output to separate files named after each accession called. Likewise, patmatmotifs is called to generate output (in 'simple' report format) for all files, generating an equal number of report files. These are parsed in a loop to extract the relevant motif information in an associative array that will be presented in a table for the user. 
</p>

<h3>Transmembrane domains</h3>
<p>
Protein sequences are treated the same way as in conservation for a multiple sequence alignment. The MSA is given to EMBOSS tmap to produce a text report and a png file. 
As the tmap report contains predicted t-segments for the consensus alignment sequence followed by all individual sequences, the report is parsed to report in a table the summary for the consensus only (for the sake of conciseness). The plot is displayed below so the user can compare predicted consensus transmembrane segments with the plot.
</p>
</body>
</html>
