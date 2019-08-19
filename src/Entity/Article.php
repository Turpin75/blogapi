<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @Serializer\ExclusionPolicy("all")
 * 
 * @Hateoas\Relation(
 *  "self",
 *  href = @Hateoas\Route(
 *      "articles_show",
 *      parameters = { "id"="expr(object.getId())" },
 *      absolute=true
 *  )
 * )
 * 
 * @Hateoas\Relation(
 *  "modify",
 *  href = @Hateoas\Route(
 *      "articles_update",
 *      parameters = { "id"="expr(object.getId())" },
 *      absolute=true
 *  )
 * )
 * 
 * @Hateoas\Relation(
 *  "delete",
 *  href = @Hateoas\Route(
 *      "articles_delete",
 *      parameters = { "id"="expr(object.getId())" },
 *      absolute=true
 *  )
 * )
 * 
 * @Hateoas\Relation(
 *  "author",
 *  embedded = @Hateoas\Embedded("expr(object.getAuthor())")
 * )
 * 
 *  @Hateoas\Relation(
 *  "weather",
 *  embedded = @Hateoas\Embedded("expr(service('app.weather').getCurrent())")
 * )
 * 
 * @Hateoas\Relation(
 *  "authenticated_user",
 *  embedded = @Hateoas\Embedded("expr(service('security.token_storage').getToken().getUser())")
 * )
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Serializer\Expose
     * @Serializer\Since("1.0")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Serializer\Expose
     * @Serializer\Since("1.0")
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * @Serializer\Since("2.0")
     */
    private $shortDescription;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="articles", cascade={"persist"})
     */
    private $author;

    private $weather;

    public function __construct(Weather $weather)
    {
       $this->weather = $weather;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
