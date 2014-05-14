<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
 *
 * Copyright (c)2009, 2010, 2011. Isaac Raway and MetaSushi, LLC.
 * All rights reserved.
 *
 * This source is commercial software. Use of this software requires a
 * site license for each domain it is used on. Use of this software or any
 * of its source code without express written permission in the form of
 * a purchased commercial or other license is prohibited.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * As part of the license agreement for this software, all modifications
 * to this source must be submitted to the original author for review and
 * possible inclusion in future releases. No compensation will be provided
 * for patches, although where possible we will attribute each contribution
 * in file revision notes. Submitting such modifications constitutes
 * assignment of copyright to the original author (Isaac Raway and
 * MetaSushi, LLC) for such modifications. If you do not wish to assign
 * copyright to the original author, your license to  use and modify this
 * source is null and void. Use of this software constitutes your agreement
 * to this clause.
 *
 **/ 
 
function pf_help_get_inner_html( $node ) {
    $innerHTML= '';
    $children = $node->childNodes;
    foreach ($children as $child) {
        $innerHTML .= $child->ownerDocument->saveXML( $child );
    }
    
    return $innerHTML;
} 

?>

<h2>Get Help on ProForm</h2><br/>
<div class="info">
    <p>Need help with ProForm? You're one click away from fanatical developer support.</p>
</div>

<h3>User Guide</h3>
<p>First, take a look at the User Guide, which can answer most questions.</p>
<p><span class="button content-btn"><a class="submit" target="_blank" href="http://metasushi.com/documentation/proform/">ProForm Documentation</a></span></p>

<h3>Forum Search</h3>
<p>Search for answers to common questions on our extensive forum archives of hundreds of resolved posts.</p>
<?php
$forum_html = file_get_contents('http://devot-ee.com/add-ons/support/proform-drag-and-drop-form-builder/viewforum/1840');
if(preg_match('#<div.*?id="forumSearchBox".*?>.*?(<form.*?</form>.*?)</div>#s', $forum_html, $matches))
{
    echo str_replace('<form', '<form target="_blank" ', $matches[1]);
}
?>
<br/>

<h3>Recent forum posts</h3>
<ul>
<?php
if(preg_match_all('#<div.*?class="topicTitle".*?>(.*?)</div>#s', $forum_html, $matches))
{
    foreach($matches[1] as $match)
    {
        echo '<li>'.str_replace('<a', '<a target="_blank" ', $match).'</li>';
    }
}
?>
</ul>
<br/><br/>

<h4>Still need help?</h4>
<p>All support is provided through the official support forum on Devot:ee.</p>
<p>
    <span class="button content-btn"><a class="submit" target="_blank" href="http://devot-ee.com/add-ons/support/proform-drag-and-drop-form-builder/viewforum/1840">Visit Support Forum</a></span>
    <span class="button content-btn"><a class="submit" target="_blank" href="http://devot-ee.com/add-ons/support/proform-drag-and-drop-form-builder/newtopic/1840">Post New Topic</a></span>


</p>


