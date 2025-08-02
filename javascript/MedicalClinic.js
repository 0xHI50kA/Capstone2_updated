let slide = document.querySelector('.slide');



let nurses = [
  {
	nurseName: ' Brass Medical Clinic',
	profile: 'View location',
	link: '_medical1.php',
	nurseImage: 'images/pharmacy/clinic1.jpg',
},
{
	nurseName: 'Hospicare MD Clinic',
	profile: 'View location',
	link: '_medical2.php',
	nurseImage: 'images/pharmacy/hospicare.jpg'
},
{
	nurseName: ' Dr. Arvelius Medical Clinic',
	profile: 'View location',
	link: '_medical3.php',
	nurseImage: 'images/pharmacy/clinic3.jpg'
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


