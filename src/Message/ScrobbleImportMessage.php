<?php

namespace App\Message;

/**
 * Message for importing scrobble task
 */
class ScrobbleImportMessage
{

  public int $userId;
  public int $importId;

  public function __construct(int $userId, int $importId)
  {
    $this->userId = $userId;
    $this->importId = $importId;
  }

}