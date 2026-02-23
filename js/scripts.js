// Tristras App JS
$(document).ready(function() {
    // Menua zabaldu/itxi
    $('#menua-ireki').click(function() {
        $('#menua-estaldura').removeClass('hidden').addClass('flex').animate({opacity: 1}, 200);
        $('#alboko-menua').removeClass('translate-x-full');
    });

    $('#menua-itxi, #menua-estaldura').click(function() {
        $('#menua-estaldura').animate({opacity: 0}, 200, function() {
            $(this).addClass('hidden').removeClass('flex');
        });
        $('#alboko-menua').addClass('translate-x-full');
    });
});


