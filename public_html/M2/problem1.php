<?php

require_once "base.php";

$ucid = "cle3"; // <-- set your ucid

$array1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
$array2 = [9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
$array3 = [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9];
$array4 = [9, 9, 8, 8, 7, 7, 6, 6, 5, 5, 4, 4, 3, 3, 2, 2, 1, 1, 0, 0];

function printOdds($arr, $arrayNumber)
{
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfo($arr, $arrayNumber);

    // Challenge: Print odd values only in a single line separated by commas
    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)

    echo "Output Array: ";
    // Start Solution Edits
    
    // UCID: cle3
    // Date: 02/16/2025
    // My goal is to print odd values only in a single line separated by comma's
    // To do this I need to have find a way to detect odd values
    // I also need a way to store those values
    // Finally I need to print said values like the problem wants me to
    // I may need a loop to iterate through the array

    //Initializing Empty Array to hold Odd Value's
    $oddArray = [];

    //I am looping through the array looking for odd values
    
    for ($a = 0; $a < count($arr); $a++){
        if ($arr[$a] % 2 !== 0){
            $oddArray[] = $arr[$a];
        }
    }

    // I am printing the odd values separated by commas

    echo implode(", ", $oddArray);


    // End Solution Edits
    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 1);
printOdds($array1, 1);
printOdds($array2, 2);
printOdds($array3, 3);
printOdds($array4, 4);
printFooter($ucid, 1);