import * as FullCalendar from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

let calendarEl = document.getElementById('calendar');
let calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
    initialView: 'timeGridWeek',
    selectable: true,
    editable: true,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    //initial data
    events: [
        {
            id: '1',
            title: 'The Title',
            start: '2022-04-13 10:20',
            end: '2022-04-14 10:20'
        }
    ]
});

calendar.render();
//custom data
calendar.addEvent({
    title: 'Event1',
    start: '2022-04-19'
});

document.getElementById('reservation_datetime').setAttribute('type', 'datetime-local')
