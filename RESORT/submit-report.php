<?php
session_start();
include '../connection/db_connection.php';



// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id']; // This is your logged-in user's ID



// Ensure that required POST data exists (optional validation)
if (!isset($_POST['resort_id'], $_POST['resort_name'], $_POST['resort_type'], $_POST['report_month'], $_POST['no_rooms'], $_POST['total_guests'], $_POST['total_guest_nights'], 
$_POST['rooms_total'], $_POST['average_guest_per_night'], $_POST['average_room_occupancy_night'],
 $_POST['foreign_visitors'], $_POST['this_municipality_male'], $_POST['this_province_male'], $_POST['other_provinces_male'], 
 $_POST['this_municipality_female'], $_POST['this_province_female'], $_POST['other_provinces_female'], $_POST['this_municipality_total'], 
 $_POST['this_province_total'], $_POST['other_provinces_total'],$_POST['foreign_country_male'], $_POST['foreign_country_female'], 
 $_POST['foreign_country_total'], $_POST['grand_total_visitors'], $_POST['grand_total_male'], $_POST['grand_total_female'])) {
    die("Required data is missing.");

    
}

// Prepare the insert statement
$insert_query = "
    INSERT INTO submitted_reports (
        resort_id, report_month, resort_name, resort_type, no_rooms, total_guests, total_guest_nights, rooms_total, average_guest_per_night,
        average_room_occupancy_night, foreign_visitors, 
        this_municipality_male, this_province_male, other_provinces_male,this_municipality_female, this_province_female, other_provinces_female,
        this_municipality_total, this_province_total, other_provinces_total,foreign_country_male, 
        foreign_country_female, foreign_country_total, grand_total_visitors, 
        grand_total_male, grand_total_female
    ) VALUES (?,?,?,?,?,?,?,?,?,? ,?,?,?,?,?,?,?,?,?,? ,?,?,?,?,?,?)
";


// Prepare the statement using the database connection
$stmt = $conn->prepare($insert_query);

// Bind parameters to the prepared statement
$stmt->bind_param(
    'isssiiiiddiiiiiiiiiiiiiiii',
    $_POST['resort_id'],
    $_POST['report_month'],

    $_POST['resort_name'],
    $_POST['resort_type'],
    $_POST['no_rooms'],
    $_POST['total_guests'],
    $_POST['total_guest_nights'],


    $_POST['rooms_total'],
    $_POST['average_guest_per_night'],
   

    $_POST['average_room_occupancy_night'],
    $_POST['foreign_visitors'],
    $_POST['this_municipality_male'],

    $_POST['this_province_male'],
    $_POST['other_provinces_male'],
    $_POST['this_municipality_female'],
    $_POST['this_province_female'],
    $_POST['other_provinces_female'],

    $_POST['this_municipality_total'],
    $_POST['this_province_total'],
    $_POST['other_provinces_total'],
    $_POST['foreign_country_male'],
    $_POST['foreign_country_female'],

    $_POST['foreign_country_total'],
    $_POST['grand_total_visitors'],
    $_POST['grand_total_male'],
    $_POST['grand_total_female']
);

// Execute the query and check for success
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Report submitted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error submitting report: " . $stmt->error]);
}


// Close the statement and connection
$stmt->close();
$conn->close();
?>
