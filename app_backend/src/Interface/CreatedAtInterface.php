<?php

namespace App\Interface;

interface CreatedAtInterface
{
    public function setCreatedAt(\DateTimeImmutable $createdAt): static;
}
