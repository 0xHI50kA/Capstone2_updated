let header = document.querySelector('.headin');

function renderHeader(argument) {
	header.innerHTML = `<!--logo-->
	<div class="logo">
		<a href="./index.html">
			<img src="symplogo.png">
		</a>
	</div>
	<!--nav links-->
	<div class="links">
		<ul><li class="home1"><a href="../../index.html">Home</a></li> 
		<li class="drop news"><a   href="../../Admin2/news-report.php">News</a> </i>
		<li class="home5"><a href="../Eventss/Event1.html">Events</a></li>
		<li class="home2"><a class="subnav" href="../Servicess/services.html">Services </a></li> 
		<li class="home4"> <a href="../SymptomAI/AboutAI.html">Symptom checker</a></li>	
		<li class="home3"><a href="../../nearby.html">Nearby Healthcare</a></i>
	</div>
	<div class="acct" style="position: absolute; z-index: -1; backgroud-color:white;"></div>
	<div class="bars">
    <p id="open" class="menu-icon">☰</p> <!-- Open Button (Hamburger) -->
    <p id="close" class="menu-icon">✖</p> <!-- Close Button (X) -->
</div>`;
};

renderHeader();
