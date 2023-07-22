<?php

namespace App\Test\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private BlogRepository $repository;
    private string $path = '/blog/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Blog::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Blog index');

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
            'blog[title]' => 'Testing',
            'blog[smallDescription]' => 'Testing',
            'blog[createdAt]' => 'Testing',
            'blog[content]' => 'Testing',
            'blog[tags]' => 'Testing',
            'blog[coverImage]' => 'Testing',
        ]);

        self::assertResponseRedirects('/blog/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Blog();
        $fixture->setTitle('My Title');
        $fixture->setSmallDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setContent('My Title');
        $fixture->setTags('My Title');
        $fixture->setCoverImage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Blog');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Blog();
        $fixture->setTitle('My Title');
        $fixture->setSmallDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setContent('My Title');
        $fixture->setTags('My Title');
        $fixture->setCoverImage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'blog[title]' => 'Something New',
            'blog[smallDescription]' => 'Something New',
            'blog[createdAt]' => 'Something New',
            'blog[content]' => 'Something New',
            'blog[tags]' => 'Something New',
            'blog[coverImage]' => 'Something New',
        ]);

        self::assertResponseRedirects('/blog/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getSmallDescription());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getTags());
        self::assertSame('Something New', $fixture[0]->getCoverImage());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Blog();
        $fixture->setTitle('My Title');
        $fixture->setSmallDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setContent('My Title');
        $fixture->setTags('My Title');
        $fixture->setCoverImage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/blog/');
    }
}
