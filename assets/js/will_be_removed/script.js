function openNav() {
  document.getElementById("mySidenav").style.width = "400px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}


// employee

var acc = document.getElementsByClassName("accordion_filter");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        /* Toggle between adding and removing the "active" class,
        to highlight the button that controls the panel */
        this.classList.toggle("active");

        /* Toggle between hiding and showing the active panel */
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}

// tarrifs

  var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = document.querySelector(".panel");
    if (panel.style.display === "flex") {
      panel.style.display = "none";
    } else {
      panel.style.display = "flex";
    }
  });
}

var acc1 = document.getElementsByClassName("accordion1");
var i;

for (i = 0; i < acc1.length; i++) {
  acc1[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel1 = document.querySelector(".panel1");
    if (panel1.style.display === "flex") {
      panel1.style.display = "none";
    } else {
      panel1.style.display = "flex";
    }
  });
}

// personal_account

 $(document).ready(function(){
    const slider = jQuery("#slider_one").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots: true,
        navText: ['<img src="/assets/img/left.png">', '<img src="/assets/img/right.png">'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:2
            }
        }
    });
});

 $(document).ready(function(){
    const slider = jQuery("#slider_two").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots: true,
        navText: ['<img src="/assets/img/left.png">', '<img src="/assets/img/right.png">'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:2
            }
        }
    });
});
