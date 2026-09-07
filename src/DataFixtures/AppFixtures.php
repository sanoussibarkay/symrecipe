<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    public function __construct()
    {
        $this->faker = \Faker\Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {

    //user
    $users = [];
        for ($i = 0; $i < 10 ; $i++) {
            $user = new \App\Entity\User();
            $user->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('password');
            $users[] = $user;

            $manager->persist($user);
        }
     
        //ingredient
        $ingredients = [];
        for ($i = 0; $i < 50 ; $i++) { 
            $ingredient = new \App\Entity\Ingredient();
            $ingredient->setName($this->faker->word())
            ->setPrice(\mt_rand(1, 10))
            ->setUser($users[\mt_rand(0, \count($users) - 1)]);

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
          
        }


        //recipe
        $recipes = [];
        for ($i = 0; $i < 25 ; $i++) {
            $recipe = new \App\Entity\Recipe();
            $recipe->setName($this->faker->word())
            ->setTime(\mt_rand(0, 1) == 1 ? \mt_rand(1, 1440) : null)
            ->setNbPeople(\mt_rand(0, 1) == 1 ? \mt_rand(1, 50) : null)
            ->setDifficulty(\mt_rand(0, 1) == 1 ? \mt_rand(1, 5) : null)
            ->setDescription($this->faker->text(300))
            ->setUser($users[\mt_rand(0, \count($users) - 1)])
            ->setPrice(\mt_rand(0, 1) == 1 ? \mt_rand(1, 1000) : null)
            ->setIsFavorite(\mt_rand(0, 1 ) == 1 ? true : false)
            ->setisPublic(\mt_rand(0, 1 ) == 1 ? true : false);
            for ($j = 0; $j < \mt_rand(5, 15
); $j++) { 
                $recipe->addIngredient($ingredients[\mt_rand(0, \count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        //mark
        foreach ($recipes as $recipe) {
            for ($i=0; $i < \mt_rand(0, 4); $i++) { 
            $mark = new \App\Entity\Mark();
            $mark->setMark(\mt_rand(1, 5))
            ->setUser($users[\mt_rand(0, \count($users) - 1)])
            ->setRecipe($recipes[\mt_rand(0, \count($recipes) - 1)]);
            $manager->persist($mark);
               
            }
           
        }

        
        $manager->flush();

    }
}
