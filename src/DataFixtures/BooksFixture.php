<?php

namespace App\DataFixtures;


use App\Entity\Books;
use App\Entity\Author;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BooksFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $Book = new Books();
        $Book->setTitle('The life');
        $Book->setAuthor($this->getReference('Author_1' , Author::class));
        $Book->setISBN('asdsadjaslkjdlaksjdlsajldjasldsa');
        $Book->setGenre('horror');
        $Book->setPublicationDate(new DateTimeImmutable('1952-02-05'));
        $Book->setBookImage('asaasasasasaassasaa');
        $manager->persist($Book);

        $Book2 = new Books();
        $Book2->setTitle('The life');
        $Book2->setAuthor($this->getReference('Author_2' , Author::class));
        $Book2->setISBN('asdsadjaslkjdlaksjdlsajldjasldsa');
        $Book2->setGenre('philosophie');
        $Book2->setPublicationDate(new DateTimeImmutable('1933-02-12'));
        $Book2->setBookImage('asaasasasasaassasaa');
        $manager->persist($Book2);


        $manager->flush();
    }
}
