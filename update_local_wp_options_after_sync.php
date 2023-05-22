<?php

// WordPress database credentials (you may have to update port number if not connecting properly)
$from = null;
$to = null;
$db_name = null;
$db_user = null;
$db_password = null;
$db_host = null;
$db_port = null;

// Check if the required arguments are provided
if ($argc > 1) {
    // Loop through the arguments to find the values
    for ($i = 1; $i < $argc; $i++) {
        if (strpos($argv[$i], '--from=') === 0) {
            $from = substr($argv[$i], 7);
        } elseif (strpos($argv[$i], '--to=') === 0) {
            $to = substr($argv[$i], 5);
        } elseif (strpos($argv[$i], '--db_name=') === 0) {
            $db_name = substr($argv[$i], 10);
        } elseif (strpos($argv[$i], '--db_user=') === 0) {
            $db_user = substr($argv[$i], 10);
        } elseif (strpos($argv[$i], '--db_password=') === 0) {
            $db_password = substr($argv[$i], 14);
        } elseif (strpos($argv[$i], '--db_host=') === 0) {
            $db_host = substr($argv[$i], 10);
        } elseif (strpos($argv[$i], '--db_port=') === 0) {
            $db_port = substr($argv[$i], 10);
        }
    }
    // Use the values as needed
    if ($from !== null && $to !== null && $db_name !== null && $db_user !== null && $db_password !== null && $db_host !== null && $db_port !== null) {
        // Connect to the database
        $db_host_port = $db_host . ":" . $db_port;
        $mysqli = new mysqli($db_host_port, $db_user, $db_password, $db_name);

        // Check connection
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $mysqli->connect_error;
            exit;
        }
        // Update query
        $query = "UPDATE wp_options SET option_value = REPLACE(option_value, '" . $from . "', '" . $to . "')";
        // Execute the update query
        if ($mysqli->query($query) === TRUE && $from !== null && $to !== null) {
            echo "Update completed successfully.";
        } else {
            echo "Error updating database: " . $mysqli->error;
        }

        // Close the database connection
        $mysqli->close();
    } else {
        echo "Invalid arguments. Please provide --from and --to and --db_* values.";
    }
} else {
    echo "Insufficient arguments. Please provide --from and --to and --db_* values.";
}

?>
