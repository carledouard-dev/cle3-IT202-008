<?php
// UCID: cle3 | Date: 2025-05-08
// Desc: Reusable form render helpers

function render_input($field)
{
    $type = $field["type"] ?? "text";
    $id = $field["id"] ?? $field["name"] ?? "";
    $name = $field["name"] ?? "";
    $label = $field["label"] ?? ucfirst($name);
    $value = $field["value"] ?? "";
    $rules = $field["rules"] ?? [];

    echo "<label for='$id'>$label</label>";
    echo "<input type='$type' id='$id' name='$name' ";

    foreach ($rules as $attr => $val) {
        if (is_bool($val)) {
            if ($val) echo "$attr ";
        } else {
            echo "$attr='$val' ";
        }
    }

    echo "value=\"" . htmlspecialchars($value) . "\" />";
}

function render_button($config = [])
{
    $text = $config["text"] ?? "Submit";
    $type = $config["type"] ?? "submit";
    $class = $config["class"] ?? "btn btn-primary";

    echo "<button type='$type' class='$class'>$text</button>";
}