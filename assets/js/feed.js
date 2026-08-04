/*
|--------------------------------------------------------------------------
| LIKE SYSTEM
|--------------------------------------------------------------------------
*/


document.querySelectorAll(".like-form").forEach(form => {


    form.addEventListener("submit", async function(e){

        e.preventDefault();


        const formData = new FormData(this);


        const response = await fetch(
            "../posts/toggle_like.php",
            {
                method:"POST",
                body:formData
            }
        );


        const data = await response.json();



        const button =
            this.querySelector(".like-button");


        const postCard = this.closest(".post-card");

        const likeCount = postCard
            ? postCard.querySelector(".like-count")
            : null;


        if(button){

            button.textContent =
                data.liked ? "Unlike" : "Like";

        }



        if(likeCount){

            likeCount.textContent =
                data.count + " likes";

        }



    });


});




/*
|--------------------------------------------------------------------------
| DELETE POST
|--------------------------------------------------------------------------
*/


document.addEventListener("click", function(e){


    if(!e.target.classList.contains("delete-post-btn")){

        return;

    }



    if(!confirm("Delete this post?")){

        return;

    }



    const postId =
        e.target.dataset.postId;



    fetch(
        "../posts/delete_post.php",
        {

            method:"POST",

            headers:{

                "Content-Type":
                "application/x-www-form-urlencoded"

            },

            body:
            "post_id=" + encodeURIComponent(postId)

        }

    )


    .then(response => response.json())


    .then(data => {


        if(data.success){


            const post =
                document.getElementById(
                    "post-" + postId
                );


            if(post){

                post.remove();

            }


        }

        else{

            alert(data.message);

        }


    });


});






/*
|--------------------------------------------------------------------------
| CREATE COMMENT
|--------------------------------------------------------------------------
*/


document.querySelectorAll(".comment-form")
.forEach(form => {



    form.addEventListener(
        "submit",
        function(e){


            e.preventDefault();



            const formData =
                new FormData(this);



            fetch(
                this.action,
                {

                    method:"POST",

                    body:formData

                }

            )


            .then(response => response.json())


            .then(data => {



                if(data.success){



                    const comment =
                        data.comment;



                    const container =
                        document.getElementById(
                            "comments-" + comment.post_id
                        );



                    const newComment =
                    document.createElement("div");



                    newComment.className =
                        "comment-wrapper";



                    newComment.id =
                        "comment-" + comment.id;



                    newComment.innerHTML = `

                        <div class="comment">


                            <img src="${
                                comment.avatar
                                ? "../" + comment.avatar
                                : "../assets/musicculture-default-avatar.png"
                            }">


                            <div>


                                <strong>
                                    ${comment.username}
                                </strong>


                                <p>
                                    ${comment.content}
                                </p>


                                <small>
                                    ${comment.created_at}
                                </small>


                            </div>


                        </div>

                    `;



                    container.prepend(newComment);



                    form.querySelector("textarea")
                        .value="";



                    const total =
                        document.querySelector(
                            `.comment-total[data-post-id="${comment.post_id}"]`
                        );



                    if(total){

                        total.textContent =
                            data.comment_count +
                            " comments";

                    }


                }



            });



        }

    );



});








/*
|--------------------------------------------------------------------------
| DELETE COMMENT
|--------------------------------------------------------------------------
*/


document.addEventListener(
    "submit",
    function(e){



        if(
            !e.target.classList.contains(
                "delete-comment-form"
            )
        ){

            return;

        }



        e.preventDefault();



        if(!confirm("Delete this comment?")){

            return;

        }




        const form =
            e.target;



        const formData =
            new FormData(form);



        fetch(
            form.action,
            {

                method:"POST",

                body:formData

            }

        )


        .then(response => response.json())


        .then(data => {


            if(data.success){



                const id =
                    form.querySelector(
                        "input[name='comment_id']"
                    ).value;



                const comment =
                    document.getElementById(
                        "comment-" + id
                    );



                if(comment){

                    comment.remove();

                }


            }


        });



    }

);