(function(){
  "use strict";

  var slideIndex = 1;

  function plusSlides(n){
    showSlides(slideIndex += n);
  }

  function showSlides(n){
    var i;
    var slides = document.getElementsByClassName("mySlides");

    if (!slides) return;

    if (n > slides.length) slideIndex = 1;
    if (n < 1) slideIndex = slides.length;

    for (i = 0; i < slides.length; i++) slides[i].style.display = "none";

    slides[slideIndex-1].style.display = "block";
  }

  document.addEventListener("DOMContentLoaded", function(){
    if (document.getElementsByClassName("mySlides").length !== 0){
      showSlides(slideIndex);
    }

    if (!!document.getElementById("prev") && !!document.getElementById("next")){
      document.getElementById("prev").addEventListener("click", function(){
        plusSlides(-1);
      });

      document.getElementById("next").addEventListener("click", function(){
        plusSlides(1);
      });
    }
  });
})();
