/*function openCity(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}*/
$ = jQuery;
$( document ).ready(function() {
    $('.tabcontent').addClass('closed');
    $('.content_tabs').find('.tabcontent').first().removeClass('closed');
    $('.content_tabs').find('.tablinks').first().addClass('open');


    $('.tablinks').click(function () {
        var current_id = $(this).data('target');

        var parent = $(this).parent('.tab').parent('.content_tabs');
        parent.find('.tabcontent').addClass('closed');
        parent.find('.tablinks').removeClass('open');
        $('#'+current_id).removeClass('closed');
        $(this).addClass('open');
    })

    $( ".code_editor_content" ).change(function() {

    });
});