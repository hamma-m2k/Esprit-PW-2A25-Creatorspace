<?php

require_once "Book.php";

class BookList {

    private $books = [];

    public function addBook(Book $book) {
        $this->books[] = $book;
    }

    public function getBooks() {
        return $this->books;
    }

    public function count() {
        return count($this->books);
    }
}
?>