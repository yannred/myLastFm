<?php

namespace App\Data;

class Notification
{
  public string $message;
  public string $type;

  public function __construct($message, $type = null)
  {
    $this->message = $message;

    if ($type) {
      $this->type = $type;
    } else {
      $this->type = 'info';
    }
  }
}