var toggled = 0;


function changeNav() {
    if ($("#nav-link").height() + $("#nav-link").offset().top > $("#splash-page").height()) {
        if (!toggled) {
            // $("#nav-link").css("color", "black");
            $("#nav-link").addClass("dark");
            toggled = 1;
        }
    } else {
        if (toggled) {
            // $("#nav-link").css("color", "white");
            $("#nav-link").removeClass("dark");
            toggled = 0;
        }
    }
}

document.addEventListener("scroll", (event)=>{
    changeNav();
});