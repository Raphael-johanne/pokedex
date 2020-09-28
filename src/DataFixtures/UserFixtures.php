<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Psr\Log\LoggerInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    /**
     * string File to import
     */
    const USER_SAMPLE = '/sample/user.csv';
    
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
        'email',
        'password'
    ];

    /**
     * @param string          $projectDir
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $projectDir, 
        LoggerInterface $logger
    ){
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

            $sample = $this->projectDir . self::USER_SAMPLE;

            if (!file_exists($sample) || !($csv = fopen($sample, 'r'))) {
                throw new NoFileException(self::USER_SAMPLE);
            }

            $i  = 0;

            while (!feof($csv)) {

                $line = fgetcsv($csv);

                if ($i === 0) {
                    if ($line !== $this->colsName) {
                        throw new \Exception('Columns does not match with the contract');
                    }
                } else {
                
                    if (!isset($line[1])) continue;

                    $item = new User();
                    $item->setEmail($line[0]);

                    $item->setPassword($line[1]);
                    $item->setRoles(["ROLE_USER"]);
                
                    $manager->persist($item);
                }

                $i++;
            }
    
            fclose($csv);

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
