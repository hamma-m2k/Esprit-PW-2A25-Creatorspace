<?php

class Book {

    private $title;
    private $author;
    private $publicationDate;
    private $language;
    private $status;
    private $copies;
    private $category;

    // constructeur
    public function __construct($title,$author,$publicationDate,$language,$status,$copies,$category)
    {
        $this->title=$title;
        $this->author=$author;
        $this->publicationDate=$publicationDate;
        $this->language=$language;
        $this->status=$status;
        $this->copies=$copies;
        $this->category=$category;
    }

    // getters
    public function getTitle(){ return $this->title; }
    public function getAuthor(){ return $this->author; }
    public function getPublicationDate(){ return $this->publicationDate; }
    public function getLanguage(){ return $this->language; }
    public function getStatus(){ return $this->status; }
    public function getCopies(){ return $this->copies; }
    public function getCategory(){ return $this->category; }

    // setters
    public function setTitle($title){ $this->title=$title; }
    public function setAuthor($author){ $this->author=$author; }
    public function setPublicationDate($publicationDate){ $this->publicationDate=$publicationDate; }
    public function setLanguage($language){ $this->language=$language; }
    public function setStatus($status){ $this->status=$status; }
    public function setCopies($copies){ $this->copies=$copies; }
    public function setCategory($category){ $this->category=$category; }

    // méthode show
    public function show()
    {
        echo "<table border='1'>";
        echo "<tr><td>Title</td><td>".$this->title."</td></tr>";
        echo "<tr><td>Author</td><td>".$this->author."</td></tr>";
        echo "<tr><td>Publication Date</td><td>".$this->publicationDate."</td></tr>";
        echo "<tr><td>Language</td><td>".$this->language."</td></tr>";
        echo "<tr><td>Status</td><td>".$this->status."</td></tr>";
        echo "<tr><td>Copies</td><td>".$this->copies."</td></tr>";
        echo "<tr><td>Category</td><td>".$this->category."</td></tr>";
        echo "</table>";
    }

}

?>