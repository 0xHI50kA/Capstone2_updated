let aboutPage = document.querySelector('.about');

let about = {
    abt: 'Atab Health Care Center',
    firstP: `Atab Health Care Center is committed to providing comprehensive, compassionate medical care that supports the overall well-being of the community. The health center’s dedicated doctor delivers top-quality healthcare with professionalism, expertise, and genuine concern. With a strong focus on patient-centered service, Atab Health Care Center ensures every individual's health needs are met with the utmost attention and care.`,
    contact: `📞(074) 420 9087 |📧 atabhc2019@gmail.com`,
    schedule: {
        openTime: '07:45',
        closeTime: '17:00',
    },
};

function getBusinessStatus() {
    const current = new Date();
    const currentTime = `${current.getHours()}:${current.getMinutes()}`.padStart(5, '0');
    const openTime = about.schedule.openTime;
    const closeTime = about.schedule.closeTime;

    if (currentTime >= openTime && currentTime < closeTime) {
        return '<span class="status open">Open</span>';
    } else {
        return '<span class="status closed">Closed now</span>';
    }
}

let renderAbout = () => {
    aboutPage.innerHTML = `
        <!-- Back Button -->
        

        <!-- Main About Section -->
        <div >
       
            <div >
                <p class"responsive-heading" style=" font-weight:bold; text-align: center;font-size: 40px;font-family: Poppins,sans-serif;color: #333333;">${about.abt}</p>
                <br>
                <p  class="abtDes" >${about.firstP}</p>
                <br>
                <p   class="abtContact"> <span style="color:black;font-weight:bold;">Contact:</span> ${about.contact}</p>
                <p  class="abtSchedule">
                    <span > Operating Hours:</span>🕒 7:45 AM - 5:00 PM <br> 
                   <span > Current Status: </span> ${getBusinessStatus()}
                </p>
                <!-- <div class="abtSoc">
                    <a href="https://www.facebook.com/profile.php?id=100068242016400" target="_blank" class="socIcon">
                        <i class="fab fa-facebook" style="font-size: 24px; color: #3b5998;"></i>
                    </a>
                    <a href="https://www.facebook.com/messages/t/100241642270992" target="_blank" class="socIcon">
                        <i class="fab fa-facebook-messenger" style="font-size: 24px; color: #0084ff;"></i>
                    </a> 
                </div> -->
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 abtPics animate">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d93300.65603413539!2d120.43108214335935!3d16.388259400000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3391a1009513eced%3A0xaa8b4b73101cf60!2sAtab%20Health%20Center!5e1!3m2!1sen!2sph!4v1733217315122!5m2!1sen!2sph" 
                    width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        
        </div>
    `;
};

// function scrollToDiv() {
//   const targetDiv = document.getElementById('target');
//   targetDiv.scrollIntoView({ behavior: 'smooth' });
// }

// Back Button functionality
function goBack() {
    window.history.back();
}

renderAbout();

// Update the status every minute
setInterval(() => {
    renderAbout();
}, 60000);
