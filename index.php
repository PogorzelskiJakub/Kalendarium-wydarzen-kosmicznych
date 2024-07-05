<?php
require("session.php");
require("db.php");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalendarz Wydarzeń Kosmicznych</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .menu {
            display: flex;
            justify-content: space-between;
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
        }
        .menu a {
            text-decoration: none;
            color: black;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .menu a:hover {
            background-color: #ddd;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    
    <div id='calendar'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: 'fetch_events.php',
                eventClick: function(info) {
                    window.location.href = 'details.php?id=' + info.event.id;
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
