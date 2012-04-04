---
entry_id: '35'
site_id: '1'
channel_id: '4'
author_id: '1'
pentry_id: '0'
forum_topic_id: null
ip_address: 75.73.88.195
title: 'Results Tag'
url_title: proform-results-tag
status: open
versioning_enabled: y
view_count_one: '0'
view_count_two: '0'
view_count_three: '0'
view_count_four: '0'
allow_comments: y
sticky: n
entry_date: '1329698521'
dst_enabled: n
year: '2012'
month: '02'
day: '19'
expiration_date: '0'
comment_expiration_date: '0'
edit_date: '20120219171303'
recent_comment_date: '0'
comment_total: '0'
last_written: '0'
fe_type: entry
---
<h1>Results Tag</h1>

<p>The <kbd>Results Tag</kbd> allows display of a thank you message along with the submitted data from a form.</p>

<!-- ********************************************************************** -->
<h2>
	Contents</h2>
<ul>
	<li><a href="#parameters">Parameters</a></li>
	<li><a href="#variables">Variables</a></li>
	<li><a href="#variable_pairs">Variable Pairs</a></li>
	<li><a href="#sample_template_code">Sample Template</a></li>
</ul>
<!-- ********************************************************************** -->

<h2><a name="parameters">Parameters</a></h2>

<p>The <kbd>Results Tag</kbd> does not accept parameters. It takes all data from the visitor's session.</p>

<p>This tag should be used in the template that handled the <a href="{site_url}documentation/proform/tags/form/#param_thank_you_url">thank_you_url</a> path sent to the <kbd>Form Tag</kbd>.</p>

<!-- ********************************************************************** -->
<h2><a name="variables">Single Variables</a></h2>

<p>See the single variables of the <a href="{site_url}documentation/proform/tags/entries/#variables">Entries Tag</a> tag for more information.</p>


<!-- ********************************************************************** -->
<h2><a name="variable_pairs">Variable Pairs</a></h2>

<p>See the variable pairs of the <a href="{site_url}documentation/proform/tags/entries/#variable_pairs">Entries Tag</a> tag for more information.</p>

<!-- ********************************************************************** -->
<h2><a name="sample_template_code">Sample Template Code</a></h2>

<p class="strongbox">Example Template</p>
<p>This tag should be used in the template specified by the URL passed in through a form tag's <a href="{site_url}documentation/proform/tags/form/#param_thank_you_url">thank_you_url</a> parameter.</p>
<pre class="brush: xml">
&#123;exp:proform:results&#125;
    &#123;if form_name&#125;
        &lt;h1&gt;Thank you for submitting the &lt;a href="&#123;path=forms/&#123;form_name&#125;&#125;"&gt;&#123;form_name&#125;&lt;/a&gt; form&lt;/h1&gt;
    
        &lt;p&gt;We have received the following information which you submitted:&lt;/p&gt;
        
        &#123;fields&#125;
            &#123;field_name&#125; = &#123;field_value&#125;&lt;br/&gt;
        &#123;/fields&#125;
        
        &lt;p&gt;Custom thank you message from the form:&lt;/p&gt;
        
        &lt;p&gt;&#123;thank_you_message&#125;&lt;/p&gt;
        
        &#123;if download_url&#125;
            &lt;p&gt;You may now download your file: &lt;a href="{download_url}"&gt;{download_label}&lt;/a&gt;&lt;/p&gt;
        &#123;/if&#125;
        
        &#123;if referrer_url&#125;
            &lt;p&gt;Return to &lt;a href="{referrer_url}"&gt;previous page&lt;/a&gt;.&lt;/p&gt;
        &#123;/if&#125;
    &#123;if:else&#125;
        &lt;h1&gt;Thanks for the form&lt;/h1&gt;
        &lt;p&gt;But, aw snap something went wrong and I can't confirm your submission.&lt;/p&gt;
    &#123;/if&#125;
&#123;/exp:proform:results&#125;
</pre>