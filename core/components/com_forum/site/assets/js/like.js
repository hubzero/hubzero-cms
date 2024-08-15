console.log("like on site forum");

window.addEventListener('DOMContentLoaded', (domEvent) => {
    // Find all the "like" button
    const likeButton = document.querySelectorAll('.comment-options .like')
    if (likeButton.length) {
        for(let i = 0; i < likeButton.length;i++) {
            likeButton[i].onclick = (e) => {
                e.preventDefault();

                const dataId = likeButton[i].dataset.id
                console.log("data id: " + dataId);

                return false;
            };
        }
    }
});