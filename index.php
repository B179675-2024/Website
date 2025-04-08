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

<!-- Introductory text -->
<h1>
Welcome to our Protein Sequence Analysis Website! (PSAW)
</h1>

<p>
You are now on the PSAW index page, where you have access to the PSAW menu leading you to the different pages of the website, as well as
a quick descrition of what each page is there for! So sit back and relax, have a sip of your favourite drink...
It's time to investigate protein sequences!
</p>

<!-- Contents -->
<h2>
Contents
</h2>

<ul>
 <li><mark><b>Index:</b></mark> You are here now! Describes the different webpages available at PSAW.</li>
 <li><mark><b>Fetch Sequences:</b></mark> This page lets you fetch from NCBI and store on our database protein sequences from a protein family and taxonomic group of your choosing (e.g., glucose-6-phosphate and Aves). Be careful with your wording! You will be giving [Protein Name] and [Organism] NCBI query terms, which can be quite strict. If a search nets you 0 results, make sure to double-check your search terms!</li>
 <li><mark><b>Conservation:</b></mark> This page allows you to do a multiple sequence alignment of a protein set of your choosing from our database. You can opt in to show the full MSA, or ignore the MSA and enjoy a conservation plot generated thanks to it!</li>
 <li><mark><b>Motifs:</b></mark> This page allows you to choose a protein set available on our database to search for PROSITE motifs within the set.</li>
 <li><mark><b>Transmembrane domains:</b></mark> This page allows you to choose one of the available protein sets on our database to predict transmembrane segments from the set's sequence alignment (you don't need to visit <b>Conservation</b> before trying this out).</li>
 <li><mark><b>Help:</b></mark> Go here if you would like more details on the meaning of each page's content.</li>
 <li><mark><b>About:</b></mark> Go there if you are looking for technical details regarding the concept of this website, page by page!</li>
 <li><mark><b>Statement of Credits:</b></mark> The author of this website is by no means a master web-developer (yet)! Therefore a lot of the code used to build PSAW relied on information or code from external resources, as well as AI co-piloting. This page is there to share all these references used in the development of PSAW.</li>
</ul>

<h3>
Note
</h3>
<p>If you're here without a specific goal in mind, we recommend you try out every page using the <mark>[glucose-6-phosphatase - Aves]</mark> protein set. Feel free to try <a href="https://bioinfmsc8.bio.ed.ac.uk/~s2013679/Website/fetch.php">fetching</a> it to see what happens!</p>
</body>
</html>
