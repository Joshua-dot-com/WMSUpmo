document.addEventListener("DOMContentLoaded", function () {
    // Get all form elements
    const roleSelect = document.getElementById("roleSelect");
    const collegeFieldContainer = document.getElementById("collegeFieldContainer");
    const collegeSelect = document.getElementById("collegeSelect");
    const adminRolesContainer = document.getElementById("adminRolesContainer");
    const adminRolesSelect = document.getElementById("adminRolesSelect");
    const otherServicesContainer = document.getElementById("otherServicesContainer");
    const otherServicesInput = document.getElementById("otherServices");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm-password");
    const passwordStrengthText = document.getElementById("password-strength-text");
    const passwordMatchText = document.getElementById("password-match-text");

    // Role selection logic
    roleSelect.addEventListener("change", function () {
        if (this.value === "Administrative Officials") {
            collegeFieldContainer.classList.add("hidden");
            collegeSelect.removeAttribute("required");

            adminRolesContainer.classList.remove("hidden");
            adminRolesSelect.setAttribute("required", "");
        } else {
            collegeFieldContainer.classList.remove("hidden");
            collegeSelect.setAttribute("required", "");

            adminRolesContainer.classList.add("hidden");
            adminRolesSelect.removeAttribute("required");

            otherServicesContainer.classList.add("hidden");
            otherServicesInput.removeAttribute("required");
        }
    });

    // Administrative roles selection logic
    adminRolesSelect.addEventListener("change", function () {
        if (this.value === "Other Services") {
            otherServicesContainer.classList.remove("hidden");
            otherServicesInput.setAttribute("required", "");
        } else {
            otherServicesContainer.classList.add("hidden");
            otherServicesInput.removeAttribute("required");
        }
    });

    // Toggle password visibility
    document.getElementById("toggle-password").addEventListener("click", function () {
        const icon = this.querySelector("i");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    });

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = "Weak";
        if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
            strength = "Strong";
        } else if (password.length >= 6) {
            strength = "Medium";
        }
        return strength;
    }

    passwordInput.addEventListener("input", function () {
        passwordStrengthText.textContent = "Password strength: " + checkPasswordStrength(passwordInput.value);
    });

    // Check if passwords match
    confirmPasswordInput.addEventListener("input", function () {
        if (confirmPasswordInput.value === passwordInput.value) {
            passwordMatchText.textContent = "Passwords match";
            passwordMatchText.classList.add("text-green-500");
            passwordMatchText.classList.remove("text-red-500");
        } else {
            passwordMatchText.textContent = "Passwords do not match";
            passwordMatchText.classList.add("text-red-500");
            passwordMatchText.classList.remove("text-green-500");
        }
    });

    // Handle form submission
    document.getElementById("signup-form").addEventListener("submit", function (e) {
        e.preventDefault();
        
        // Check if passwords match before submitting
        if (passwordInput.value !== confirmPasswordInput.value) {
            alert("Passwords do not match!");
            return;
        }

        const formData = new FormData(this);
        fetch("PHP/register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                window.location.href = "Login.html";
            }
        })
        .catch(error => console.error("Error:", error));
    });
});