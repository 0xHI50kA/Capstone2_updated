let open = document.querySelector('#open');
let close = document.querySelector('#close');
let navLinks = document.querySelector('.links');
let dropdownLinks = document.querySelectorAll('.dropdownLinks');
let dropdown = document.querySelectorAll('.dropdown');

//display nav menu when navbar is being clicked
open.addEventListener('click', (e) => {
console.log(e.target.parentElement)
navLinks.style.left = '0';
open.style.display = 'none';
close.style.display = 'block';
});

//undisplay nav menu when navbar is being clicked
close.addEventListener('click', (e) => {
navLinks.style.left = '-1000px';
open.style.display = 'block';
close.style.display = 'none';

});



