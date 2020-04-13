function toggleMenu() {
  var x = document.getElementById("sidebar");
  x.classList.toggle("responsive");
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("toggle").addEventListener("click", toggleMenu);
});
