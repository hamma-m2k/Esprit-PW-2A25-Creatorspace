<?php

class BookController
{

    public function showBook($book)
    {
        echo "<h2>Book Information</h2>";
        $book->show();
    }

}

?>