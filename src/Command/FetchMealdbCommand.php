<?php

namespace App\Command;

use App\Entity\Ingredient;
use App\Entity\Measure;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Tag;
use mysql_xdevapi\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FetchMealdbCommand extends Command
{
    protected static $defaultName = 'app:fetch-mealdb';

    protected function configure()
    {
        $this
            ->setDescription('Command for fetching and persisting random recipe from MealDB')
        ;
    }

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->container->get('doctrine')->getManager();

        $setmeasures = false;
        if ($setmeasures) {
            $marr = array("unit", "g", "tbsp", "tsp", "cup", "ml", "pinch", "handful", "shot", "oz", "lbs");
            foreach ($marr as $m) {
                $meas = new Measure();
                $meas->setTitle($m);
                $entityManager ->persist($meas);
            }
            $entityManager->flush();
        }

        $extraction = file_get_contents(
            'https://raw.githubusercontent.com/LeaVerou/forkgasm/master/recipes.json',
            true
        );

        $decoded_extract = json_decode($extraction);

        foreach ($decoded_extract->recipe as $newRecipe) {
            if (property_exists($newRecipe, 'ingredient')) {
                if (count($newRecipe->ingredient) === 0) {
                    continue;
                }
            }

            $rec = new Recipe();
            $rec->setTitle($newRecipe->name);
            $rec->setDescription($newRecipe->description);

            if (property_exists($newRecipe, 'tag')) {
                if (count($newRecipe->ingredient) !== 0) {
                    foreach ($newRecipe->tag as $newTag) {
                        $persistedTag = $entityManager->getRepository(Tag::class)
                                ->findOneBy(['title' => $newTag]);
                        if ($persistedTag == null) {
                            $persistedTag = new Tag();
                            $persistedTag->setTitle($newTag);
                        }
                        $entityManager->persist($persistedTag);
                        $rec->addTag($persistedTag);
                    }
                }
            }

            $entityManager->persist($rec);

            foreach ($newRecipe->ingredient as $newIngredient) {
                $ingr = new Ingredient();

                if (!property_exists($newIngredient, 'name')) {
                    continue;
                }

                $ingr->setTitle($newIngredient->name);
                $entityManager->persist($ingr);

                $mea = new Measure();

                if (property_exists($newIngredient, 'unit')) {
                    switch ($newIngredient->unit) {
                        case "tsp":
                            $mea = $entityManager->getRepository(Measure::class)->find(4);
                            break;
                        case "tbsp":
                            $mea = $entityManager->getRepository(Measure::class)->find(3);
                            break;
                        case "g":
                            $mea = $entityManager->getRepository(Measure::class)->find(2);
                            break;
                        case "cup":
                            $mea = $entityManager->getRepository(Measure::class)->find(5);
                            break;
                        case "cups":
                            $mea = $entityManager->getRepository(Measure::class)->find(5);
                            break;
                        case "oz":
                            $mea = $entityManager->getRepository(Measure::class)->find(10);
                            break;
                        case "lbs":
                            $mea = $entityManager->getRepository(Measure::class)->find(11);
                            break;
                        case "lb":
                            $mea = $entityManager->getRepository(Measure::class)->find(11);
                            break;
                        case "ml":
                            $mea = $entityManager->getRepository(Measure::class)->find(6);
                            break;
                        case "pinch":
                            $mea = $entityManager->getRepository(Measure::class)->find(7);
                            break;
                        case "handful":
                            $mea = $entityManager->getRepository(Measure::class)->find(8);
                            break;
                        case "shot":
                            $mea = $entityManager->getRepository(Measure::class)->find(9);
                            break;
                        case "bunch":
                            $mea = $entityManager->getRepository(Measure::class)->find(8);
                            break;
                        default:
                            $mea = $entityManager->getRepository(Measure::class)->find(1);
                            break;
                    }
                } else {
                    $mea = $entityManager->getRepository(Measure::class)->find(1);
                }

                $recIngr = new RecipeIngredient();

                if (property_exists($newIngredient, 'amount')) {
                    $amount = $newIngredient->amount;
                    if (is_numeric($amount)) {
                        $recIngr->setAmount($amount);
                    }
                }

                $recIngr->setIngredient($ingr);
                $recIngr->setMeasure($mea);
                $recIngr->setRecipe($rec);
                $entityManager->persist($recIngr);

            }
            $entityManager->flush();
        }

        return 0;
    }
}
