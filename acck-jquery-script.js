$ = jQuery;
$(document).ready(function() {
    // Target the loader div element
    var $loader = $("#loader");

    // Append the loader HTML to the div element
    $loader.append('<div class="loader"></div>');

    // Perform image upload and database insertion here

    // When the process is complete, remove the loader HTML from the div element
    $loader.empty();
});