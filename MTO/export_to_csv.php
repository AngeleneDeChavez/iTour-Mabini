<?php
include '../connection/db_connection.php';

// Get the selected month if provided in the URL
$selected_month = isset($_GET['month']) ? $_GET['month'] : '';

// Prepare the SQL query
$sql = "SELECT 
            resort_id, report_month, resort_name, resort_type, no_rooms, total_guests, total_guest_nights, rooms_total, average_guest_per_night,
            average_room_occupancy_night, foreign_visitors, 
            this_municipality_male, this_province_male, other_provinces_male,this_municipality_female, this_province_female, other_provinces_female,
            this_municipality_total, this_province_total, other_provinces_total,foreign_country_male, 
            foreign_country_female, foreign_country_total, grand_total_visitors, 
            grand_total_male, grand_total_female
        FROM submitted_reports";

// Apply month filter if selected
if ($selected_month) {
    $sql .= " WHERE report_month = " . intval($selected_month);
}

// Execute the query and store the result
$result = $conn->query($sql);

// Check if data is returned
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Output headers to prompt download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tourism_report.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, [
    'Resort Name', 'Type/Class', 'Number of Rooms', 
    'Guests Check in (unit: visitors)', 'Guests staying overnight (Guest Nights)', 
    'Rooms Occupied (unit: rooms)', 'Average Guests/Night', 
    'Average Room Occupancy Rate', 'Total Foreign Visitors', 
    'This Municipality Male', 'This Municipality Female', 'This Municipality Total',
    'This Province Male', 'This Province Female', 'This Province Total',
    'Other Provinces Male', 'Other Provinces Female', 'Other Provinces Total',
    'Foreign Country Male', 'Foreign Country Female', 'Foreign Country Total',
    'Grand Total Male', 'Grand Total Female', 'Grand Total Visitors'
]);

// Fetch and write data rows
while ($row = $result->fetch_assoc()) {
    // Calculate the total values
    $this_municipality_total = $row['this_municipality_male'] + $row['this_municipality_female'];
    $this_province_total = $row['this_province_male'] + $row['this_province_female'];
    $other_provinces_total = $row['other_provinces_male'] + $row['other_provinces_female'];
    $foreign_country_total = $row['foreign_country_male'] + $row['foreign_country_female'];
    $grand_total_male = $row['this_municipality_male'] + $row['this_province_male'] + $row['other_provinces_male'];
    $grand_total_female = $row['this_municipality_female'] + $row['this_province_female'] + $row['other_provinces_female'];
    $grand_total_visitors = $grand_total_male + $grand_total_female;

    // Write the data row
    fputcsv($output, [
        $row['resort_name'], $row['resort_type'], $row['no_rooms'],
        $row['total_guests'], $row['total_guest_nights'], $row['rooms_total'], 
        $row['average_guest_per_night'], $row['average_room_occupancy_night'], 
        $row['foreign_visitors'], $row['this_municipality_male'], 
        $row['this_municipality_female'], $this_municipality_total,
        $row['this_province_male'], $row['this_province_female'], $this_province_total,
        $row['other_provinces_male'], $row['other_provinces_female'], $other_provinces_total,
        $row['foreign_country_male'], $row['foreign_country_female'], $foreign_country_total,
        $grand_total_male, $grand_total_female, $grand_total_visitors
    ]);
}

// Close the database connection
$conn->close();
exit();
?>
