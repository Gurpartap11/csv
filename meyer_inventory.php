<?php
// database credentials
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "partsrv";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

set_time_limit(0);
ini_set('memory_limit', '2048M');

// path to CSV file
$file = fopen('Meyer Inventory.csv', 'r');
if (!$file) {
    echo "Error opening CSV file: " . error_get_last()['message'];
    exit;
}

$result = mysqli_query($conn, "SELECT COUNT(*) FROM meyer_inventory");
$row = mysqli_fetch_row($result);
if ($row[0] == 0) { mysqli_query($conn, "ALTER TABLE meyer_inventory AUTO_INCREMENT = 1"); }

fgetcsv($file);
$sql = "INSERT INTO meyer_inventory (MFG_Code, MFG_Name, MFG_Item_Number, Item_Number, LTL, MFG_Dropship_Qty_Available, MFG_Dropship_Fee, Oversize, Addtl_Handling_Charge, Discontinued, Kit, UPC, ETA, Special_Order, Qty_013, Qty_032, Qty_041, Qty_049, Qty_053, Qty_058, Qty_061, Qty_062, Qty_063, Qty_064, Qty_065, Qty_069, Qty_071, Qty_072, Qty_092, Qty_094, Qty_098, Qty_108, Qty_119, Qty_125, Qty_130, Total_Qty, Parts_Link)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,'')";

$stmt = $conn->prepare($sql);

while (($data = fgetcsv($file)) !== false) { 
  // Loop through each value and bind null if empty
  foreach ($data as &$value) {
    if ($value === '') {
        $value = null;
    }
}
    $stmt->bind_param('ssssddddddssssddddsddddddddsssssiiiidd', ...$data);
    $stmt->execute(); 

    // Checking for errors
    if ($stmt->error) { 
        echo "Error inserting row: " . $stmt->error;
    } else {
        $last_id = $conn->insert_id;
        echo "Row inserted successfully with ID: " . $last_id . "<br>";
    }
}

// Closing the CSV file and database connection
fclose($file);
$stmt->close();
$conn->close();
?>
