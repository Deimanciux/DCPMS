import $ from 'jquery';
import * as FullCalendar from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

let reservation_id = $('#reservation_id');
let reservation_title = $('#reservation_title');
let reservation_startDate = $('#reservation_startDate');
let reservation_endDate = $('#reservation_endDate');
let reservation_service = $('#reservation_service');
let form = $('form[name="reservation"]')
let reservation_alert_danger = $('#reservation_alert');
let calendar_alert_danger = $('#calendar_alert');
let empty_value_message = ' can not be empty';

let calendarEl = document.getElementById('calendar');
let calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
    timeZone: 'Europe/Vilnius',
    initialView: 'timeGridWeek',
    selectable: true,
    editable: true,
    eventConstraint: 'businessHours',
    slotMinTime: "07:00:00",
    slotMaxTime: "22:00:00",
    scrollTimeReset: false,
    eventOverlap: function(stillEvent, movingEvent) {
        display_error(null, 'Reservations can not overlap', calendar_alert_danger);
        return false;
    },
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    // events: [
    //     {
    //         id: '1',
    //         title: 'The Title',
    //         start: '2022-04-13 10:20',
    //         end: '2022-04-14 10:20',
    //         service: 3
    //     }
    // ],
    businessHours: [
        {
            daysOfWeek: [ 1, 2, 3 ],
            startTime: '08:00',
            endTime: '18:00'
        },
    ],
    eventClick: function(info) {
        setValues(info.event);
        new bootstrap.Modal($('#exampleModal')).show();
    },

    eventDrop: function(info) {
        setValues(info.event);

        let newDate = new Date(info.event.start);
        if (newDate < Date.now()) {
            info.revert();
            display_error(null, 'Reservation can not be created in the past ', calendar_alert_danger);
            return;
        }

        sendReservationEditRequest(info.event);
    }
});

async function getReservationsByUser() {
    await $.ajax({
        method: "GET",
        url: "/reservations",
        dataType: 'json',
        success: function (response) {
            $.each(response.data, function(index, reservation) {
                calendar.addEvent(reservation)
            });
        },
        error: function (response) {
            console.log(response);
        }
    });
}

function sendReservationEditRequest(event) {
    $.ajax({
        method: "PUT",
        url: "/reservation/" + event.id,
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        data: JSON.stringify({
            title : event.title,
            service: event.extendedProps.service,
            start : event.start.toISOString(),
            end : event.end.toISOString(),
        }),
        success: function (response) {

        },
        error: function (response) {
            console.log(response);
        }
    });
}

async function sendReservationDeleteRequest(id) {
    await $.ajax({
        method: "DELETE",
        url: "/reservation/" + id,
        contentType: "application/json; charset=utf-8",
        success: function (response) {
            console.log(response)
        },
        error: function (response) {
        }
    });
}


function setValues(event) {
    reservation_id.val(event.id)
    reservation_title.val(event.title)
    reservation_startDate.val(event.start.toISOString().slice(0, 16))
    reservation_endDate.val(event.start.toISOString().slice(0, 16))
    reservation_service.val(event.extendedProps.service)
}

function clearValues() {
    reservation_id.val('')
    reservation_title.val('')
    reservation_startDate.val('')
    reservation_endDate.val('')
    reservation_service.val('')
}

$('#new-reservation').on('click', function () {
    clearValues();
})

$('#reservation_save').on('click', function (event) {
    event.preventDefault();

    if (reservation_title.val().trim().length === 0) {
        display_error('Title', empty_value_message, reservation_alert_danger);
        return;
    }

    if (reservation_startDate.val().trim().length === 0) {
        display_error('Start date', empty_value_message, reservation_alert_danger);
        return;
    }

    if (reservation_service.val() === null) {
        display_error('Service', empty_value_message, reservation_alert_danger);
        return;
    }

    form.submit();
});


$('#reservation_delete').on('click', async function (event) {
    event.preventDefault();

    await sendReservationDeleteRequest(reservation_id.val())
    form.submit();
});

function display_error(parameter_name, error, element) {
    element.css("display", "block");

    if (parameter_name !== null) {
        element.text(parameter_name + error)

        return;
    }

    element.text(error)
}

async function init() {
    await getReservationsByUser();
    calendar.render();
    reservation_startDate.attr('type', 'datetime-local');
    $('.title').addClass('p-0');
}

init();
