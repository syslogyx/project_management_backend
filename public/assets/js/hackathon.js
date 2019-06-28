// Global Script


$(document).ready(function () {
    M.AutoInit();
    $('.slider').slider();
});


document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.slider');
    var instances = M.Slider.init(elems, options);
  });
