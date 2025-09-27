<?php

/**
 * Title: Fire Heading
 * Description: Generates heading tag
 *
 * Usage: new Fire_Heading('h2', 'This is the heading text', 'text-18');
 *
 * @param String $tag tag of element
 * @param String $text text for heading
 * @param String $class classes for heading
 */
class Fire_Heading {
  public $tag;
  public $text;
  public $class;

  public function __construct($tag = 'h2', $text = '', $class = '') {
    $this->tag = $tag;
    $this->text = $text;
    $this->class = $class;
    $this->echo_heading();
  }

  /**
   * Renders heading tag on the page
   *
   */
  private function echo_heading() {

    if (isset($this->text)) {
      $dom = new DOMImplementation();
      $document = $dom->createDocument(null, 'html', $dom->createDocumentType('html'));
      $div = $document->appendChild($document->createElement($this->tag));
      $div->setAttribute('class', $this->class);
      $divInner = $document->createDocumentFragment();
      $appendResult = $divInner->appendXML(str_replace('&', '&amp;', $this->text));

      if ($appendResult === false) {
        return;
      }

      $div->appendChild($divInner);
      echo $document->saveHTML($div);
    } else {
      throw new Exception('Text is required in Fire_Heading');
    }
  }
}
