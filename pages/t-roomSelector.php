<?php
$pageTitle = "Room Selector";
include "../backend/db_connection.php";

function fetchRooms()
{
    global $conn;
    $sql = "SELECT * FROM rooms";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get image based on room_detail
function getRoomImage($room_detail)
{
    switch ($room_detail) {
        case 'Television':
            return '../images/tv.png';
        case 'Whiteboard':
            return '../images/wb.png';
        case 'Projector':
            return '../images/projector.png';
        case 'Computer':
            return '../images/pc.png';
        default:
            return '../images/NTC-logo.png';
    }
}

// Check if a filter is applied
if (isset($_POST['filter'])) {
    // Handle the AJAX request for filtering rooms
    $filter = $_POST['filter'];

    // Modify the query based on the filter
    if ($filter == 'all') {
        $query = "SELECT * FROM rooms";
    } else {
        $query = "SELECT * FROM rooms WHERE room_detail = '$filter'";
    }

    $result = $conn->query($query);
    if (!$result) {
        die("Error fetching rooms: " . $conn->error);
    }

    // Return only the filtered rooms as HTML for the AJAX request
    while ($room = $result->fetch_assoc()) {
        $room_id = $room['room_id'];
        $room_code = $room['room_code'];
        $room_detail = $room['room_detail'];
        $room_status = $room['room_status'];
        $room_image = getRoomImage($room_detail);
?>
        <div class="room" id="room-<?php echo $room_id; ?>" onclick="selectRoom('<?php echo $room_id; ?>')">
            <img src="<?php echo $room_image; ?>" alt="Room Image">
            <p><?php echo $room_code; ?></p>
            <p>Status: <?php echo $room_status; ?></p>
        </div>
<?php
    }
    exit; // Stop script execution after returning the filtered HTML for AJAX
}

// Fetch all rooms for initial page load
$query = "SELECT * FROM rooms";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching rooms: " . $conn->error);
}

include "panel.php";
?>

<script>
    // Function to handle room selection
    function selectRoom(roomId) {
        // Here, you would send an AJAX request to fetch room-specific data based on the roomId
        const roomStatus = 'occupied'; // For now, we are using a static example

        if (roomStatus === 'occupied') {
            document.getElementById('subject-info').textContent = 'Mathematics';
            document.getElementById('year-course-info').textContent = '2nd Year, BSc IT';
            document.getElementById('date-info').textContent = 'September 29, 2024';
            document.getElementById('time-info').textContent = '10:00 AM - 12:00 PM';
        } else {
            // Clear info if the room is available
            document.getElementById('subject-info').textContent = '';
            document.getElementById('year-course-info').textContent = '';
            document.getElementById('date-info').textContent = '';
            document.getElementById('time-info').textContent = '';
        }
    }

    // Function to filter rooms
    function filterRooms(roomType) {
        // Use AJAX to send the filter request to the server
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "t-roomSelector.php", true); // Send request to the same PHP page
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // On success, update the room grid
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // Replace the room grid with the filtered rooms
                document.querySelector('.room-grid').innerHTML = xhr.responseText;
            }
        };

        // Send the filter type in the request
        xhr.send("filter=" + roomType);
    }
</script>

<main>

    <div class="mainRoomSelector">
        <div class="room-selector">
            <!-- Left side grid (70%) -->
            <div class="room-grid" style="width: 70%; float: left;">
                <?php
                // Loop through each room and display it (initial page load)
                while ($room = $result->fetch_assoc()) {
                    $room_id = $room['room_id'];
                    $room_code = $room['room_code'];
                    $room_detail = $room['room_detail'];
                    $room_status = $room['room_status'];
                    $room_image = getRoomImage($room_detail);
                ?>
                    <div class="room" id="room-<?php echo $room_id; ?>" onclick="selectRoom('<?php echo $room_id; ?>')">
                        <img src="<?php echo $room_image; ?>" alt="Room Image">
                        <p><?php echo $room_code; ?></p>
                        <p>Status: <?php echo $room_status; ?></p>
                    </div>
                <?php } ?>
            </div>

            <!-- Right side filter & room details (30%) -->
            <div class="room-info" style="width: 30%; float: right;">
                <!-- Filter Buttons -->
                <button onclick="filterRooms('all')">All</button>
                <button onclick="filterRooms('television')">Television</button>
                <button onclick="filterRooms('whiteboard')">Whiteboard</button>
                <button onclick="filterRooms('projector')">Projector</button>
                <button onclick="filterRooms('computer')">Computer</button>

                <!-- Room Information -->
                <div id="room-info">
                    <p><strong>Subject:</strong> <span id="subject-info"></span></p>
                    <p><strong>Year Level & Course:</strong> <span id="year-course-info"></span></p>
                    <p><strong>Date:</strong> <span id="date-info"></span></p>
                    <p><strong>Time:</strong> <span id="time-info"></span></p>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
include "closing.php";
?>