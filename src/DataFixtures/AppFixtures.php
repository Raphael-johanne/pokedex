<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use App\Entity\Pokemon;
use App\Entity\Type;
use Psr\Log\LoggerInterface;
use App\Repository\TypeRepository;

class AppFixtures extends Fixture
{
    /**
     * string File to import
     */
    const POKEMON_SAMPLE = '/sample/pokemon.csv';
    
    /**
     * Root project directory
     */
    private $projectDir;

    /**
     * LoggerInterface Logger
     */
    private $logger;

    /**
     * array Mandatories columns
     */   
    protected $colsName = [
        '#',
        'Name',
        'Type 1',
        'Type 2',
        'Total',
        'HP',
        'Attack',
        'Defense',
        'Sp. Atk',
        'Sp. Def',
        'Speed',
        'Generation',
        'Legendary'
    ];

    /**
     * @param string          $projectDir
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $projectDir, 
        LoggerInterface $logger
    ) {
        $this->projectDir   = $projectDir;
        $this->logger       = $logger;
    }
    
    /**
     * load to the db
     * 
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        try {

            $sample = $this->projectDir . self::POKEMON_SAMPLE;

            if (!file_exists($sample) || !($csv = fopen($sample, 'r'))) {
                throw new NoFileException(self::POKEMON_SAMPLE);
            }

            $i              = 0;
            $date           = new \DateTime();
            $types          = $data = [];
            $existingTypes  = '';

            while (!feof($csv)) {

                $line = fgetcsv($csv);

                if ($i === 0) {
                    if ($line !== $this->colsName) {
                        throw new \Exception('Columns does not match with the contract');
                    }
                } else {
                
                    if (!isset($line[1])) continue;
                    
                    $data[] = $line;
                }

                $i++;
            }

            fclose($csv);

            /**
             * Proceed types installation
            */
            foreach ($data as $row) {
                foreach ([2,3] as $key) {
                    if ($row[$key] && !isset($types[$row[$key]])) {
                        $type = new Type();
                        $type->setName($row[$key]);
                        $type->setDate($date);
                        $types[$row[$key]] = $type;
                        $manager->persist($type);
                    }
                }
            }
            
            /**
             * Proceed pokemon installation
            */
            foreach ($data as $row) {
                $item = new Pokemon();
                    
                foreach ([2,3] as $key) {
                    if ($row[$key] && isset($types[$row[$key]])) {
                        $item->addType($types[$row[$key]]);
                    }
                }

                $item->setName($row[1])
                    ->setHp($row[4])
                    ->setAttack($row[5])
                    ->setDefense($row[6])
                    ->setSpAttack($row[7])
                    ->setSpDefense($row[8])
                    ->setSpeed($row[9])
                    ->setGeneration($row[11])
                    ->setLegendary($row[12])
                    ->setDate($date);
                
                $manager->persist($item);
            }
    
            $manager->flush();

        } catch (NoFileException $e) {
            $this->logger->error(
                'The file does not exist or the server has not the corrects rights to read it: ' . $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->error(
                'The Fixture encountered an error: ' . $e->getMessage()
            );
        }
    }
}
