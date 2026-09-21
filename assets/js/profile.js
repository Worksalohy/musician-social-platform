const followBtn = document.getElementById("follow-btn");
const successMessage = document.getElementById("success-message");

if (followBtn) {
    followBtn.addEventListener("click", function () {
        const userId = this.dataset.userId;

        fetch("../users/follow-toggle.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "user_id=" + encodeURIComponent(userId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.textContent =
                    data.action === "follow"
                        ? "Unfollow"
                        : "Follow";
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
}

if (successMessage) {
    setTimeout(() => {
        successMessage.style.opacity = "0";
        successMessage.style.transform = "translateY(-5px)";

        setTimeout(() => {
            successMessage.remove();
        }, 400);
    }, 3000);
}
