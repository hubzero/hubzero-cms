window.addEventListener('DOMContentLoaded', (domEvent) => {
    // Find all the "like" / stat button
    const commentSections = document.querySelectorAll('.comment-content')
    if (commentSections.length) {
        for(let i = 0; i < commentSections.length;i++) {
            let likeButton = commentSections[i].querySelector('.like');
            let likeStatsLink = commentSections[i].querySelector('.likesStat');
            let whoLikedPostDiv = commentSections[i].querySelector('.whoLikedPost');

            // Make sure all these HTML elements are present before assigning attributes
            if (likeStatsLink && whoLikedPostDiv && likeButton) {
                likeStatsLink.onclick = (e) => {
                    e.preventDefault();
                    this.__toggle = !this.__toggle;
                    if(this.__toggle) {
                        whoLikedPostDiv.style.height = `${whoLikedPostDiv.scrollHeight}px`;
                    } else {
                        whoLikedPostDiv.style.height = 0;
                    }
                }

                likeButton.onclick = (e) => {
                    e.preventDefault();
    
                    let hasHeart = likeButton.classList.contains("userLiked");
    
                    const threadId = likeButton.dataset.thread;
                    const postId = likeButton.dataset.post;
                    const userId = likeButton.dataset.user;
                    const userName = likeButton.dataset.userName;
                    const nameAndId = `${userName}#${userId}`;
    
                    const likesList = likeButton.dataset.likesList;
                    const likeCount = likeButton.dataset.count;
    
                    // console.log(threadId, postId, userId, likeCount, userName, likesList);
    
                    const likesListArray = likesList.split("/");
    
                    if (hasHeart) {
                        removeLike(threadId, postId, userId).then((res) => {
                            if (res.ok) {
                                const newLikeCount = Number(likeCount) - 1;
                                const newLikesString = likesListArray.filter(e => e !== nameAndId).join('/');
    
                                likeButton.dataset.count = `${newLikeCount}`;
                                likeButton.classList.remove("userLiked");
                                likeButton.dataset.likesList = newLikesString;
                                likeStatsLink.innerHTML = (newLikeCount === 0) ? 'No Likes' : `View Likes (${newLikeCount})`;
    
                                if (newLikeCount > 0) {
                                    let whoLikedArray = [];
                                    const newLikesArray = newLikesString.split("/");
                                    for (let i = 0; i < newLikesArray.length; i++) {
                                        const nameArray = newLikesArray[i].split('#')
                                        const userName = nameArray[0];
                                        const userId =  nameArray[1];
                                        const userProfileUrl = `/members/${userId}/profile`;
    
                                        whoLikedArray.push(`<a href=${userProfileUrl} target='_blank'>${userName}</a>`);
                                    }
    
                                    likeStatsLink.classList.remove("noLikes");
                                    whoLikedPostDiv.innerHTML = "<div class='names'>" + whoLikedArray.join(', ') + " liked this</div>";
                                } else {
                                    likeStatsLink.classList.add("noLikes");
                                    whoLikedPostDiv.innerHTML = "";
                                }
    
                                // console.warn(`Like removed for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                            }
                        })
                    } else {
                        addLike(threadId, postId, userId).then((res) => {
                            if (res.ok) {
                                const newLikeCount = Number(likeCount) + 1;
                                const newLikesString = [...likesListArray, nameAndId].filter(Boolean).join('/');
    
                                likeButton.dataset.count = `${newLikeCount}`;
                                likeButton.classList.add("userLiked");
                                likeButton.dataset.likesList = newLikesString;
                                likeStatsLink.innerHTML = `View Likes (${newLikeCount})`;
                                likeStatsLink.classList.remove("noLikes");
    
                                let whoLikedArray = [];
                                const newLikesArray = newLikesString.split("/");
                                for (let i = 0; i < newLikesArray.length; i++) {
                                    const nameArray = newLikesArray[i].split('#')
                                    const userName = nameArray[0];
                                    const userId =  nameArray[1];
                                    const userProfileUrl = `/members/${userId}/profile`;
    
                                    whoLikedArray.push(`<a href=${userProfileUrl} target='_blank'>${userName}</a>`);
                                }
    
                                whoLikedPostDiv.innerHTML = "<div class='names'>" + whoLikedArray.join(', ') + " liked this</div>";
    
                                // console.log(`Like recorded for forum thread '${threadId}' of post '${postId}' for user ${userId}`);
                            }
                        })
                    }
    
                    return false;
                };
            }
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

