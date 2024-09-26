<?php

namespace App\Tests\Controller;

use App\Entity\Voitures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class VoituresControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/voitures/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Voitures::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Voiture index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'voiture[marque]' => 'Testing',
            'voiture[modele]' => 'Testing',
            'voiture[km]' => 'Testing',
            'voiture[annee]' => 'Testing',
            'voiture[couleur]' => 'Testing',
            'voiture[boite]' => 'Testing',
            'voiture[carburant]' => 'Testing',
            'voiture[prix]' => 'Testing',
            'voiture[image]' => 'Testing',
            'voiture[agence]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Voitures();
        $fixture->setMarque('My Title');
        $fixture->setModele('My Title');
        $fixture->setKm('My Title');
        $fixture->setAnnee('My Title');
        $fixture->setCouleur('My Title');
        $fixture->setBoite('My Title');
        $fixture->setCarburant('My Title');
        $fixture->setPrix('My Title');
        $fixture->setImage('My Title');
        $fixture->setAgence('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Voiture');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Voitures();
        $fixture->setMarque('Value');
        $fixture->setModele('Value');
        $fixture->setKm('Value');
        $fixture->setAnnee('Value');
        $fixture->setCouleur('Value');
        $fixture->setBoite('Value');
        $fixture->setCarburant('Value');
        $fixture->setPrix('Value');
        $fixture->setImage('Value');
        $fixture->setAgence('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'voiture[marque]' => 'Something New',
            'voiture[modele]' => 'Something New',
            'voiture[km]' => 'Something New',
            'voiture[annee]' => 'Something New',
            'voiture[couleur]' => 'Something New',
            'voiture[boite]' => 'Something New',
            'voiture[carburant]' => 'Something New',
            'voiture[prix]' => 'Something New',
            'voiture[image]' => 'Something New',
            'voiture[agence]' => 'Something New',
        ]);

        self::assertResponseRedirects('/voitures/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getMarque());
        self::assertSame('Something New', $fixture[0]->getModele());
        self::assertSame('Something New', $fixture[0]->getKm());
        self::assertSame('Something New', $fixture[0]->getAnnee());
        self::assertSame('Something New', $fixture[0]->getCouleur());
        self::assertSame('Something New', $fixture[0]->getBoite());
        self::assertSame('Something New', $fixture[0]->getCarburant());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getAgence());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Voitures();
        $fixture->setMarque('Value');
        $fixture->setModele('Value');
        $fixture->setKm('Value');
        $fixture->setAnnee('Value');
        $fixture->setCouleur('Value');
        $fixture->setBoite('Value');
        $fixture->setCarburant('Value');
        $fixture->setPrix('Value');
        $fixture->setImage('Value');
        $fixture->setAgence('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/voitures/');
        self::assertSame(0, $this->repository->count([]));
    }
}
