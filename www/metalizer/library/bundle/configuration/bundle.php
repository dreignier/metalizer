<?php
/**
 * All bundle.processor.<name> can specify a new BundleFileProcessor for bundles.
 * When you use a bundle, you can set the processor by using the prefix <name>:
 * For example, you can use the pattern 'css:foo/*_dir/*.css' in a bundle if the "bundle.processor.css" exists.
 */
$config['bundle.processor.default'] = 'DefaultFileProcessor';
