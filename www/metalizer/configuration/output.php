<?php
/**
 * If true, the html output will be cleaned using Tidy.
 * The tidy php extension must be activated ! 
 * @var bool
 */
$config['output.clean'] = $config['metalizer.mode'] == MODE_DEVELOPMENT;

/**
 * The configuration for tidy. It will be given to Tidy it output.clean is true.
 * See http://tidy.sourceforge.net/docs/quickref.html for more informations.
 * @var array[mixed]
 */
$config['output.clean.configuration'] = array(
   'indent' => true,
   'output-xhtml' => true,
   'wrap' => 500,
   'tab-size' => 3,
   'indent-spaces' => 3
);
