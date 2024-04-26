document.addEventListener("DOMContentLoaded", function() {
    var boutonVoirGroupe = document.getElementById("chat-indicator");
    var btn = document.getElementById("chat");

    btn.addEventListener("click", function() {
        console.log("chatbot.js");
        if (boutonVoirGroupe.classList.contains("show")) {
            boutonVoirGroupe.classList.remove("show");
            boutonVoirGroupe.classList.add("hidden");
        } else {
            boutonVoirGroupe.classList.remove("hidden");
            boutonVoirGroupe.classList.remove("hidden");
            boutonVoirGroupe.classList.add("show");
        }
    });
});