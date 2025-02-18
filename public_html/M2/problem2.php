<?php

require_once "base.php";

$ucid = "mt85"; // <-- set your ucid


$array1 = [0.1, 0.2, 0.3, 0.4, 0.5, 0.6];
$array2 = [1.0000001, 1.0000002, 1.0000003, 1.0000004, 1.0000005];
$array3 = [1.0 / 3.0, 2.0 / 3.0, 4.0 / 3.0, 8.0 / 3.0, 8.0 / 3.0];
$array4 = [1e16, 1.0, -1e16, 2.0, -2.0, 1e-16];
$array5 = [M_PI, M_E, sqrt(2), sqrt(3), sqrt(5), log(2), log10(3)];


function sumValues($arr, $arrayNumber)
{
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoDouble($arr, $arrayNumber);

    // Challenge 1: Sum all the values of the passed in array and assign to `total`
    // Challenge 2: Have the sum be represented as a number with exactly 2 decimal places, assign to `modifiedTotal`
    // Example: 0.1 would be shown as 0.10, 1 would be shown as 1.00, etc
    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)

    $total = 0;
    // Start Solution Edits
    // Solve Challenge 1 here

    // UCID: cle3
    // Date: 02/16/2025
    // I need to sum all the values in an array
    // I also need to have the result given as a 2 decimal float
    // I need to loop through the array and first sum all the values
    // Then I need to use some function to have the printed value as a float with 2 decimal places
    // I may use an empty array to store the summed values

    //First I need to sum all the values in the array and assign it to total
    foreach ($arr as $value) {
        $total += $value;
    }




    // Solve Challenge 2 here
    $modifiedTotal = "?";

    // Secondly I have to round it 2 decimal places, and assign it to `modifiedTotal`
    $modifiedTotal = number_format($total, 2, '.', '');


    // End Solution Edits
    echo "<p>Total Raw Value: {$total}</p>";
    echo "<p>Total Modified Value: {$modifiedTotal}</p>";
    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 2);
sumValues($array1, 1);
sumValues($array2, 2);
sumValues($array3, 3);
sumValues($array4, 4);
sumValues($array5, 5);
printFooter($ucid, 2);