<?php
/**
 * Posting fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Posting;
use Doctrine\Persistence\ObjectManager;

/**
 * Class PostingFixtures.
 */
class PostingFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager ): void
    {
        $this->createMany(50, 'postings', function ($i) use ($manager) {
            $category = $manager->getRepository(Category::class)->findBy(
                ['id' => rand(1, 10)],
                ['id' => 'desc'],
                1
            );

            $posting = new Posting();
            $posting->setIsActive(1);
            $posting->setTitle($this->faker->sentence);
            $posting->setDescription($this->faker->paragraph($nbSentences = 3, $variableNbSentences = true));
            $posting->setDate($this->faker->dateTime($max = 'now', $timezone = null));
            $posting->setImg($this->faker->imageUrl($width = 640, $height = 480));
            $posting->setCategory($this->getRandomReference('categories'));
            $posting->setPostedBy($this->faker->email);

            return $posting;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
