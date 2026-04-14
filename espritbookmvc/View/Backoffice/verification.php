<?php
require_once "../../Model/Book.php";
require_once "../../Model/BookList.php";
require_once "../../Controller/BookController.php";

session_start();

if (!isset($_SESSION['bookList'])) {
    $_SESSION['bookList'] = new BookList();
}

$bookList = $_SESSION['bookList'];

if(
    isset($_POST['title']) &&
    isset($_POST['author']) &&
    isset($_POST['publicationDate']) &&
    isset($_POST['language']) &&
    isset($_POST['status']) &&
    isset($_POST['copies']) &&
    isset($_POST['category'])
){
    $book1 = new Book(
        $_POST['title'],
        $_POST['author'],
        $_POST['publicationDate'],
        $_POST['language'],
        $_POST['status'],
        $_POST['copies'],
        $_POST['category']
    );

    $bookList->addBook($book1);
    $_SESSION['bookList'] = $bookList;

    echo "<h2>var_dump()</h2>";
    var_dump($book1);

    $controller = new BookController();

    echo "<h2>Controller showBook()</h2>";
    $controller->showBook($book1);

    echo "<br><br>";
    echo "<a href='http://localhost/espritbookmvc/View/Backoffice/liste.php'>📚 Voir la liste des livres (" . $bookList->count() . ")</a>";
    echo " | ";
    echo "<a href='http://localhost/espritbookmvc/View/Backoffice/addBook.php'>➕ Ajouter un autre livre</a>";

} else {
    echo "<p>Données manquantes. <a href='http://localhost/espritbookmvc/View/Backoffice/addBook.php'>Retour au formulaire</a></p>";
}
?>