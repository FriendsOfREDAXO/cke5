<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

include_once (__DIR__ . '/db.php');

try {
    if (rex_string::versionCompare($this->getVersion(), '3.3.0', '<')) {
        // copy custom data to assets folder
        if (!file_exists(rex_path::assets('addons/cke5_custom_data'))) {
            mkdir(rex_path::assets('addons/cke5_custom_data'));
        }
        if (!file_exists(rex_path::assets('addons/cke5_custom_data/custom-style.css'))) {
            rex_file::copy($this->getPath('custom_data/custom-styles.css'), rex_path::assets('addons/cke5_custom_data/custom-style.css'));
        }
    }
  
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}
rex_extension::register('PACKAGES_INCLUDED', function () {
    // recreate profiles after update
    \Cke5\Handler\Cke5ExtensionHandler::updateProfiles();
});  
