const conversationList = document.getElementById("conversation-list");


async function loadConversations() {

    try {

        const response = await fetch("fetch-conversations.php");


        if (!response.ok) {
            throw new Error("Unable to load conversations.");
        }


        const html = await response.text();


        conversationList.innerHTML = html;


    } catch (error) {

        console.error(error);


        conversationList.innerHTML = `
            <p class="error-message">
                Unable to load conversations.
            </p>
        `;
    }
}


loadConversations();


// Refresh inbox automatically
setInterval(loadConversations, 5000);