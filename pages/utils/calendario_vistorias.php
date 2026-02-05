<title>Calendário de Vistorias</title>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<style>
  body {
    font-family: Arial, sans-serif;
    margin: 20px;
  }
  #calendar {
    max-width: 1000px;
    margin: auto;
  }
</style>
</head>

<body>

<h2>📅 Calendário de Vistorias</h2>

<div id="calendar"></div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'pt-br',
    height: 'auto',

    events: 'api_vistorias_calendario.php',

    eventClick: function(info) {
      alert(
        'Vistoria ID: ' + info.event.id +
        '\nProcesso: ' + info.event.title +
        '\nData: ' + info.event.start.toLocaleDateString('pt-BR')
      );
    }
  });

  calendar.render();
});
</script>
