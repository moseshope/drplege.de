const calendar = document.querySelector(".calendar");
const monthYear = document.querySelector(".month-year");
const prevButton = document.querySelector(".prev");
const nextButton = document.querySelector(".next");
const daysTable = document.querySelector(".days tbody");
const preMonthYear = document.querySelector(".pre-month-year");
const nextMonthYear = document.querySelector(".next-month-year");

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
let selectedDateOne = null;

// Function to generate the calendar table
function generateCalendar() {
  const daysInMonth = 32 - new Date(currentYear, currentMonth, 32).getDate();
  let firstDay = new Date(currentYear, currentMonth).getDay();
  firstDay = firstDay ? firstDay - 1 : 6;
  const row = document.createElement("tr");
  daysTable.innerHTML = "";
  row.innerHTML = "";

  for (let i = 0; i < firstDay; i++) {
    row.innerHTML += `<td><div></div></td>`;
  }
  let conditionCount = 1;
  for (let i = 1; i <= daysInMonth; i++) {
    const date2 = new Date();
    const date1 = new Date(`${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${String(i).padStart(2, "0")}`);
    row.innerHTML += `<td><div id="${currentYear}-${String(currentMonth + 1).padStart(2, "0")}-${i}" class="${date2.getDate() === i && date2.getFullYear() === currentYear && date2.getMonth() === currentMonth && "today"} ${date1 < date2 && "disable-date"}">${i}</div></td>`;

    if (firstDay + i === 7 || (i + firstDay) / conditionCount === 7 || i === daysInMonth) {
      daysTable.innerHTML += row.innerHTML;
      row.innerHTML = "";
      conditionCount++;
    }
  }
  monthYear.textContent = `${getMonthName(currentMonth)} ${currentYear}`;
  preMonthYear.textContent = `${getMonthName(currentMonth === 0 ? 11 : currentMonth - 1)} ${currentMonth === 0 ? currentYear - 1 : currentYear}`;
  nextMonthYear.textContent = `${getMonthName(currentMonth === 11 ? 0 : currentMonth + 1)} ${currentMonth === 11 ? currentYear + 1 : currentYear}`;
}

// Function to get the month name
function getMonthName(month) {
  const monthNames = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
  return monthNames[month];
}

let selectedDates = [];
// Function to handle date selection
function handleDateClick(event) {

  const date = event.target.textContent;
  const formattedDate = `${date.padStart(2, "0")}-${(currentMonth + 1).toString().padStart(2, "0")}-${currentYear}`;
  const date1 = new Date(`${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${date.padStart(2, "0")}`);
  const date2 = new Date();
  if (date1 < date2) return formattedDate;
  var selectedEl = document.querySelectorAll(".selected-date");
  
  if (selectedDateOne) {

    const selectedDate = [];
    for (let i = Math.min(selectedDateOne, date); i <= Math.max(selectedDateOne, date); i++) {
      document.getElementById(`${currentYear}-${String(currentMonth + 1).padStart(2, "0")}-${i}`).classList.add("selected-date");
      selectedDate.push(`${i.toString().padStart(2, "0")}-${(currentMonth + 1).toString().padStart(2, "0")}-${currentYear}`)
    }
    selectedDateOne = null;
    return selectedDate;

  } else {
    
    if (selectedEl) selectedEl.forEach(ele => ele.classList.remove("selected-date"));

    event.target.classList.add("selected-date");
    selectedDateOne = date ? Number(date) : null;
    return [formattedDate];

  }

}

// Function to navigate to the previous month
function prevMonth() {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  generateCalendar();
}

// Function to navigate to the next month
function nextMonth() {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  generateCalendar();
}

generateCalendar();

daysTable.addEventListener("click", handleDateClick);
