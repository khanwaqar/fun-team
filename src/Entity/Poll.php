<?php

namespace App\Entity;

use App\Repository\PollRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PollRepository::class)]
class Poll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'polls')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endDate = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, PollOption>
     */
    #[ORM\OneToMany(targetEntity: PollOption::class, mappedBy: 'poll')]
    private Collection $pollOptions;

    #[ORM\ManyToOne(targetEntity: PollOption::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?PollOption $winnerOption = null;


    /**
     * @var Collection<int, PollVote>
     */
    #[ORM\OneToMany(targetEntity: PollVote::class, mappedBy: 'poll')]
    private Collection $pollVotes;

    public function __construct()
    {
        $this->pollOptions = new ArrayCollection();
        $this->pollVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getEndDate(): ?\DateTimeInterface { return $this->endDate; }
    public function setEndDate(?\DateTimeInterface $endDate): self { $this->endDate = $endDate; return $this; }

    public function getWinnerOption(): ?PollOption { return $this->winnerOption; }
    public function setWinnerOption(?PollOption $winnerOption): self { $this->winnerOption = $winnerOption; return $this; }



    /**
     * @return Collection<int, PollOption>
     */
    public function getPollOptions(): Collection
    {
        return $this->pollOptions;
    }

    public function addPollOption(PollOption $pollOption): static
    {
        if (!$this->pollOptions->contains($pollOption)) {
            $this->pollOptions->add($pollOption);
            $pollOption->setPoll($this);
        }

        return $this;
    }

    public function removePollOption(PollOption $pollOption): static
    {
        if ($this->pollOptions->removeElement($pollOption)) {
            // set the owning side to null (unless already changed)
            if ($pollOption->getPoll() === $this) {
                $pollOption->setPoll(null);
            }
        }

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
            $pollVote->setPoll($this);
        }

        return $this;
    }

    public function removePollVote(PollVote $pollVote): static
    {
        if ($this->pollVotes->removeElement($pollVote)) {
            // set the owning side to null (unless already changed)
            if ($pollVote->getPoll() === $this) {
                $pollVote->setPoll(null);
            }
        }

        return $this;
    }

    public function hasUserVoted(User $user): bool
    {
        foreach ($this->pollVotes as $vote) {
            if ($vote->getUser() === $user) {
                return true;
            }
        }
        return false;
    }
}
