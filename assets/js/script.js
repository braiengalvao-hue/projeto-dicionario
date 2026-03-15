const themeBtn = document.getElementById("theme-toggle");
const themeIcon = document.getElementById("theme-icon"); // Certifique-se que este ID está na tag <img>

function toggleTheme() {
  const isDark = document.body.classList.toggle("dark-mode");

  if (isDark) {
    localStorage.setItem("theme", "dark");
    // Troca o caminho para a imagem do Sol
    themeIcon.src = "assets/images/sun_icon.svg";
  } else {
    localStorage.setItem("theme", "light");
    // Troca o caminho para a imagem da Lua
    themeIcon.src = "assets/images/moon_icon.svg";
  }
}

// Configura o evento de clique
themeBtn.addEventListener("click", toggleTheme);

// Ao carregar a página: verifica a preferência salva e define a imagem correta
window.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
    themeIcon.src = "assets/images/sun_icon.svg";
  } else {
    themeIcon.src = "assets/images/moon_icon.svg";
  }
});
