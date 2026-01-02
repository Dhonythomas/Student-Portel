document.addEventListener("DOMContentLoaded", function () {
    // ===== ELEMENT REFERENCES =====
    const wrapper = document.querySelector(".wrapper");
    const loginBtn = document.querySelector(".btnLogin-popup");
    const closeIcon = document.querySelector(".icon-close");
    const hamburger = document.querySelector(".hamburger");
    const navigation = document.querySelector(".navigation");
    const overlay = document.querySelector(".overlay");
    const navLinks = document.querySelectorAll(".navigation a, .navigation .btnLogin-popup");
    const login = document.getElementById("login-btn");

    // ===== POPUP HANDLERS =====
    const popup = document.getElementById("customPopup");
    const popupMessage = document.getElementById("popupMessage");

    document.querySelectorAll(".close-btn, .popup-ok").forEach(btn => {
        btn?.addEventListener("click", closePopup);
    });

    document.getElementById("username")?.addEventListener("keyup", validateUsername);
    document.getElementById("password")?.addEventListener("keyup", validatePassword);

    function showPopup(message) {
        popupMessage.innerText = message;
        popup.classList.add("visible");
    }

    function closePopup() {
        popup.classList.remove("visible");
    }

    // ===== MENU AND WRAPPER CONTROLS =====
    loginBtn?.addEventListener("click", () => {
        closeMenu();
        wrapper?.classList.add("active-popup");
    });

    closeIcon?.addEventListener("click", () => {
        wrapper?.classList.remove("active-popup");
    });

    hamburger?.addEventListener("click", () => {
        navigation?.classList.toggle("active");
        overlay?.classList.toggle("active");
        hamburger.innerHTML = navigation?.classList.contains("active")
            ? '<ion-icon name="close-outline"></ion-icon>'
            : '<ion-icon name="menu-outline"></ion-icon>';
    });

    overlay?.addEventListener("click", closeMenu);

    navLinks.forEach(link => {
        link.addEventListener("click", closeMenu);
    });

    function closeMenu() {
        navigation?.classList.remove("active");
        overlay?.classList.remove("active");
        hamburger.innerHTML = '<ion-icon name="menu-outline"></ion-icon>';
    }

    // ===== VALIDATION FUNCTIONS =====
    function validateUsername() {
        const username = document.getElementById("username").value.trim();
        const usernameError = document.getElementById("username-error");

        if (username === "") {
            usernameError.innerHTML = "Username is required";
            return false;
        } else if (username.length < 4) {
            usernameError.innerHTML = "Must be at least 4 characters";
            return false;
        } else {
            usernameError.innerHTML = "";
            return true;
        }
    }

    function validatePassword() {
        const password = document.getElementById("password").value.trim();
        const passwordError = document.getElementById("password-error");

        if (password === "") {
            passwordError.innerHTML = "Password is required";
            return false;
        } else if (password.length < 6) {
            passwordError.innerHTML = "Must be at least 6 characters";
            return false;
        } else {
            passwordError.innerHTML = "";
            return true;
        }
    }

    // ===== LOGIN LOGIC =====
    login?.addEventListener("click", async () => {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const remindme = document.querySelector("#remember")?.checked || false;

        if (!validateUsername() || !validatePassword()) {
            showPopup("Please fix the errors before logging in.");
            return;
        }

        // 🔄 Spinner effect during login
        login.disabled = true;
        const originalText = login.innerHTML;
        login.innerHTML = `<span class="spinner"></span> Logging in...`;

        try {
            const response = await fetch("login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    remindme: remindme,
                    action: "login"
                })
            });

            const result = await response.json();

            if (result.status === "success") {
                showPopup("Login successful!");

                setTimeout(() => {
                    const redirect = {
                        student: "student.html",
                        staff: "staff.html",
                        admin: "admin.html"
                    }[result.role];

                    if (redirect) window.location.href = redirect;
                }, 800);
            } else {
                showPopup("Login failed: " + result.message);
            }
        } catch (error) {
            console.error(error);
            showPopup("Error connecting to server.");
        } finally {
            login.disabled = false;
            login.innerHTML = originalText;
        }
    });

    // Prevent default form submit (optional safety)
    document.querySelector("form")?.addEventListener("submit", e => e.preventDefault());
});
