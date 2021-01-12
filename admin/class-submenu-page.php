<?php
class Submenu_Page
{
  private $deserializer;
  private $serializer;

  public function __construct($serializer, $desrializer)
  {
    $this->serializer = $serializer;
    $this->deserializer = $desrializer;
  }
  public function render()
  {
    //echo 'This is the basic submenu page';
    include_once('views/settings.php');
  }
}
