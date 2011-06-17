<?php

namespace Ckluster\TutorialBundle\Entity;

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
     * @var Ckluster\TutorialBundle\Entity\Product
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Assert\NotBlank()
     *
     * @var Ckluster\TutorialBundle\Entity\User
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

    public function __construct(Product $pProduct = null,
                                User $pAuthor = null,
                                $piRating = null,
                                $psReview = null)
    {
        parent::__construct();
        $this->product = $pProduct;
        $this->author = $pAuthor;
        $this->rating = $piRating;
        $this->review = $psReview;
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

    public function setRating($piRating)
    {
        $this->rating = \intval($piRating);
    }

    public function getReview()
    {
        return $this->review;
    }

    public function setReview($psReview)
    {
        $this->review = $psReview;
    }

}
