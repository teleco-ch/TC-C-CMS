document.addEventListener('DOMContentLoaded', function () {
    // menu overlay so we can open and close it bruh
    const overlay = document.querySelector('.menu-overlay');
    const hamburger = document.querySelector('.hamburger-menu');
    
    function toggleMenu() {
        if (overlay.classList.contains('open')) {
            overlay.classList.remove('open');
            setTimeout(() => {
                overlay.style.visibility = 'hidden';
            }, 400); // match this delay with the css transition duration because ja isch halt so
        } else {
            overlay.style.visibility = 'visible';
            overlay.classList.add('open');
        }
        hamburger.classList.toggle('open');
        document.body.style.overflow = overlay.classList.contains('open') ? 'hidden' : ''; // no scrolling on the main page while menu is open
    }

    // if burger king menu button exists, hook it up to toggle the menu
    if (hamburger) {
        hamburger.addEventListener('click', function () {
            toggleMenu();
        });
    }

    // close menu when clicking a link inside it because so funktioniert das halt, pech gha wenns der nid passt lol 
    if (overlay) {
        overlay.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                overlay.classList.remove('open');
                setTimeout(() => {
                    overlay.style.visibility = 'hidden';
                    hamburger.classList.remove('open');
                    document.body.style.overflow = ''; // kei ahnig was das macht aber es het mis problem emol glöst
                }, 400); // match transition duration ... again no clue what this actually does but it works so i'm not touching it
            });
        });
    }
});