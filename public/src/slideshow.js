(function(){
  "use strict";

  var slideIndex = 1;

  function plusSlides(n){
    showSlides(slideIndex += n);
  }

  function showSlides(n){
    var i;
    var slides = document.getElementsByClassName("mySlides");

    if (n > slides.length) slideIndex = 1;
    if (n < 1) slideIndex = slides.length;

    for (i = 0; i < slides.length; i++) slides[i].style.display = "none";

    slides[slideIndex-1].style.display = "block";
  }

  document.addEventListener("DOMContentLoaded", function(){
    showSlides(slideIndex);
  });

  document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("prev").addEventListener("click", function(){
      plusSlides(-1);
    });
  });

  document.addEventListener("DOMContentLoaded", function(){
    document.getElementById("next").addEventListener("click", function(){
      plusSlides(1);
    });
  });
})();
