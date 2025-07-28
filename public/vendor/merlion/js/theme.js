(function () {
    function initActiveMenu() {
        let currentPath = location.pathname;
        if (currentPath) {
            let a = null;
            let links = document.getElementById("navbar-nav")?.querySelectorAll("a") || [];
            for (let p in links) {
                let el = links[p];
                let url = null;
                try {
                    url = el.getAttribute('href');
                } catch (e) {
                    continue;
                }
                if (!url) {
                    continue;
                }
                const match = url.match(/^https?:\/\/[^\/]+(.*)$/);
                let href = match ? match[1] || '/' : url;
                if (href === currentPath) {
                    a = el;
                    break;
                }
                if (currentPath.indexOf(href) !== -1) {
                    a = el;
                }
            }
            if (a) {
                var navItem = a.closest(".nav-item");
                if (navItem) {
                    navItem.classList.add("active");
                }
                let parentCollapseDiv = a.closest(".collapse.menu-dropdown");
                if (parentCollapseDiv) {
                    parentCollapseDiv.classList.add("show");
                    parentCollapseDiv.parentElement.children[0].classList.add("active");
                    parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
                    if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
                        parentCollapseDiv.parentElement.closest(".collapse").classList.add("show");
                        if (parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling)
                            parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling.classList.add("active");

                        if (parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse.menu-dropdown")) {
                            parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").classList.add("show");
                            if (parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").previousElementSibling) {
                                parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.closest(".collapse").previousElementSibling.classList.add("active");
                                if ((document.documentElement.getAttribute("data-layout") === "horizontal") && parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.closest(".collapse")) {
                                    parentCollapseDiv.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.closest(".collapse").previousElementSibling.classList.add("active")
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    window.addEventListener("load", function () {
        initActiveMenu();
    });
})();


!function(e) {
    "function" == typeof define && define.amd ? define(e) : e()
}(function() {
    "use strict";
    const e = {
        theme: "light",
        "theme-base": "gray",
        "theme-font": "sans-serif",
        "theme-primary": "blue",
        "theme-radius": "1"
    }
        , t = new Proxy(new URLSearchParams(window.location.search),{
        get: (e, t) => e.get(t)
    });
    for (const n in e) {
        const o = t[n];
        let a;
        if (o)
            localStorage.setItem("tabler-" + n, o),
                a = o;
        else {
            a = localStorage.getItem("tabler-" + n) || e[n]
        }
        a !== e[n] ? document.documentElement.setAttribute("data-bs-" + n, a) : document.documentElement.removeAttribute("data-bs-" + n)
    }
});
