<?php

namespace App\Entity;

use App\Repository\PollOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PollOptionRepository::class)]
class PollOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pollOptions')]
    private ?Poll $poll = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $votes = 0;

    /**
     * @var Collection<int, PollVote>
     */
    #[ORM\OneToMany(targetEntity: PollVote::class, mappedBy: 'option')]
    private Collection $pollVotes;

    public function __construct()
    {
        $this->pollVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $poll): static
    {
        $this->poll = $poll;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(?int $votes): static
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * @return Collection<int, PollVote>
     */
    public function getPollVotes(): Collection
    {
        return $this->pollVotes;
    }

    public function addPollVote(PollVote $pollVote): static
    {
        if (!$this->pollVotes->contains($pollVote)) {
            $this->pollVotes->add($pollVote);
            $pollVote->setOption($this);
        }

        return $this;
    }

    public function removePollVote(PollVote $pollVote): static
    {
        if ($this->pollVotes->removeElement($pollVote)) {
            // set the owning side to null (unless already changed)
            if ($pollVote->getOption() === $this) {
                $pollVote->setOption(null);
            }
        }

        return $this;
    }

    /**
     * Returns the total votes for this option
     */
    public function getVotesCount(): int
    {
        return $this->pollVotes->count();
    }
}
