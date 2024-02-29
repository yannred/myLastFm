<?php

namespace App\Message;

class ScrobbleImportMessage
{

  public int $userId;

  public function __construct(int $userId)
  {
    $this->userId = $userId;
  }

}