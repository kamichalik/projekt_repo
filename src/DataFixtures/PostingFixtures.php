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
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(50, 'postings', function ($i) use ($manager) {
            $category = $manager->getRepository(Category::class)->findBy(
                ['id' => rand(1, 10)],
                ['id' => 'desc'],
                1
            )[0];

            $posting = new Posting();
            $posting->setIsActive(1);
            $posting->setTitle($this->faker->sentence);
            $posting->setDescription($this->faker->paragraph($nbSentences = 3, $variableNbSentences = true));
            $posting->setDate($this->faker->dateTime($max = 'now', $timezone = null));
            $posting->setImg($this->faker->imageUrl($width = 640, $height = 480));
            $posting->setCategory($category);

            return $posting;
        });

        $manager->flush();
    }
}
