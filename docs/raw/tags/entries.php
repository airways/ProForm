{ce:core:include template="global/_header"}

<h1>Entries Tag</h1>

<p>The <kbd>Entries Tag</kbd> provides a way to query and display any entry created through ProForm.</p>

<h2>Contents</h2>

<ul>
    <li><a href="#parameters">Parameters</a></li>
    <li><a href="#variables">Variables</a></li>
    <li><a href="#variable_pairs">Variable Pairs</a></li>
    <li><a href="#sample_template">Sample Template Code</a></li>
</ul>

<h2><a name="parameters">Parameters</a></h2>
<p>The following parameters are available:</p>

<ul>
    <li><a href="#param_entry_id">entry_id="1"</a></li>
    <li><a href="#param_form">form="contact_us"</a></li>
</ul>

<h3><a name="param_entry_id">entry_id="1"</a></h3>

<p>The <b>entry_id</b> parameter allows you to filter the results to a single entry.</p>

<h3><a name="param_form">form="contact_us"</a></h3>
<p>The <b>form</b> parameter allows you to filter the results to those form a particular form.</p>

<h2><a name="variables">Single Variables</a></h2>
<p>All of the <a href="{site_url}documentation/proform/tags/form#variables">single variables</a> available in the <kbd>Form Tag</kbd> are also available in the <dfn>Entries Tag</dfn>.</p>

<p>Additionally, the following variables are available in the <dfn>Entries Tag</dfn> and notification templates:</p>

<ul>
    <li><a href="#var_value_user_agent">{value:user_agent}</a></li>
    <li><a href="#var_value_ip_address">{value:ip_address}</a></li>
</ul>

<h3><a name="var_value_user_agent">{value:user_agent}</a></h3>
<p>The User-Agent header as sent by the visitor&#39;s browser when the entry was saved.</p>

<h3><a name="var_value_ip_address">{value:ip_address}</a></h3>
<p>The IP address as sent by the visitor&#39;s browser when the entry was saved.</p>

<h2><a name="variable_pairs">Variable Pairs</a></h2>
<p>All of the <a href="{site_url}documentation/proform/tags/form#variable_pairs">variable pairs</a> available in the <kbd>Form Tag</kbd> are also available in the <dfn>Entries Tag</dfn>.</p>

<p>Some additional values are available within these variable pairs.</p>

<h3><a name="forms">{fields}</a></h3>
<p>In addition to the normal variables available within the {fields} variable pair, both at the root of the <dfn>Entries Tag</dfn> and within the {fieldrow} variable pair, the <dfn>Entries Tag</dfn> provides the following additional variables.</p>

<ul>
    <li><a href="#var_value_form_entry_id">{value:form_entry_id}</a></li>
    <li><a href="#var_value_set">{value:*}</a></li>
</ul>

<h5><a name="var_value_form_entry_id">{value:form_entry_id}</a></h5>
<p>Provides the unique ID of the current entry in the entries loop.</p>

<h5><a name="var_value_set">{value:*}</a></h5>
<p>One variable is created for each field that is submitted to the form. For instance, if you have a <kbd>first_name</kbd> field, a variable named <kbd>{value:first_name}</kbd> would be created to provide it&#39;s value.</p>

<p>Note however that it is often easier to use the {field_name} and {field_value} variables provided by the <a href="{site_url}documentation/proform/tags/form#var_fields">{fields} variable pair</a> to render a template with values from the entry. This way, a single template can render entries for any of your forms.</p>

<h2><a name="sample_template">Sample Template Code</a></h2>
<p>Here&#39;s a simple example of using the <dfn>Entries Tag</dfn> to display all entries created in a particular form:</p>

<p class="strongbox">Example Usage</p>
<pre class="brush: xml">
&#123;exp:proform:entries form_name="{segment_2}"&#125;
  &lt;tr&gt;
    &lt;th&gt;Row Number&lt;/th&gt;
    &lt;th&gt;Form Entry ID&lt;/th&gt;
    &lt;th&gt;User Agent&lt;/th&gt;
    &lt;th&gt;IP Address&lt;/th&gt;
    &#123;fields&#125;
      &lt;th&gt;{field_label}&lt;/th&gt;
    &#123;/fields&#125;
  &lt;/tr&gt;
  &#123;if entries:no_results&#125;
    &lt;tr&gt;
      &lt;td colspan="100" class="error"&gt;No entries!&lt;/td&gt;
    &lt;/tr&gt;
  &#123;if:else&#125;
    &lt;tr&gt;
      &lt;td&gt;{row:number}&lt;/td&gt;
      &lt;td&gt;{value:form_entry_id}&lt;/td&gt;
      &lt;td&gt;{value:user_agent}&lt;/td&gt;
      &lt;td&gt;{value:ip_address}&lt;/td&gt;
      &#123;fields&#125;
        &#123;if field_type == &#39;file&#39;&#125;
          &#123;if field_value&#125;
            &lt;td&gt;
              &lt;img src="{field_value}" /&gt;
            &lt;/td&gt;
          &#123;if:else&#125;
            &lt;td&gt;&lt;/td&gt;
          &#123;/if&#125;
        &#123;if:else&#125;
          &lt;td&gt;
            &#123;field_value&#125;
          &lt;/td&gt;
        &#123;/if&#125;
      &#123;/fields&#125;
    &lt;/tr&gt;
  &#123;/if&#125;
&#123;/exp:proform:entries&#125;
</pre>

{ce:core:include template="global/_footer"}