<?php
$currpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
$note = @$_GET['note'];

// Default toastr message
$message = "";
$type = "";

if ($note === "error") {
    $message = "Error";
    $type = "error";
} elseif ($note === "invalid") {
    $message = "Invalid";
    $type = "error";
} elseif (in_array($note, ["added", "update", "delete"])) {
    // Map specific messages to toastr types
    $messages = [
        "added" => "Item Added",
        "update" => "Changes Saved",
        "delete" => "Item Removed",
    ];
    $message = $messages[$note];
    $type = "success";
}


if (!empty($message)) {
    echo "
        <script>
            toastr.$type(" . json_encode($message) . ");
        </script>
    ";
}

?>