<?php
/* Set Responsive Pics variables (match TW config) */
if (class_exists('ResponsivePics')) {
  ResponsivePics::setBreakPoints([
    'sm'    => 0,
    'md'    => 768,
    'lg'    => 992,
    'xl'    => 1200,
  ]);
  ResponsivePics::setImageQuality(100);
}