<?php

namespace Morzan\TutorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * A product review
 *
 * @ORM\Entity
 *
 * @author arturo
 */
class ProductReview extends BaseEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="reviews")
     * 
     * @Assert\NotBlank()
     * 
     * @var Morzan\TutorialBundle\Entity\Product
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Assert\NotBlank()
     *
     * @var Morzan\TutorialBundle\Entity\User
     */
    private $author;

    /**
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Min(limit=1)
     * @Assert\Max(limit=5)
     *
     * @var integer
     */
    private $rating;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $review;

    public function __construct(Product $pProduct, User $pAuthor, int $pRating,
                                string $pReview)
    {
        parent::__construct();
        $this->product = $pProduct;
        $this->author = $pAuthor;
        $this->rating = $pRating;
        $this->review = $pReview;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $pProduct)
    {
        $this->product = $pProduct;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(User $pAuthor)
    {
        $this->author = $pAuthor;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating(int $pRating)
    {
        $this->rating = $pRating;
    }

    public function getReview()
    {
        return $this->author;
    }

    public function setReview(string $pReview)
    {
        $this->review = $pReview;
    }

}
