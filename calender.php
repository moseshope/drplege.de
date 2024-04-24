<html>

<head>
  <style>
  .calendar {
    width: 100%;
    margin: auto;
  }

  .calendar .header {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    user-select: none !important;
    font-size: var(--sm-text);
    font-weight: 700;
    color: var(--secondary);
    align-items: center;
    justify-content: space-between;
  }

  .calendar .header .pre-month-year {
    color: black;
  }

  .calendar .header .icon {
    background-color: transparent;
    color: var(--secondary);
    border: none;
    outline: none;
    border-radius: 4px;
    font-size: var(--md-text);
  }

  .calendar table {
    width: 100%;
    margin-top: 16px;
    margin-bottom: 16px;
    text-align: center;
  }

  .calendar thead {
    font-size: var(--md-text);
    color: var(--main);
    font-weight: 900;
  }

  .calendar td {
    font-size: var(--sm-text);
    font-weight: 900;
  }

  .calendar td div {
    margin: auto;
    cursor: pointer;
    height: 35px;
    width: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    padding: 4px;
  }

  .calendar td .selected-date {
    background-color: var(--main);
    color: white;
    border-radius: 50%;
    margin: auto;
  }

  .calendar .today {
    background-color: var(--secondary);
    color: white;
    border-radius: 50%;
    margin: auto;
  }

  .calendar td div:hover:not(.selected-date, .today) {
    background-color: #eaf2f8;

  }
  </style>
</head>

<body>
  <div class="calendar">
    <div class="header">
      <div class="cursor-pointer" onclick="prevMonth()">
        <span class="prev icon" style="color: black;">&lt;</span>
        <span class="pre-month-year"></span>
      </div>
      <div class="cursor-pointer" onclick="nextMonth()">
        <span class="month-year next"></span>
        <span class="next icon">></span>
      </div>
    </div>
    <table class="days">
      <thead>
        <tr>
          <th>S</th>
          <th>M</th>
          <th>D</th>
          <th>M</th>
          <th>D</th>
          <th>F</th>
          <th>S</th>
        </tr>
      </thead>
      <tbody id="calendarBody" onclick="getDate(event)"></tbody>
    </table>
  </div>

  <body>
    <script src="js/calender.js"></script>

</html>