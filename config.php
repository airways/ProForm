<?php

// @version 1.71
// @prolib 0.93

define('PROFORM_VERSION', '1.71');
define('PROFORM_NAME', 'ProForm');
define('PROFORM_CLASS', 'Proform'); // must match module class name
define('PROFORM_DESCRIPTION', 'ProForm is an advanced form management module for ExpressionEngine 2.0, designed to make creation and management of forms easier for developers and end users.');
define('PROFORM_DOCSURL', 'http://metasushi.com/documentation/proform');

if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    define('PROFORM_DEBUG', TRUE);
} else {
    define('PROFORM_DEBUG', FALSE);
}

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

// EE 2.8 cp_url function is now used to generate URLs - need to provide it if
// we are on a version prior to EE 2.8
if (version_compare(APP_VER, '2.8', '<') && !function_exists('cp_url'))
{
    function cp_url($path, $qs = '')
    {
    	$path = trim($path, '/');
    	$path = preg_replace('#^cp(/|$)#', '', $path);
        
        $segments = explode('/', $path);
        $result = BASE.AMP.'C='.$segments[0].AMP.'M='.$segments[1];
        
    	if (is_array($qs))
    	{
    		$qs = AMP.http_build_query($qs, AMP);
    	}
    	
    	$result .= $qs;
    
    	return $result;
    }
}

function pf_log($message, $obj=NULL) {
    if(PROFORM_DEBUG) {
        global $pf_log_prefix;
        error_log('PROFORM '.$pf_log_prefix.' '.$message.(!is_null($obj) ? '::'.preg_replace('/\s+/', ' ',print_r($obj, TRUE)) : ''));
    }
}
