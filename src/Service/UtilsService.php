<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class UtilsService
{
  protected LoggerInterface $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function logDevInfo(string $message): void
  {
    if ($_ENV['APP_ENV'] === 'dev') {
      $this->logger->info($message);
    }
  }

  public function logError(string $message): void
  {
    $this->logger->error($message);
  }


}