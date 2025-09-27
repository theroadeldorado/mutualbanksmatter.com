<?php

/**
 * Title: Fire Anchor
 * Description: Generates anchor tag
 *
 * Usage: new Fire_Anchor('Press me', '/testing');
 *
 * @param Array $action url, title, target - action from acf
 * @param String $class custom classes for link
 */
class Fire_Anchor
{
  public function __construct($action, $class)
  {
    $this->title = $action['title'];
    $this->url = $action['url'];
    $this->target = $action['target'];
    $this->class = $class;
    $this->echo_tag();
  }

  /**
   * Renders anchor tag on the page
   *
   */
  private function echo_tag()
  {
    $anchor = "<a href=\"{$this->url}\" class=\"{$this->class}\" ";

    if ($this->target) {
      $anchor .= 'target="_blank"';
    }

    $anchor .= ">{$this->title}</a>";
    echo $anchor;
  }
}
