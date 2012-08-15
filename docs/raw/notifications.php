{ce:core:include template="global/_header" title="Email Notification Templates"}

<h1>Notification Templates</h1>

<p>Notification templates in <dfn>ProForm</dfn> are based on the same ExpressionEngine templates that you use to build any page on your site. Because of this, they can make use of any installed Module or Plugin, and have full conditionals support.</p>

<p>If you do not need a fancy display or other information about the entry, this is probably all you will need - a single template can render notifications for any form submitted to <dfn>ProForm</dfn>.</p>

<h2>Single Variables</h2>
<p>All of the <a href="{root_url}tags/entries.html#variables">variables</a> available in the <kbd>Entries Tag</kbd> are available in the notification template when notifications are generated for a newly created entry.</p>

<h2>Variable Pairs</h2>
<p>Similarly, all of the <a href="{root_url}tags/entries.html#variable_pairs">variable pairs</a> available in the <kbd>Entries Tag</kbd> are available in the notification template when notifications are generated for a newly created entry.</p>

<h2>Sample Template</h2>
<p>One of the simplest possible notification templates looks like this:</p>

<p class="strongbox">Example Template</p>
<textarea cols="80" rows="40" style="width: 100%; font-family: courier;">
&lt;p&gt;Someone submitted the &#123;form_name&#125; form:&lt;/p&gt;
&#123;fields&#125;
  &lt;b&gt;&#123;field_label&#125;&lt;/b&gt;: 
    &#123;if field_filename&#125;
        &lt;a href="&#123;field_value&#125;"&gt;&#123;field_filename&#125;&lt;/a&gt;
    &#123;if:else&#125;
        &#123;field_value&#125;
    &#123;/if&#125;&lt;br/&gt;
&#123;/fields&#125;
</textarea>

{ce:core:include template="global/_footer"}