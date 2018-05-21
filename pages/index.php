<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'));
rex_be_controller::includeCurrentPageSubPath();
