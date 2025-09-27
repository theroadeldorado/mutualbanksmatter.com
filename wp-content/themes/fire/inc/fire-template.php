<?php

/**
 * Class Fire_Template
 *
 * This class is used for rendering templates in PHP. It allows you to specify a template file,
 * pass variables to it, and control whether the template should be included once or multiple times.
 *
 * Example usage:
 * new Fire_Template('@components/folder/file.php', ['var_1' => $var_1, 'var_2' => $var_2]);
 *
 * @package Fire
 */


class Fire_Template {
  /**
   * @var string $template_name The name of the template file to be rendered.
   */
  private $template_name;

  /**
   * @var array $vars An associative array of variables to be passed to the template file.
   */
  private $vars;

  /**
   * @var bool $once A boolean indicating whether the template file should be included once or multiple times.
   */
  private $once;

  /**
   * @const DIRECTORIES An associative array mapping shorthand directory names to their actual paths.
   *
   * This constant is used to simplify the referencing of commonly used directories in the application.
   */
  const DIRECTORIES = array(
    '@components' => '/templates/components/',
  );

  /**
   * Fire_Template constructor.
   *
   * @param string $template_name The name of the template file to be rendered.
   * @param array $vars An associative array of variables to be passed to the template file.
   * @param bool $once A boolean indicating whether the template file should be included once or multiple times.
   */
  public function __construct($template_name, $vars = array(), $once = false) {
    // Check if $template_name contains DIRECTORY, if so replace it with the path to the directory
    foreach (self::DIRECTORIES as $key => $value) {
      if (strpos($template_name, $key) !== false) {
        $template_name = str_replace($key, $value, $template_name);
      }
    }

    $this->template_name = $template_name;
    $this->vars = $vars;
    $this->once = $once;
    $this->render();
  }

  /**
   * Renders the template file.
   *
   * This method extracts the variables to a local scope and includes the template file.
   * If $once is true, the template file is included once. Otherwise, it is included every time this method is called.
   */
  public function render() {
    extract($this->vars); // Extract the variables to a local scope

    if ($this->once) {
      require_once(get_template_directory() . '/' . $this->template_name);
    } else {
      require(get_template_directory() . '/' . $this->template_name);
    }
  }
}
