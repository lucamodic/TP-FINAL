const hamburgerButton = document.querySelector(".hamburger-button");
const headerRight = document.querySelector(".header-right");

let headerAbierto = false;

hamburgerButton.addEventListener("click", () => {
    hamburgerButton.classList.toggle("rotate");
    if (headerAbierto) {
        headerRight.classList.remove("abierto");
        headerAbierto = false;
    } else {
        headerRight.classList.add("abierto");
        headerAbierto = true;
    }
});