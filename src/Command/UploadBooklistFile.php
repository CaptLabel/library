<?php


namespace App\Command;


use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Kind;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UploadBooklistFile extends Command
{
    const COLUMN_NUMBER = 4;
    const FILE_PATH = __DIR__."/../../public/files/csv/booklist/";

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    protected static $defaultName = 'upload:booklist-file';

    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager

    )
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Upload a booklist file.')
            ->setHelp('This command allows you to upload a booklist file...')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = $this->getFiles();

        foreach ($files as $file) {
            $output->writeln([
                "========= start ==========",
                "[upload] {$file->getBasename()}"
            ]);

            if ("csv" !== $file->getExtension()) {
                $output->writeln([
                    "[warning] the file is not csv",
                    "========= end =========="
                ]);
                continue;
            }

            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                    $i++;
                    if ($i == 1) { continue; }
                    if (self::COLUMN_NUMBER !== count($data)) {
                        $output->writeln("[warning] there is a problem with the line {$i}");
                        continue;
                    }

                    $book = new Book();
                    $book->setTitle($data[0]);
                    $author = $this->createAuthor($data[1]);
                    $kind = $this->createKind($data[3]);
                    $book->setReleaseDate((int)$data[2]);
                    $book->setKind($kind);
                    $book->setAuthor($author);

                    $this->entityManager->persist($book);
                    $this->entityManager->flush();
                }
                fclose($handle);
            }

            $output->writeln([
                "========= end =========="
            ]);
        }

        return Command::SUCCESS;
    }

    /**
     * @return Finder
     */
    private function getFiles(): Finder
    {
        $finder = new Finder();

        return $finder->files()
            ->in(self::FILE_PATH)
        ;
    }

    /**
     * @param string $name
     * @return Author
     */
    private function createAuthor(string $name): Author
    {
        $author = $this->entityManager->getRepository(Author::class)->findOneBy([
            'name' => $name
        ]);
        if (!$author) {
            $author = new Author();
            $author->setName($name);
        }

        return  $author;
    }

    /**
     * @param string $label
     * @return Kind
     */
    private function createKind(string $label): Kind
    {
        $kind = $this->entityManager->getRepository(Kind::class)->findOneBy([
            'label' => $label
        ]);
        if (!$kind) {
            $kind = new Kind();
            $kind->setLabel($label);
        }

        return $kind;
    }
}