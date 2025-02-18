<?php

require_once "base.php";

$ucid = "cle3"; // <-- set your ucid


$array1 = ["hello world!", "php programming", "special@#$%^&characters", "numbers 123 456", "mIxEd CaSe InPut!"];
$array2 = ["hello world", "php programming", "this is a title case test", "capitalize every word", "mixEd CASE input"];
$array3 = ["  hello   world  ", "php    programming  ", "  extra    spaces  between   words   ",
    "      leading and trailing spaces      ", "multiple      spaces"];
$array4 = ["hello world", "php programming", "short", "a", "even"];


function transformText($arr, $arrayNumber) {
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoBasic($arr, $arrayNumber);

    // Challenge 1: Remove non-alphanumeric characters except spaces
    // Challenge 2: Convert text to Title Case
    // Challenge 3: Trim leading/trailing spaces and remove duplicate spaces
    // Result 1-3: Assign final phrase to `$placeholderForModifiedPhrase`
    // Challenge 4 (extra credit): Extract middle 3 characters (beginning starts at middle of phrase),
    // assign to `$placeholderForMiddleCharacters`
    // if not enough characters assign "Not enough characters"

    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)
    $placeholderForModifiedPhrase = "";
    $placeholderForMiddleCharacters = "";
    foreach ($arr as $index => $text) {
        // Start Solution Edits

    // UCID: cle3
    // Date: 02/16/2025
    // I need to find a way to identify non-alphanumeric characters(except spaces) and remove them
    // I need to convert it to Title Text
    // I need to find out how I can trim leading/trailing spaces and remove duplicate spaces
    // I also need to assign final phrase to `$placeholderForModifiedPhrase`
    // I will not be attempting the extra credit, I do not understand how to and I probably need a few days to learn.

    //preg_replace changes specific characters, so it will allow us to change non-alphanumeric characters
    $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);

   //ucwords changes it to Title Text by capitilizing the first letter
    $text = ucwords(strtolower($text));

    //trimming
    $text = trim($text);

    //this allows me to remove duplicate spaces
    $text = preg_replace('/\s+/', ' ', $text);

    //assigning the completed output
    $placeholderForModifiedPhrase = $text;



        // End Solution Edits

        printStringTransformations($index, $placeholderForModifiedPhrase, $placeholderForMiddleCharacters);
    }

    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 4);
transformText($array1, 1);
transformText($array2, 2);
transformText($array3, 3);
transformText($array4, 4);
printFooter($ucid, 4);

?>