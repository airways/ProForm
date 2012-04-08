{ce:core:include template="global/_header"}

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

<p>This tag should be used in the template that handles the <a href="{site_url}documentation/proform/tags/form/#param_thank_you_url">thank_you_url</a> path sent to the <kbd>Form Tag</kbd>.</p>

<!-- ********************************************************************** -->
<h2><a name="variables">Single Variables</a></h2>

<p>See the single variables of the <a href="{root_url}tags/form.html#variables">Entries Tag</a> tag for more information.</p>

<p>Additionally, any unknown <a href="{root_url}tags/form.html#param_custom">custom parameters</a> sent to the form tag will be available as single variables within the <kbd>Results Tag</kbd>.</p>

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

{ce:core:include template="global/_footer"}