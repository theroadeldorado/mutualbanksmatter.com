<?php

/**
 * Title: Fire SVG
 * Description: Loads and escapes SVGs automatically
 *
 * Usage: new Fire_SVG('svg-name');
 *
 * @param String $svg_name The name of the SVG you want to load
 */
class Fire_SVG {
  public $svg_name; // Declare the property here

  public function __construct($svg_name) {
    $this->svg_name = $svg_name;
    $this->echo_svg();
  }

  /**
   * Load's the escaped SVG
   *
   */
  public function echo_svg() {
		$file_path = file_get_contents( get_theme_file_path('theme/assets/media/svgs/' . $this->svg_name . '.svg' ));
    echo wp_kses($file_path, $this->ruleset());
  }

	/**
	 * Sets up rulesets for escaping SVGs.
	 */
	private function ruleset() {
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
				'viewBox'         => true,
				'fill'            => true,
				'stroke'          => true,
			),
			'g'     => array(
				'fill'   => true,
				'stroke' => true,
			),
			'title' => array( 'title' => true ),
			'path'  => array(
				'd'                => true,
				'fill'             => true,
				'fill-rule'        => true,
				'clip-rule'        => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linecap'   => true,
				'stroke-linejoin'  => true,
			),
			'circle' => array(
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'rect' => array(
				'x'            => true,
				'y'            => true,
				'width'        => true,
				'height'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'rx'           => true,
				'ry'           => true,
			),
			'line' => array(
				'x1'           => true,
				'y1'           => true,
				'x2'           => true,
				'y2'           => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'polyline' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
			'polygon' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
			),
		);
		return array_merge( $kses_defaults, $svg_args );
	}
}
