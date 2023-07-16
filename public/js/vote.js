VoteCountBLock = document.querySelector('.article-detail-arrows p');
UpButton = document.querySelector('.article-detail-arrows .fa-arrow-up');
DownButton = document.querySelector('.article-detail-arrows .fa-arrow-down');
const slug = location.pathname.split('/')[2]

UpButton.addEventListener("click", () => {
    fetch(location.pathname + "/vote/up", {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            'slug': slug
        }),
    }).then((response) => {
        response.json().then(json => {
            changeCountValue(json.count)

        })
    })
});

DownButton.addEventListener("click", () => {
    fetch(location.pathname + "/vote/down", {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            'slug': slug
        }),
    }).then((response) => {
        response.json().then(json => {
            changeCountValue(json.count)

        })
    })
});

function changeCountValue(value) {
    VoteCountBLock.textContent = value
}