<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product class
 * @ORM\Entity
 *
 * @author arturo
 */
class Product extends BaseEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     *
     * @Assert\Type(type="numeric")
     * @Assert\Min(limit=0.01)
     *
     * @var string
     */
    private $cost;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Assert\Type(type="numeric")
     * @Assert\Max(limit=5)
     * @Assert\Min(limit=0)
     *
     * @var float
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity="ProductReview", mappedBy="product")
     * 
     * @var Doctrine\Common\Collections\ArrayCollection
     */
    private $reviews;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $dateOfCreation;

    public function __construct(string $pName, string $pDescription,
                                string $pCost, $pfRating = NULL)
    {
        $this->id = $this->generateID();
        $this->name = $pName;
        $this->description = $pDescription;
        $this->cost = $pCost;
        $this->rating = $pfRating;
        $this->reviews = new ArrayCollection();
        $this->dateOfCreation = $this->now();
    }

    public function addReview(ProductReview $review)
    {
        if ($review->getProduct()->id !== $this->id) {
            return;
        }

        /* update the rating */
        $total_reviews = $this->reviews->count();
        $current_sum = $total_reviews * $this->rating;
        $new_rating = ($current_sum + $review->getRating()) / ($total_reviews + 1);
        $this->rating = $new_rating;

        $this->reviews[] = $review;
    }

    public function getDateOfCreation()
    {
        return $this->dateOfCreation;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $pName)
    {
        $this->name = $pName;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $pDescription)
    {
        $this->description = $pDescription;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost(string $pCost)
    {
        $this->cost = $pCost;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($pfRating)
    {
        $this->rating = $pfRating;
    }

}
