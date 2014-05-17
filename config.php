<?php

// @version 1.49
// @prolib 0.71

define('PROFORM_VERSION', '1.49');
define('PROFORM_NAME', 'ProForm');
define('PROFORM_CLASS', 'Proform'); // must match module class name
define('PROFORM_DESCRIPTION', 'ProForm is an advanced form management module for ExpressionEngine 2.0, designed to make creation and management of forms easier for developers and end users.');
define('PROFORM_DOCSURL', 'http://metasushi.com/documentation/proform');
define('PROFORM_DEBUG', TRUE);


// EE 2.5.5 or less not officially supported anymore,
// but keeping this for backwards compatibility.
if (version_compare(APP_VER, '2.6', '<') && !function_exists('ee'))
{
    function ee()
    {
        static $EE;
        if ( ! $EE) $EE = get_instance();
        return $EE;
    }
}
