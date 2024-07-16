<?php
include 'config.php';

if (isset($_GET['show_title'])) {
    $show_title = urldecode($_GET['show_title']);

    $stmt = $conn->prepare("SELECT click_count FROM show_clicks WHERE show_title = ?");
    $stmt->bind_param("s", $show_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE show_clicks SET click_count = click_count + 1 WHERE show_title = ?");
    } else {
        $stmt = $conn->prepare("INSERT INTO show_clicks (show_title, click_count) VALUES (?, 1)");
    }
    $stmt->bind_param("s", $show_title);
    $stmt->execute();
    $stmt->close();

    header("Location: episodes?show_title=" . urlencode($show_title));
} else {
    echo "No show title provided.";
}

$conn->close();
?>
