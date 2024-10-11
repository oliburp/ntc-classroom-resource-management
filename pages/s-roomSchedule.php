<?php
$pageTitle = "View Room Schedule";
include "panel.php";

// Function to get rooms by building
function getRoomsByBuilding($conn, $building)
{
    $sql = "SELECT * FROM rooms WHERE room_code LIKE ?";
    $stmt = $conn->prepare($sql);
    $param = $building . '%';  // For building A, B, or C
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}
?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<main>
    <div class="mainRoomSchedule">
        <div class="buildingTabs">
            <div class="container mt-5">
                <ul class="nav nav-tabs" id="buildingTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="building-a-tab" data-toggle="tab" href="#building-a" role="tab" aria-controls="building-a" aria-selected="true">Building A</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="building-b-tab" data-toggle="tab" href="#building-b" role="tab" aria-controls="building-b" aria-selected="false">Building B</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="building-c-tab" data-toggle="tab" href="#building-c" role="tab" aria-controls="building-c" aria-selected="false">Building C</a>
                    </li>
                </ul>

                <div class="tab-content" id="buildingTabsContent">
                    <!-- Building A -->
                    <div class="tab-pane fade show active" id="building-a" role="tabpanel" aria-labelledby="building-a-tab">
                        <div class="row mt-3">
                            <?php
                            $roomsA = getRoomsByBuilding($conn, 'A');
                            while ($room = $roomsA->fetch_assoc()) {
                                echo '<div class="col-md-4">';
                                echo '<div class="card">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $room['room_code'] . '</h5>';
                                echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Building B -->
                    <div class="tab-pane fade" id="building-b" role="tabpanel" aria-labelledby="building-b-tab">
                        <div class="row mt-3">
                            <?php
                            $roomsB = getRoomsByBuilding($conn, 'B');
                            while ($room = $roomsB->fetch_assoc()) {
                                echo '<div class="col-md-4">';
                                echo '<div class="card">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $room['room_code'] . '</h5>';
                                echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Building C -->
                    <div class="tab-pane fade" id="building-c" role="tabpanel" aria-labelledby="building-c-tab">
                        <div class="row mt-3">
                            <?php
                            $roomsC = getRoomsByBuilding($conn, 'C');
                            while ($room = $roomsC->fetch_assoc()) {
                                echo '<div class="col-md-4">';
                                echo '<div class="card">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $room['room_code'] . '</h5>';
                                echo '<p class="card-text">' . $room['room_detail'] . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include "closing.php";
?>