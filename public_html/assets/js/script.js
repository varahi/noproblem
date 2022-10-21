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
    const slider = $("#slider_one").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots: true,
        navText: ['<img src="assets/img/left.png">', '<img src="assets/img/right.png">'],
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
    const slider = $("#slider_two").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots: true,
        navText: ['<img src="assets/img/left.png">', '<img src="assets/img/right.png">'],
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
    const slider = $("#slider_one_vacan").owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots: true,
        navText: ['<img src="assets/img/left.png">', '<img src="assets/img/right.png">'],
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


// изменение
var acc = document.getElementsByClassName("accordion_filter_one_vacan");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function(e) {
          e.preventDefault();
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

// до сюда


var maxWidth = 200;
var maxHeight = 100;

$('.vacancy_description').on('input', function(e){         
    // Если появляется скролл и его ширина больше клиентской → увеличиваем ширину клиента
    if (this.scrollWidth > this.clientWidth) 
        this.style.width = this.scrollWidth + 'px';
      
    // Если ширина больше максимально допустимой → даем словам "ломаться" и фиксируем ширину
    if (this.clientWidth >= maxWidth) {
        this.style.width = maxWidth;
        this.style.whiteSpace = 'pre-wrap';
    }
   
    if (this.clientHeight > maxHeight) 
        return;    
      
    if (this.scrollHeight > this.clientHeight) 
        this.style.height = this.scrollHeight + 'px';
})

 let fields = document.querySelectorAll('.field__file');
    Array.prototype.forEach.call(fields, function (input) {
      let label = input.nextElementSibling,
        labelVal = label.querySelector('.field__file-fake').innerText;
  
      input.addEventListener('change', function (e) {
        let countFiles = '';
        if (this.files && this.files.length >= 1)
          countFiles = this.files.length;
  
        if (countFiles)
          label.querySelector('.field__file-fake').innerText = 'Выбрано файлов: ' + countFiles;
        else
          label.querySelector('.field__file-fake').innerText = labelVal;
      });
    });

    $(function(){
    $('#job_form_category').change(function(){
        $('section label').show();
        $('#job_form_category').each(function(){
            var val=this.value
            if (val!='') {
                val=this.id.valueOf()[0]+'-'+val;
                $('section label:not([data-game*="'+val+'"])').hide();
            };
        });
    });
});