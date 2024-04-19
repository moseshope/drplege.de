function HandleDropMenu(id) {
    const menu = document.getElementById(id);
    if (menu.style.display === "block") return menu.style.display = "none";
    return menu.style.display = "block"
}
document.addEventListener("click", function (event) {
    var dropdowns = document.getElementsByClassName("dropdown");
    for (var i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i].querySelector(".dropdown-content");
        if (!dropdowns[i].contains(event.target)) {
            openDropdown.style.display = "none"
        }
    }
});

function showPassword(id) {
    const input = document.getElementById(id);
    if (input.type === "text") {
        input.type = "password";
    } else {
        input.type = "text"
    }
}

function handleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.style.display === "none" || !sidebar.style.display) {
        sidebar.style.display = "block"
    } else {
        sidebar.style.display = "none"
    }
}

function handlePageMenu() {
    const pageMenu = document.getElementById("nav-menu-item-list");
    if (!pageMenu.style.display || pageMenu.style.display === 'none') {
        pageMenu.style.display = "block";
    } else {
        pageMenu.style.display = "none";
    }
}

//toast
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.classList.add('toast');
    if (type === "warning") toast.classList.add('toast-warning');
    if (type === "error") toast.classList.add('toast-danger');
    if (type === "success") toast.classList.add('toast-success');
    toast.innerHTML = `
      <span class="close" onclick="this.parentElement.classList.remove('show')">&times;</span>
      <div class="message">${message}</div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }, 100);
}
//Select
const handleSelect = (id) => {
    const SelectInput = document.getElementById(id);
    SelectInput.value = this.event.target.value;
}

document.addEventListener('focusin', () => {
    const SelectOption = document.getElementById('Services-Options');
    const SelectInput = document.getElementById('Services-input');
    const statusSelect = document.getElementById('Status-input');
    const statusOption = document.getElementById('Status-Options');
    const doctorInput = document.getElementById('doctor-input');
    const doctorOption = document.getElementById('doctor-Options');

    if (statusSelect && statusOption && !statusSelect.matches(':focus') && !statusOption.matches(':focus')) statusOption.style.display = 'none';
    if (SelectInput && SelectOption && !SelectInput.matches(':focus') && !SelectOption.matches(':focus')) SelectOption.style.display = 'none';
    if (doctorOption && doctorInput && !doctorInput.matches(':focus') && !doctorOption.matches(':focus')) doctorOption.style.display = 'none';
    if (statusSelect && statusSelect.matches(':focus')) statusOption.style.display = 'block';
    if (SelectInput && SelectInput.matches(':focus')) SelectOption.style.display = 'block';
    if (doctorInput && doctorInput.matches(':focus')) doctorOption.style.display = 'block';


    //Edit Staff
    const SelectOptionEdit = document.getElementById('Services-Options-E');
    const SelectInputEdit = document.getElementById('Services-input-E');
    const statusSelectEdit = document.getElementById('Status-input-E');
    const statusOptionEdit = document.getElementById('Status-Options-E');


    if (statusSelectEdit && statusOptionEdit && !statusSelectEdit.matches(':focus') && !statusOptionEdit.matches(':focus')) statusOptionEdit.style.display = 'none';
    if (SelectInputEdit && SelectOptionEdit && !SelectInputEdit.matches(':focus') && !SelectOptionEdit.matches(':focus')) SelectOptionEdit.style.display = 'none';
    if (statusSelectEdit && statusSelectEdit.matches(':focus')) statusOptionEdit.style.display = 'block';
    if (SelectInputEdit && SelectInputEdit.matches(':focus')) SelectOptionEdit.style.display = 'block';
});