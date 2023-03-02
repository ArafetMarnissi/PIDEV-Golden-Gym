<?php

namespace App\Test\Controller;

use App\Entity\TraitementReclamation;
use App\Repository\TraitementReclamationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TraitementReclamationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TraitementReclamationRepository $repository;
    private string $path = '/traitement/reclamation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(TraitementReclamation::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TraitementReclamation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'traitement_reclamation[dateReponse]' => 'Testing',
            'traitement_reclamation[DescriptionReponse]' => 'Testing',
            'traitement_reclamation[reclamation]' => 'Testing',
        ]);

        self::assertResponseRedirects('/traitement/reclamation/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new TraitementReclamation();
        $fixture->setDateReponse('My Title');
        $fixture->setDescriptionReponse('My Title');
        $fixture->setReclamation('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TraitementReclamation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new TraitementReclamation();
        $fixture->setDateReponse('My Title');
        $fixture->setDescriptionReponse('My Title');
        $fixture->setReclamation('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'traitement_reclamation[dateReponse]' => 'Something New',
            'traitement_reclamation[DescriptionReponse]' => 'Something New',
            'traitement_reclamation[reclamation]' => 'Something New',
        ]);

        self::assertResponseRedirects('/traitement/reclamation/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateReponse());
        self::assertSame('Something New', $fixture[0]->getDescriptionReponse());
        self::assertSame('Something New', $fixture[0]->getReclamation());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new TraitementReclamation();
        $fixture->setDateReponse('My Title');
        $fixture->setDescriptionReponse('My Title');
        $fixture->setReclamation('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/traitement/reclamation/');
    }
}
