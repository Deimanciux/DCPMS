import $ from 'jquery';
import * as FullCalendar from "@fullcalendar/core";
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import 'jquery-datetimepicker';

let reservation_id = $('#reservation_id');
let reservation_reasonOfVisit = $('#reservation_reasonOfVisit');
let reservation_startDate = $('#reservation_startDate');
let reservation_startTime = $('#reservation_startTime');
let reservation_endDate = $('#reservation_endDate');
let reservation_service = $('#reservation_service');
let form = $('form[name="reservation"]')
let reservation_alert_danger = $('#reservation_alert');
let calendar_alert_danger = $('#calendar_alert');
let empty_value_message = ' can not be empty';
let calendar_container = $('#calendar');
let reservation_doctor = $('#reservation_doctor');
let reservation_patient = $('#reservation_user');
let reservation_doctor_option = $('#reservation_doctor option');
let reservation_delete = $('#reservation_delete');
let reservation_save = $('#reservation_save');
let new_reservation = $('#new-reservation');
let show_all_records = $('#show-all-records');
let data_calendar_user = new_reservation.attr('data-calendar-user');
let exampleModalLabel = $('#exampleModalLabel');
let times;
let date = new Date();

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
    businessHours: [
        {
            daysOfWeek: [ 1, 2, 3, 4, 5, 6 ],
            startTime: '08:00',
            endTime: '18:00'
        },
    ],
    eventClick: async function(info) {
        setValues(info.event);
        await getDoctorsByService(reservation_service.val())
        setValues(info.event);
        exampleModalLabel.text('Edit reservation');
        await hideFieldsDependingOnRole();
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
        url: "/reservations/user/" + data_calendar_user,
        dataType: 'json',
        success: function (response) {
            $.each(response.data, function(index, reservation) {
                calendar.addEvent(reservation)
            });
        },
        error: function (response) {
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
            service: event.extendedProps.service,
            start : event.start.toISOString(),
            end : event.end.toISOString(),
        }),
        success: function (response) {

        },
        error: function (response) {
        }
    });
}

async function sendReservationDeleteRequest(id) {
    await $.ajax({
        method: "DELETE",
        url: "/reservation/" + id,
        contentType: "application/json; charset=utf-8",
        success: function (response) {
        },
        error: function (response) {
        }
    });
}

async function getDoctorsByService(id) {
    await $.ajax({
        method: "GET",
        url: "/service/doctors/" + id,
        contentType: "application/json; charset=utf-8",
        success: function (response) {
            reservation_doctor.empty();

            if ( response['data'].length === 0) {
                reservation_doctor.parent().hide();
            } else {
                reservation_doctor.parent().show();
            }

            for (let i = 0; i < response['data'].length; i++) {
                reservation_doctor.append(new Option(response['data'][i].name_surname, response['data'][i].id));
            }
        },
        error: function (response) {
        }
    });
}

async function getAvailableTimes(date, doctor, service) {
    await $.ajax({
        method: "GET",
        url: '/reservation/time/' + date + '/doctor/' + doctor + '/service/' + service,
        contentType: "application/json; charset=utf-8",
        success: function (response) {
            console.log(response.data);
            times = response.data;

            $(reservation_startTime).datetimepicker({
                datepicker:false,
                format: 'H:i',
                allowTimes: times
            });
        },
        error: function (response) {
        }
    });
}

function setValues(event) {
    reservation_id.val(event.id);
    reservation_reasonOfVisit.val(event.extendedProps.reasonOfVisit);
    reservation_startDate.val(event.start.toISOString().slice(0, 10));
    reservation_endDate.val(event.start.toISOString().slice(0, 16));
    reservation_service.val(event.extendedProps.service);
    reservation_doctor.val(event.extendedProps.doctor);
    reservation_patient.val(event.extendedProps.patient);
    reservation_delete.css('display', 'block');

}

function clearValues() {
    reservation_id.val('');
    reservation_reasonOfVisit.val('');
    reservation_startDate.val('');
    reservation_endDate.val('');
    reservation_service.val('');
    reservation_doctor.parent().hide();
    reservation_patient.val('');
    reservation_delete.css('display', 'none');
    exampleModalLabel.text('Create new reservation');
}

new_reservation.on('click', async function (event) {
    clearValues();

    if (event.target.hasAttribute('data-service-id')) {
        reservation_service.val($(event.target).attr("data-service-id"));
        await getDoctorsByService(reservation_service.val())
        reservation_doctor.parent().show();
    }

    await hideFieldsDependingOnRole();
})

