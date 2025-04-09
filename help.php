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

<h1>Help</h1>
<h2>Fetch</h2>
<p>
You can request sequences for a protein family in a taxonomic group of your choosing. These two categories will be submitted to NCBI as [Protein Name] 
and [Organism] query fields, which tend to be strict. If NCBI understands your query and finds matching entries, they will be stored in our server's database and become available for analysis in the other pages.
</p>

<p>
<b>Note about the current implementation:</b> 
If a search returns an already existing accession, it will update the existing entry with the latest query terms used that returned the accession 
to limit data bloat. This presents the risk of having certain entries excluded from protein sets imported in the past. For that reason, we recommend using the fetching page to refresh existing protein sets if you have any doubts about their completeness before/after analysis.
</p>
<h2>Conservation</h2>
<p>
To get started, select one of the available protein sets and press <mark>Run conservation analysis</mark>.
</p>

<p>
After launching the conservation analysis, the will produce a multiple sequence alignment with the EMBOSS emma tool (based on CLUSTALW). You can press a toggle to view the MSA. 
</p>

<p>
The MSA is used to generate a conservation plot for your selected sequences with EMBOSS plotcon. The conservation plot shows average sequence similarity within a certain window of amino-acids (10 by default here), along the length of the alignment. 
Sequence similarity at a given position here refers to the average of the pairwise subsitution scores (matrix: BLOSUM62) for that position in the alignment. 
Although it can be difficult to directly interpret similarity scores in the plot due to the dependence on the sliding window size, the plot easily highlights regions with relatively high conservation in the sequence alignment.
To get more from the significance of the similarity scores, details are available in the <a href="https://emboss.sourceforge.net/apps/release/6.6/emboss/apps/plotcon.html">plotcon documentation</a>.
</p>

<h2>Motifs</h2>
<p>
To get started, select one of the available protein sets and press <mark>Run motif search</mark>.
</p>

<p>
All sequences from your protein set will be scanned for known motifs in our PROSITE installation (using EMBOSS patmatmotifs). The page displays the results in a table listing sequence accessions followed by the motif names and their location in the sequence.
</p>

<h2>Transmembrane domains</h2>
<p>
To get started, select one of the available protein sets and press <mark>Run t-segment production</mark>.
</p>

<p>
This component will then run EMBOSS tmap on the multiple sequence alignment of your selection. Two results are displayed: a table listing the predicted transmembrane segments in the alignment's consensus sequence, and a plot of the propensities (~likelihoods) used to predict the segments over the sequences.
</p>

<p>
Black bars may be displayed above the line plots to highlight predicted segments, but in case of their absences, you can look for peaks in the graphs that correspond to the predictions, if they are confident enough.
</p>

</body>
</html>
