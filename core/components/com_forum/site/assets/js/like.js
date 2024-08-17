console.log("like on site forum");

window.addEventListener('DOMContentLoaded', (domEvent) => {
    // Find all the "like" button
    const likeButton = document.querySelectorAll('.comment-options .like')
    if (likeButton.length) {
        for(let i = 0; i < likeButton.length;i++) {
            likeButton[i].onclick = (e) => {
                e.preventDefault();

                let hasHeart = likeButton[i].classList.contains("userLiked");

                const threadId = likeButton[i].dataset.thread;
                const postId = likeButton[i].dataset.post;
                const userId = likeButton[i].dataset.user;

                console.log(threadId, postId, userId);

                if (hasHeart) {
                    removeLike(threadId, postId, userId).then((res) => {
                        if (res.ok) {
                            likeButton[i].classList.remove("userLiked");
                            console.warn(`Like removed for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                        }
                    })
                } else {
                    addLike(threadId, postId, userId).then((res) => {
                        if (res.ok) {
                            likeButton[i].classList.add("userLiked");
                            console.log(`Like recorded for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                        }
                    })
                }

                return false;
            };
        }
    }
});

const addLike = async (threadId, postId, userId) => {
    const postUrl = "/api/forum/likes/addLikeToPost";
    const data = {threadId, postId, userId};

    try {
        let response = await fetch(postUrl, {
            method: "POST", headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams(data) // urlencoded form body
        });

        if (!response.ok) {
            window.confirm("Server Error with API");
            console.error(`Error Code: ${response.status} / Error Message: ${response.statusText}`);
        }

        return response;
    } catch (error) {
        if (error instanceof SyntaxError) {
            console.error('There was a SyntaxError', error);
        } else {
            console.error('There was an error', error);
        }
    }
};

const removeLike = async (threadId, postId, userId) => {
    const deleteAssertionUrl = "/api/forum/likes/deleteLikeFromPost";
    const data = {threadId, postId, userId};

    const deleteAssertionResp = await fetch(deleteAssertionUrl, {
        method: "DELETE", headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: new URLSearchParams(data)
    })

    if (!deleteAssertionResp.ok) {
        window.confirm("Server Error with API");
        console.error(`Error Code: ${response.status} / Error Message: ${response.statusText}`);
    }

    return deleteAssertionResp;
}