reservation_save.on('click', function (event) {
    event.preventDefault();
    reservation_alert_danger.css('display', 'none');

    if (reservation_reasonOfVisit.val().trim().length === 0) {
        display_error('Reason of visit', empty_value_message, reservation_alert_danger);
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

    if (reservation_doctor.val() === null) {
        display_error('Doctor', empty_value_message, reservation_alert_danger);
        return;
    }

    if (reservation_patient.val() === null) {
        display_error('Patient', empty_value_message, reservation_alert_danger);
        return;
    }

    let events = calendar.getEvents();
    for (let i = 0; i < events.length; i++) {
        if (events[i].extendedProps.service == reservation_service.val() && events[i].start > Date.now() && reservation_id.val() === '' && events[i].extendedProps.patient == reservation_patient.val()) {
            display_error(null, 'Only one reservation can be created per service', reservation_alert_danger);;
            return;
        }
    }



    form.submit();
});


reservation_delete.on('click', async function (event) {
    event.preventDefault();

    await sendReservationDeleteRequest(reservation_id.val())
    form.submit();
});

reservation_service.on('change', async function (event) {
    event.preventDefault();
    if ($.inArray('ROLE_DOCTOR', roles) !== -1 || $.inArray('ROLE_ADMIN', roles) !== -1) {
       return;
    }

    await getDoctorsByService(reservation_service.val())
});

function display_error(parameter_name, error, element) {
    element.css("display", "block");

    if (parameter_name !== null) {
        element.text(parameter_name + error)

        return;
    }

    element.text(error)
}

function checkIfFieldsShouldBeHidden() {
    if (reservation_id.val() === '' || reservation_doctor_option.length === 0) {
        reservation_doctor.parent().hide();
    } else {
        reservation_doctor.parent().show();
    }
}

async function init() {
    if (data_calendar_user !== undefined) {
        await getReservationsByUser();
    }

    if (calendar_container.length !== 0) {
        calendar.render();
    }

    checkIfFieldsShouldBeHidden();
    // reservation_startDate.attr('type', 'datetime-local');

    $(reservation_startDate).attr('autocomplete', 'off');
    $(reservation_startTime).attr('autocomplete', 'off');
    $(reservation_startDate).datetimepicker({
        timepicker:false,
        format: 'Y-m-d',
        minDate: date.setDate(date.getDate() + 1),
        onChangeDateTime: async function() {
            console.log(reservation_startDate.val());
            await getTimesByRole();
        }
    });

    $('.title').addClass('p-0');
}

async function getTimesByRole() {
    if ($.inArray('ROLE_DOCTOR', roles) !== -1 || $.inArray('ROLE_ADMIN', roles) !== -1) {
        await getAvailableTimes(reservation_startDate.val(), data_calendar_user, reservation_service.val())
    }

    if ($.inArray('ROLE_PATIENT', roles) !== -1) {
        await getAvailableTimes(reservation_startDate.val(), reservation_doctor.val(), reservation_service.val())
    }
}

init();

//--------------------------------------------------
let positions;
let rows =  $("tr");
let healthRecordTableContainer =  $('#health-record-table-container');
let modalHealthRecordId = $('#health_record_id');
let modalHealthRecordPosition = $('#health_record_position');
let modalHealthRecordDiagnosis = $('#health_record_diagnosis');
let modalHealthRecordNotes = $('#health_record_notes');
let modalHealthRecordUser = $('#health_record_user');
let patient;
let toothData = $('.tooth-data');
let roles;
let newHealthRecord = $('#new-health-record');
let doctor_services;

async function initHealthRecordPage() {
    await getPositions();
    addEventsOnTooth();
    patient = healthRecordTableContainer.attr('data-patient-id');
    modalHealthRecordUser.val(patient);
}

async function getPositions() {
    await $.ajax({
        method: "GET",
        url: "/positions",
        dataType: 'json',
        success: function (response) {
            positions = response.data;
        },
        error: function (response) {
        }
    });
}

async function getRoles(userId) {
    await $.ajax({
        method: "GET",
        url: "/user/roles/" + userId,
        dataType: 'json',
        success: function (response) {
            roles = response.data;
        },
        error: function (response) {
        }
    });
}

async function getServices(userId) {
    await $.ajax({
        method: "GET",
        url: "/service/doctor/" + userId,
        dataType: 'json',
        success: function (response) {
            doctor_services = response.data;
        },
        error: function (response) {
        }
    });
}

async function getHealthRecordTemplateByTooth(position) {
    await $.ajax({
        method: "GET",
        url: "/health-records/" + position,
        dataType: 'json',
        success: function (response) {
            healthRecordTableContainer.empty();
            healthRecordTableContainer.append(response.data)
        },
        error: function (response) {
        }
    });
}

show_all_records.on('click', async function () {
   await getHealthRecordTemplateByPatient(patient)
})

async function getHealthRecordTemplateByPatient(patient) {
    await $.ajax({
        method: "GET",
        url: "/health-records/patient/" + patient,
        dataType: 'json',
        success: function (response) {
            healthRecordTableContainer.empty();
            healthRecordTableContainer.append(response.data);
            addEventListeners();
        },
        error: function (response) {
        }
    });
}

async function getHealthRecordTemplateByPositionAndUser(patient, position) {
    await $.ajax({
        method: "GET",
        url: "/health-records/patient/" + patient + "/position/" + position,
        dataType: 'json',
        success: function (response) {
            roles = response.roles;
            healthRecordTableContainer.empty();
            healthRecordTableContainer.append(response.data)
        },
        error: function (response) {
        }
    });
}

async function deleteHealthRecord(healthRecordId) {
    await $.ajax({
        method: "DELETE",
        url: "/health-record/delete/" + healthRecordId,
        dataType: 'json',
        success: function (response) {

        },
        error: function (response) {
        }
    });
}


function getHealthRecord(id) {
    $.ajax({
        method: "GET",
        url: "/health-record/" + id,
        contentType: "application/json; charset=utf-8",
        dataType: 'json',
        success: function (response) {
            setHealthRecordValues(response.data)
        },
        error: function (response) {
        }
    });
}

function addEventsOnTooth() {
    for(let i=0; i<positions.length; i++) {
        rows.find("[data-position-number='" + positions[i].position + "']").on('click', async function (event) {
            if (event.target.tagName === "IMG") {
                removeStyleFromToothData()
                $(this).closest("[data-position-number]").css('backgroundColor', "#f7f7f9");
            } else {
                removeStyleFromToothData()
                $(event.target).css('backgroundColor', "#f7f7f9");
            }
            makeActionsAfterToothClick(positions[i])
        })
        rows.find("[data-position-image='" + positions[i].position + "']").on('click', async function () {
            makeActionsAfterToothClick(positions[i])
        })
    }
}

async function makeActionsAfterToothClick(position) {
    await getHealthRecordTemplateByPositionAndUser(patient, position.position);

    if ($.inArray('ROLE_DOCTOR', roles) !== -1 || $.inArray('ROLE_ADMIN', roles) !== -1) {
        modalHealthRecordPosition.val(position.id);
        new bootstrap.Modal($('#healthRecordModal')).show();
    }

}

$(".health-record-delete-action").on('click', async function(event) {
    if (event.target.getAttribute('data-record-id') !== null) {
        await deleteHealthRecord(event.target.getAttribute('data-record-id'));
        await getHealthRecordTemplateByPatient(patient);
    }
});

$(".health-record-edit-action").on('click', async function(event) {
    if (event.target.getAttribute('data-record-id') !== null) {
        await getHealthRecord(event.target.getAttribute('data-record-id'))
        new bootstrap.Modal($('#healthRecordModal')).show();
        await getHealthRecordTemplateByPatient(patient);
    }
});

function removeStyleFromToothData () {
    for (let i=0; i<toothData.length; i++) {
        $(toothData[i]).css('backgroundColor', "");
    }
}

function addEventListeners() {
    $(".health-record-delete-action").on('click', async function(event) {
        if (event.target.getAttribute('data-record-id') !== null) {
            await deleteHealthRecord(event.target.getAttribute('data-record-id'));
            await getHealthRecordTemplateByPatient(patient);
        }
    });

    $(".health-record-edit-action").on('click', async function(event) {
        if (event.target.getAttribute('data-record-id') !== null) {
            await getHealthRecord(event.target.getAttribute('data-record-id'))
            new bootstrap.Modal($('#healthRecordModal')).show();
            await getHealthRecordTemplateByPatient(patient);
        }
    });
}

newHealthRecord.on('click', async function () {
   clearHealthRecordValues();
})


function setHealthRecordValues(data) {
    modalHealthRecordId.val(data.id);
    modalHealthRecordPosition.val(data.position);
    modalHealthRecordDiagnosis.val(data.diagnosis);
    modalHealthRecordNotes.val(data.notes);
    modalHealthRecordUser.val(data.user);
}

function clearHealthRecordValues() {
    modalHealthRecordId.val('');
    modalHealthRecordPosition.val('');
    modalHealthRecordDiagnosis.val('');
    modalHealthRecordNotes.val('');
}

async function hideFieldsDependingOnRole() {
    await getRoles(data_calendar_user);

    if ($.inArray('ROLE_DOCTOR', roles) !== -1 || $.inArray('ROLE_ADMIN', roles) !== -1) {
        reservation_doctor.val(data_calendar_user);
        reservation_doctor.parent().css("display", "none");
        reservation_service.empty();
        await getServices(data_calendar_user);

        if (doctor_services.length === 0) {
            display_error(null, 'Assign yourself to at least one service', reservation_alert_danger);
            reservation_service.parent().css("display", "none");
            reservation_save.css("display", "none");
        }

        if (doctor_services !== undefined) {
            for (let i = 0; i < doctor_services.length; i++) {
                reservation_service.append(new Option(doctor_services[i].title, doctor_services[i].id));
            }
        }
    }

    if ($.inArray('ROLE_PATIENT', roles) !== -1) {
        reservation_patient.val(data_calendar_user);
        reservation_patient.parent().css('display', 'none');
    }
}

initHealthRecordPage();
