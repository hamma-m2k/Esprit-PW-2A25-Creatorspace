<?php

require_once "../../Model/Book.php";

$book1 = new Book(
    "Clean Code",
    "Robert Martin",
    "2008",
    "English",
    "Available",
    5,
    "Programming"
);

echo "<h2>var_dump()</h2>";
var_dump($book1);

echo "<h2>show()</h2>";
$book1->show();

?>