let header = document.querySelector('.headin');

function renderHeader(argument) {
	header.innerHTML = `<!--logo-->
	<div class="logo">
		<a href="../../index.html">
			<img src="symplogo.png" >
		</a>
	</div>
	<!--nav links-->
	<div class="links">
		<ul> <li><a href="../../index.html">Home</a></li> 
		<li class="drop news"><a href="../../Admin2/news-report.php">News</a> </i>
			<li><a href="../Eventss/Event1.html">Events</a></li>
			<li class="home2"><a class="subnav" href="services.html">Services </a> </i>
				<!-- <ul class="drp">
					<li><a href="Immunisation.html">Immunization</a></li>
					<li><a href="Surgery.html">Consultations</a></li>
					<li><a href="Skin.html">Dental Check-up</a></li>
					<li><a href="Fitnes.html">Breast Screening</a></li>
					<li><a href="travel_vaccine.html">TB DOTS</a></li>
					
					<li><a href="Antenatal.html"> Saturday</a></li>
					<li><a href="Antenatal.html"> Sunday</a></li>
					
				</ul> -->
				
				</li> 
				<li><a href="../SymptomAI/AboutAI.html">Symptom checker</a></li>
				
			<li class="drop abt"><a class="subnav" href="../../nearby.html">Nearby Healthcare</a></i>
    <!--  <ul class="sub">
		<li><a href="Doctors.html"></a></li>
		<li><a href="Nurses.html"></a></li>
		<li><a href="Management.html">< Our Management</a></li>
      </ul> -->
			</li>

			
    <!--  <ul class="subb">
		<li><a href="../atabs_Health_Care/Admin2/news-report.php">Health News</a></li> -->


		
      </ul>
			</li>
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
    <p id="open" class="menu-icon">☰</p> <!-- Open Button (Hamburger) -->
    <p id="close" class="menu-icon">✖</p> <!-- Close Button (X) -->
</div>`;
};

renderHeader();
