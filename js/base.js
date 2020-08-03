/* Only essential Javascript should go here */

// Get page path
let path = window.location.pathname;
path = path.split('/');
page = path[path.length - 1];

if (page === "" || page === "index.php") {
    // Home page (index.php)
    document.body.classList.add('homepage');

    /* Hide all pictures at the start */
    let pics = $('.pic');
    $('.pic').css( "opacity", "0" );
}