<?php
require_once "../../Model/Book.php";
require_once "../../Model/BookList.php";
require_once "../../Controller/BookController.php";

session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Liste des Livres</title>
</head>
<body>

<h1>📚 Liste des Livres</h1>

<?php
if (!isset($_SESSION['bookList']) || $_SESSION['bookList']->count() === 0) {
    echo "<p>Aucun livre enregistré.</p>";
} else {
    $bookList = $_SESSION['bookList'];
    $controller = new BookController();

    echo "<p><strong>" . $bookList->count() . " livre(s) enregistré(s)</strong></p>";

    foreach ($bookList->getBooks() as $index => $book) {
        echo "<h3>Livre #" . ($index + 1) . "</h3>";
        $controller->showBook($book);
        echo "<br>";
    }
}
?>

<br>
<a href="http://localhost/espritbookmvc/View/Backoffice/addBook.php">➕ Ajouter un nouveau livre</a>

</body>
</html>