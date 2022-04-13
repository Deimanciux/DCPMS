import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

let calendarEl = document.getElementById('calendar');
let calendar = new Calendar(calendarEl, {
    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
    initialView: 'timeGridWeek',
    selectable: true,
    editable: true,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    events: [
        { // this object will be "parsed" into an Event Object
            title: 'The Title', // a property!
            start: '2022-04-13', // a property!
            end: '2022-04-04' // a property! ** see important note below about 'end' **
        }
    ]
});

calendar.render();
