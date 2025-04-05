<?php

namespace App\DataFixtures;

use App\Entity\Author;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $Author = new Author();
        $Author->setFirstName('Mohamed');
        $Author->setLastName('Amini');
        $Author->setBiography('He was doing some shit in his life)');
        $Author->setAuthorImage('232323232323232323s2d32s3d2s32ds32d11111111');
        $Author->setYearOfBirth(new DateTimeImmutable('2002-10-02'));
        $this->addReference('Author_1', $Author);
        $manager->persist($Author);


        $Author1 = new Author();
        $Author1->setFirstName('Mo2112121ham211212ed1');
        $Author1->setLastName('222112');
        $Author1->setBiography('He was doi12212112212121ng some shit in his life111)');
        $Author1->setAuthorImage('232323232323232323s2d32s3d2s32ds32d11111111');
        $Author1->setYearOfBirth(new DateTimeImmutable('2032-10-02'));
        $this->addReference('Author_2', $Author1);
        $manager->persist($Author1);




        $manager->flush();


    }
}
