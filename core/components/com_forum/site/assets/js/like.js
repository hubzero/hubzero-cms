console.log("like on site forum");

window.addEventListener('DOMContentLoaded', (domEvent) => {
    // Find all the "like" button
    const likeButton = document.querySelectorAll('.comment-body .like')
    const likePopup = document.querySelectorAll('.comment-body .elementToPopup')
    if (likeButton.length) {
        for(let i = 0; i < likeButton.length;i++) {
            console.log(likeButton);
            console.log(likePopup);

            likeButton[i].onclick = (e) => {
                e.preventDefault();

                let hasHeart = likeButton[i].classList.contains("userLiked");

                const threadId = likeButton[i].dataset.thread;
                const postId = likeButton[i].dataset.post;
                const userId = likeButton[i].dataset.user;
                const userName = likeButton[i].dataset.userName;
                const likesList = likeButton[i].dataset.likesList;
                const likeCount = likeButton[i].dataset.count;

                console.log(threadId, postId, userId, likeCount, userName, likesList);

                const likesListArray = likesList.split("/");

                if (hasHeart) {
                    removeLike(threadId, postId, userId).then((res) => {
                        if (res.ok) {
                            const newLikeCount = Number(likeCount) - 1;
                            const newLikesString = likesListArray.filter(e => e !== userName).join('/');

                            likeButton[i].dataset.count = `${newLikeCount}`;
                            likeButton[i].innerHTML = (newLikeCount === 0) ? 'Like' : `Like (${newLikeCount})`;
                            likeButton[i].classList.remove("userLiked");
                            likeButton[i].dataset.likesList = newLikesString;

                            likePopup[i].innerHTML = newLikesString;

                            console.warn(`Like removed for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                        }
                    })
                } else {
                    addLike(threadId, postId, userId).then((res) => {
                        if (res.ok) {
                            const newLikeCount = Number(likeCount) + 1;
                            const newLikesString = [...likesListArray, userName].join('/');

                            likeButton[i].dataset.count = `${newLikeCount}`;
                            likeButton[i].innerHTML = `Like (${newLikeCount})`;
                            likeButton[i].classList.add("userLiked");
                            likeButton[i].dataset.likesList = newLikesString;

                            likePopup[i].innerHTML = newLikesString;

                            console.log(`Like recorded for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                        }
                    })
                }

                return false;
            };

            likeButton[i].onmouseover = (e) => {
                likePopup[i].style.display = 'block';
            };

            likeButton[i].onmouseleave = (e) => {
                likePopup[i].style.display = 'none';
            };

            // https://www.geeksforgeeks.org/how-to-open-a-popup-on-hover-using-javascript/
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