</div>

    <script>
        const searchInput = document.getElementById("global-search");
const searchResults = document.getElementById("search-results");


searchInput?.addEventListener("keyup", function(){

    const query = this.value.trim();


    if(query.length < 2){

        searchResults.innerHTML = "";
        return;

    }


    fetch("/search/live-search.php?q=" + encodeURIComponent(query))

    .then(response => response.json())

    .then(users => {


        searchResults.innerHTML = "";


        users.forEach(user => {


            const avatar = user.avatar
                ? "/" + user.avatar
                : "/assets/musicculture-default-avatar.png";


            searchResults.innerHTML += `

                <a class="search-item"
                   href="/profile/profile.php?id=${user.id}">

                    <img src="${avatar}">

                    <div>
                        <strong>${user.username}</strong>
                        <br>
                        <small>${user.instrument ?? ''}</small>
                    </div>

                </a>

            `;


        });


    });


});
    </script>

</body>
</html>