const form = document.getElementById("message-form");
const messagesContainer = document.getElementById("messages-container");

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("send-message.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.reset();
                loadMessages();
            } else {
                alert("Failed to send message.");
            }
        })
        .catch(error => console.error(error));
});

function loadMessages() {
    const userId = document.querySelector("[name='receiver_id']").value;

    fetch(`fetch-messages.php?user_id=${userId}`)
        .then(response => response.text())
        .then(html => {
            messagesContainer.innerHTML = html;
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
}

// Initial load
loadMessages();

// Refresh every 3 seconds
setInterval(loadMessages, 3000);