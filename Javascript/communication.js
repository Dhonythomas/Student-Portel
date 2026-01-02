// enquiry.js
document.addEventListener("DOMContentLoaded", () => {
    const enquiryForm = document.querySelector('#formContainer form');
    const formContainer = document.getElementById('formContainer');
    const enquiryBtn = document.getElementById('enquiryBtn');

    // --- Local showPopup definition (uses your existing popup container) ---
    function showPopup(message) {
        const popup = document.getElementById("customPopup");
        const popupMsg = document.getElementById("popupMessage");

        if (popup && popupMsg) {
            popupMsg.innerHTML = message;
            popup.style.display = "flex";
        } else {
            console.error("Popup elements not found in DOM.");
        }
    }

    // --- Form submission logic ---
    if (enquiryForm) {
        enquiryForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const departmentEmail = document.getElementById('departmentemail').value.trim();
            const studentEmail = document.getElementById('stduentEmail').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!departmentEmail || !studentEmail || !subject || !message) {
                showPopup("⚠️ Please fill in all fields before sending your enquiry.");
                return;
            }

            const formData = { departmentEmail, studentEmail, subject, message };

            try {
                const response = await fetch('sendEnquiry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    showPopup("✅ Enquiry sent successfully!");
                    enquiryForm.reset();
                    if (formContainer) formContainer.classList.remove('visible');
                    if (enquiryBtn)
                        enquiryBtn.innerHTML = '<ion-icon name="mail-outline"></ion-icon>Make an Enquiry';
                } else {
                    showPopup("⚠️ " + (result.message || "Unable to send enquiry."));
                }
            } catch (error) {
                console.error('Error sending enquiry:', error);
                showPopup("❌ Failed to connect to the server. Please try again later.");
            }
        });
    }
});
