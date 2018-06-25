<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */
if (rex_string::versionCompare($this->getVersion(), '2.0.0', '<')) {
    include_once $this->getPath('install.php');
}

try {
    // regenerate profiles general
    Cke5\Creator\Cke5ProfilesCreator::profilesCreate();
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}
