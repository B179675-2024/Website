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

<h1>Statement of Credits</h1>
<p>Any code taken taken from external sources was rewritten to suit the author's own interpretation for what the website's codebase should do or look like. External references were typically consulted to better understand how certain code functionalities work, but some code blocks were directly adapted from these references and will be additionally mentioned below.</p>
<h2>A. Code references</h2>
<ul>
 <li><a href="https://bioinfmsc8.bio.ed.ac.uk/IWD2.html">IWD2 course website</a>: the course website was used to consolidate general knowledge about mySQL, php and JavaScript especially. Menu.php derived from the <a href="https://bioinfmsc8.bio.ed.ac.uk/AY24_IWD2_DirLearn_02b.html">directed learning exercise</a>'s file menuf.php.</li>
 <li><a href="https://www.php.net/manual/en/book.pdo.php">PHP manual for PDO</a>: the official php pdo manual and its examples (e.g., PDO fetch() ) were used to write all code segments handling connections to the mySQL database and the resulting pdo object.</li>
 <li><a href="https://www.w3schools.com/">W3schools</a>: supported html tag usage, and example code was very important to help implement some code segments, notably the <a href="https://www.w3schools.com/php/php_forms.asp">guide for php form handling</a> that defined the transition from data selection to analysis in the analysis pages.</li>
 <li><a href="https://stackoverflow.com/">Stackoverflow</a>: the stackoverflow forums was an important source of reflection when writing certain code segments; the javascript toggle element of <b>Conservation</b> was adapted from <a href="https://stackoverflow.com/questions/19163327/how-do-i-make-a-div-hidden-by-default-using-javascript">this question's page</a> about hiding elements by default.</li>
</ul>

<h2>B. Use of AI</h2>
<p><a href="https://chatgpt.com/">ChatGPT</a> model 4o was queried for co-piloting and to improve coding time efficiency. After deciding on the overall design for each web page, ChatGPT was sent four queries. Each query described the structure of the mySQL database that would store the protein sequences as well as brief descriptions of the high-level design for the <b>Fetch, Conservation, Motifs</b> and <b>Transmembrane domains</b> components of the website respectively (one query per component). 
Then, each query requested from ChatGPT a code-implementation suggestion based on php. Some of the code and formatting suggested by chatGPT was adapted to be integrated in the website's codebase (e.g., code blocks that parse text output from the EMBOSS tools are largely adapted from these code suggestions). 
Even if they weren't directly adapted, other code suggestions helped form other aspects of the implementation such as the idea to use WebEnv and QueryKey tokens from the esearch request to process an efetch request able to collect more complete data samples; the idea to use base64 encoding was also taken from ChatGPT.
</p

<p>
To make sense of ChatGPT's suggestions, further queries in response were sent, probing for specific functional reasoning 
(e.g., considerations for writing permissions when using shell_exec() ). 
The result from these exchanges with the AI model supported adaptation of the model's code suggestions into relevant pieces of code for the website 
and encouraged looking for documentation on specific and relevant items (e.g., looking for the php shell_exec() function in the official php manual). 
Code from ChatGPT considered for adapation in PSAW was first checked for validity by searching for backing internet resources in the case of code choices unfamiliar to the author.
</p>
</body>
</html>
