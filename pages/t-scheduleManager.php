<?php
$pageTitle = "Schedule Manager";
include "panel.php";

function fetchRooms()
{
    global $conn;
    $sql = "SELECT * FROM rooms";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$rooms = fetchRooms();
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var smallCalendarEl = document.getElementById('small-calendar');
        var bigCalendarEl = document.getElementById('big-calendar');
        var addEventButton = document.getElementById('add-event-button');
        var addEventModal = document.getElementById('addEventModal');
        var startTimeInput = document.getElementById('start-time');
        var endTimeInput = document.getElementById('end-time');

        // Initialize the small dayGridMonth calendar
        var smallCalendar = new FullCalendar.Calendar(smallCalendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            height: 'auto',
            events: '../backend/fetch-events.php',
            dateClick: function(info) {
                // Sync small calendar with the big one
                bigCalendar.changeView('timeGridDay', info.dateStr);

                // Set the start and end time in the modal when a date is clicked
                setModalDateTime(info.date);
            },
            timeZone: 'UTC+8'
        });

        // Initialize the big timeGridDay calendar
        var bigCalendar = new FullCalendar.Calendar(bigCalendarEl, {
            initialView: 'timeGridDay',
            editable: true,
            selectable: true,
            events: '../backend/fetch-events.php',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek'
            },
            allDaySlot: false,
            eventClick: function(info) {
                $('#editEventModal').modal('show');

                document.getElementById('event-id').value = info.event.id;
                document.getElementById('edit-subject').value = info.event.title;
                document.getElementById('edit-start-time').value = new Date(info.event.start).toISOString().slice(0, 16);
                document.getElementById('edit-end-time').value = info.event.end ? new Date(info.event.end).toISOString().slice(0, 16) : new Date(info.event.start).toISOString().slice(0, 16);

                let roomSelect = document.getElementById('edit-room');
                let roomCode = info.event.extendedProps.room_code;

                for (let i = 0; i < roomSelect.options.length; i++) {
                    if (roomSelect.options[i].textContent == roomCode) {
                        roomSelect.options[i].selected = true;
                        break;
                    }
                }
            },
            dateClick: function(info) {
                setModalDateTime(info.date);
            },
            eventContent: function(arg) {
                let timeText = arg.timeText;
                return {
                    html: `<b>${arg.event.title}</b><br>${arg.event.extendedProps.room_code} ~ <i>${timeText}</i>`

                };
            },
            timeZone: 'UTC+8'
        });

        // Handle saving changes (update event)
        document.getElementById('save-event').addEventListener('click', function() {
            const eventId = document.getElementById('event-id').value;
            const subject = document.getElementById('edit-subject').value;
            const room_id = document.getElementById('edit-room').value;
            const start_time = document.getElementById('edit-start-time').value;
            const end_time = document.getElementById('edit-end-time').value;

            $.ajax({
                url: '../backend/update-event.php',
                method: 'POST',
                data: {
                    id: eventId,
                    subject: subject,
                    room_id: room_id,
                    start_time: start_time,
                    end_time: end_time
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert(res.success);
                        location.reload();
                    } else {
                        alert(res.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error: " + error);
                }
            });
            console.log({
                id: eventId,
                subject: subject,
                room_id: room_id,
                start_time: start_time,
                end_time: end_time
            });

        });


        // Handle deleting the event
        document.getElementById('delete-event').addEventListener('click', function() {
            const eventId = document.getElementById('event-id').value;

            if (confirm("Are you sure you want to delete this event?")) {
                $.ajax({
                    url: '../backend/delete-event.php',
                    method: 'POST',
                    data: {
                        id: eventId
                    },
                    success: function(response) {
                        alert("Event deleted successfully!");
                        location.reload(); // Reload the page after deleting the event
                    },
                    error: function(xhr, status, error) {
                        alert("Error: " + error);
                    }
                });
            }
        });


        // Function to show the modal and set the date/time inputs
        addEventButton.addEventListener('click', function() {
            addEventModal.style.display = 'block';
        });

        document.getElementById('addEventForm').onsubmit = function(e) {
            e.preventDefault();
            addEventToDatabase();
        };

        // Function to add event to database
        function addEventToDatabase() {
            const subject = document.getElementById('event-title').value;
            const room_id = document.getElementById('select-room').value;
            const start_time = document.getElementById('start-time').value;
            const end_time = document.getElementById('end-time').value;
            const user_id = document.getElementById('user-id').value;

            $.ajax({
                url: '../backend/add-event.php',
                method: 'POST',
                data: {
                    subject: subject,
                    room_id: room_id,
                    start_time: start_time,
                    end_time: end_time,
                    user_id: user_id
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert(res.success);
                        location.reload(); // Reload the page to reflect the new event
                    } else {
                        alert(res.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error: " + error);
                }
            });
        }

        // Helper function to set the modal date and time fields
        function setModalDateTime(selectedDate) {
            // Get the local time and format it to YYYY-MM-DDTHH:MM (for datetime-local input)
            var localDateStr = selectedDate.toISOString().slice(0, 16);

            var endDate = new Date(selectedDate);
            endDate.setHours(selectedDate.getHours() + 1);

            var endDateStr = endDate.toISOString().slice(0, 16);

            startTimeInput.value = localDateStr;
            endTimeInput.value = endDateStr;
        }


        smallCalendar.render();
        bigCalendar.render();
    });
</script>

<main>

    <div class="mainScheduleManager">
        <div class="left-schedule">
            <div id="small-calendar"></div>
            <button id="add-event-button">Create</button>
            <div id="addEventModal" style="display:none;">
                <form id="addEventForm">
                    <label for="event-title">Subject:</label>
                    <input type="text" id="event-title" name="subject" required>

                    <label for="select-room">Room:</label>
                    <select id="select-room" name="room_id" required>
                        <option value="" disabled selected>Room</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?php echo htmlspecialchars($room['room_id']); ?>"><?php echo htmlspecialchars($room['room_code']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Hidden input for user_id -->
                    <input type="hidden" id="user-id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                    <label for="start-time">Start Time:</label>
                    <input type="datetime-local" id="start-time" name="start_time" required>

                    <label for="end-time">End Time:</label>
                    <input type="datetime-local" id="end-time" name="end_time" required>

                    <button type="submit">Add Event</button>
                </form>
            </div>
        </div>
        <div id="big-calendar"></div>
        <!-- Edit Event Modal -->
        <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editEventForm">
                            <input type="hidden" id="event-id" name="id">

                            <label for="edit-subject">Subject:</label>
                            <input type="text" id="edit-subject" name="subject" class="form-control" required>

                            <label for="edit-room">Room:</label>
                            <select id="edit-room" name="room_id" class="form-control">
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['room_id']; ?>"><?php echo $room['room_code']; ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label for="edit-start-time">Start Time:</label>
                            <input type="datetime-local" id="edit-start-time" name="start_time" class="form-control" required>

                            <label for="edit-end-time">End Time:</label>
                            <input type="datetime-local" id="edit-end-time" name="end_time" class="form-control" required>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="delete-event">Delete</button>
                        <button type="button" class="btn btn-primary" id="save-event">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
include "closing.php";
?>