const body = document.querySelector("body"),
    modeToggle = body.querySelector(".mode-toggle");

// sidebar = body.querySelector("nav");
// sidebarToggle = body.querySelector(".sidebar-toggle");

modeToggle.addEventListener("click", () => {
    body.classList.toggle("dark")
});

// sidebarToggle.addEventListener("click", () => {
//     sidebar.classList.toggle("close");
// })