let header = document.querySelector('.headin');

function renderHeader(argument) {
	header.innerHTML = `<!--logo-->
	<div class="logo">
		<a href="index.html">
			<img src="symplogo.png">
		</a>
	</div>
	<!--nav links-->
	<div class="links">
		<ul> <li class="home1"><a href="index.html">Home</a></li> 
			<li class="home2" ><a   href="./Admin2/news-report.php">News</a> </i>
			<li><a href="AAtabHealthCare/Eventss/Event1.html">Events</a></li>
			<li ><a class="subnav" href="AAtabHealthCare/Servicess/services.html">Services </a> </i>
			<li><a href="AAtabHealthCare/SymptomAI/AboutAI.html">Symptom checker</a></li>	
			<li class="home3"  class="drop abt" ><a class="subnav" href="nearby.html">Nearby Healthcare</a></i>
      </ul>
			
	</div>
	<!--	<span class="sun">
<i class="fa-solid fa-sun "></i>
</span>
<span class="moon">
<i class="fa-solid fa-moon "></i>
</span>
	-->
	<!--accounts-->
	<div class="acct" style="position: absolute; z-index: -1; backgroud-color:white;">
		
</div>
	
	
	<div class="bars">
    <i id="open" class="menu-icon">☰</i> <!-- Open Button (Hamburger) -->
    <i id="close" class="menu-icon">✖</i> <!-- Close Button (X) -->
</div>`;
};

renderHeader();