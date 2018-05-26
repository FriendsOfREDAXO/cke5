<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */
if (rex_string::versionCompare($this->getVersion(), '1.0.0', '>')) {
    include_once $this->getPath('install.php');
}