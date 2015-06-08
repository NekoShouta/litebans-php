<?php
if (isset($_POST['name'], $_POST['table'])) {
    $name = $_POST['name'];
    // validate user input
    if (strlen($name) > 16 || !preg_match("/[0-9a-zA-Z_]/", $name)) {
        echo('Invalid name.');
        return;
    }
    require 'includes/page.php';
    $name = $_POST['name'];

    $stmt = $conn->prepare("SELECT name,uuid FROM " . $table_history . " WHERE name=? ORDER BY date LIMIT 1");
    if ($stmt->execute(array($name))) {
        if ($row = $stmt->fetch()) {
            $name = $row['name'];
            $uuid = $row['uuid'];
        }
    }
    if (!isset($uuid)) {
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        echo($name . ' has not joined before.');
        return;
    }
    $stmt = $conn->prepare("SELECT * FROM " . $table_bans . " WHERE (uuid=? AND active=1) LIMIT 1");
    if ($stmt->execute(array($uuid))) {
        if (!($row = $stmt->fetch())) {
            echo($name . ' is not banned.');
            return;
        }
        $banner = get_banner_name($row['banned_by_name']);
        $reason = $row['reason'];
        $time = millis_to_date($row['time']);
        $until = millis_to_date($row['until']);
        echo($name . ' is banned! <br>');
        echo('Banned by: ' . $banner . '<br>');
        echo('Reason: ' . clean($reason) . '<br>');
        echo('Banned on: ' . $time . '<br>');
        if ($row['until'] > 0) {
            echo('Banned until: ' . $until . '<br>');
        } else {
            echo('Banned permanently.');
        }
    }
}
?>