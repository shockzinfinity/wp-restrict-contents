<?php

class Content_messaenger
{
  private $deserializer;

  public function __construct($deserializer)
  {
    $this->deserializer = $deserializer;
  }

  public function init()
  {
    add_filter('the_content', array($this, 'display'));
  }

  public function display($content)
  {
    echo $this->deserializer->get_value('restrict-custom-data');
    return $content;
  }
}
