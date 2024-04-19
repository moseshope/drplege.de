const handleNextPage = (currentPage, totalPage, elementId) => {
    let page = currentPage;
    if (currentPage < totalPage) page = page + 1;
    CreatePagination({ elementId, totalPage, currentPage: page });

    const searchParams = new URLSearchParams(window.location.search);
    searchParams.set("page", page);
    window.location.search = searchParams.toString();
}

const handlePreviousPage = (currentPage, totalPage, elementId) => {
    let page = currentPage;
    if (currentPage !== 1) page = page - 1;
    CreatePagination({ elementId, totalPage, currentPage: page });

    const searchParams = new URLSearchParams(window.location.search);
    searchParams.set("page", page);
    window.location.search = searchParams.toString();
}

const GoToPage = (currentPage, totalPage, elementId) => {
    CreatePagination({ elementId, totalPage, currentPage });
    const searchParams = new URLSearchParams(window.location.search);
    searchParams.set("page", currentPage);
    window.location.search = searchParams.toString();
}

function CreatePagination({ elementId, totalPage = 1, currentPage = 1 }) {
    const Pagination = document.getElementById(elementId);

    Pagination.innerHTML = `<li class="previous ${currentPage === 1 ? "disable-btn" : ""}" onClick="handlePreviousPage(${currentPage},${totalPage},'${elementId}')"><i class="fa-solid fa-chevron-left"></i></li>`
    for (let i = 0; i < totalPage; i++) {
        if (i === currentPage - 2 || i === currentPage - 1 || i === currentPage || (currentPage === 1 && i === 2) || (i === totalPage && i === totalPage - 2)) Pagination.innerHTML += `<li class='pages ${currentPage === i + 1 ? "active-page" : ""}' onClick="GoToPage(${i + 1},${totalPage},'${elementId}')">${i + 1}</li>`
    }
    Pagination.innerHTML += `<li class="next ${currentPage === totalPage ? "disable-btn" : ""}" onClick="handleNextPage(${currentPage},${totalPage},'${elementId}')"><i class="fa-solid fa-chevron-right"></i></li>`;

    return currentPage;
}