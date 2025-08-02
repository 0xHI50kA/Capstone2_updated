let servicesPageContent = [
    {
        immHeading: 'Adolescense Consultation',
        immContent: `We provide comprehensive services to support your well-being:

<br>👵 Adolescense Consultations - Addressing age-related health concerns and promoting a vibrant, active lifestyle for elderly individuals.
<br>
<br>📆 <span style="color:black;font-weight:bold;">When:</span> Every Wednesday
<br>⏰<span style="color:black;font-weight:bold;">Time:</span> 12:00 NN - 5:00 PM
<br>
<br>✅ No appointment needed!
<br>
<br>
Your journey to health and peace of mind starts here. `,
        immImage: '../Images/adolescence.jpg',
    },

];

let immunisationVaccine = document.querySelector('.immuneV');

function renderImmunisation() {
    // Render General Consultation Section
    const immunisation = servicesPageContent[0];
    immunisationVaccine.innerHTML += `<div>
        <div class="row imm">
            <div class="immT col-lg-5 col-md-6 col-sm-12 animate">
                <div class="topV">
                    <p style="font-size: 40px;">${immunisation.immHeading}</p>
                </div>
                <p style="font-size: 25px;">${immunisation.immContent}</p>
                 <a href="../../about.html" style="display: inline-block; padding: 10px 20px; color: white; background-color: #0078d7; text-decoration: none; border-radius: 5px; text-align: center;">Visit Us</a>
            </div>
            <div class="immI col-lg-5 col-md-6 col-sm-12 animate">
                <img src="${immunisation.immImage}" class="img-fluid"  alt="Immunisation Image">
            </div>
        </div>
    </div>`;


 
}

renderImmunisation();
