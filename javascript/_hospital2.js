let chinny = {
    name: 'Pines City Doctors Hospital',
    img: 'images/hospital/pines.jpg',
};

let chinnyPro = document.querySelector('.henry');

let renderChinny = () => {
    chinnyPro.innerHTML = `
        <div class="row container-fluid dr">
            <div class="col-sm-12 col-lg-5 col-md-12 animate drImage">
                <img src="${chinny.img}" class="img-fluid" alt="${chinny.name}">
            </div>
            <div class="col-sm-12 animate col-lg-5 col-md-12 drDescript">
                <p class="drName">${chinny.name}</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d46641.063446769665!2d120.55166170220645!3d16.426905910296195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3391a3de5954d665%3A0x4574abca6156e018!2sPines%20City%20Doctor&#39;s%20Hospital!5e1!3m2!1sen!2sph!4v1742829489493!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="mt-3">
                   
                </div>
            </div>
        </div>`;
};

renderChinny();
