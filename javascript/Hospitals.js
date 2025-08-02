let slide = document.querySelector('.slide');



let nurses = [
{
	nurseName: 'BGH (Baguio General Hospital)',
	profile: 'View location',
	link: '_hospital1.php',
	nurseImage: 'images/hospital/bgh.jpg'
},
{
	nurseName: 'Pines City Doctors Hospital',
	profile: 'View location',
	link: '_hospital2.php',
	nurseImage: 'images/hospital/pines.jpg'
},
{
	nurseName: 'Notre Dame de Chartres Hospital',
	profile: 'View location',
	link: '_hospital3.php',
	nurseImage: 'images/hospital/notre.jpg'
},
];


let renderNurses = () => {
	nurses.forEach(nurse => {
		slide.innerHTML += `<div class="teamlead animate col-sm-12 col-lg-4 col-md-6">
   		<div class="nurse">
   		<div class="doc-img">
   			<img src="${nurse.nurseImage}">
   		</div>
   			<div class="team-bio">
   			<span class="nn">
   			<span class="namE"><strong>${nurse.nurseName}</strong></span>
   			<!--
   			<span class="specialty">Autopedic</span>
   -->
   		</span>
   		<!--
   			<span class="ss">
   				<span class="patientS "> 2K+ Patients</span>
   				<span class="rating"><i class="fa-sharp fa-solid fa-star"> </i>4.8(500)</span>
   			</span>
   		-->
   		</div>
       <p class="viewProfile">
   				<a href="${nurse.link}" >${nurse.profile}</a>
   			</p>
   		</div>
   		</div>
   		`;
	});
};
	renderNurses();


