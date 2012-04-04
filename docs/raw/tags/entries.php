---
entry_id: '23'
site_id: '1'
channel_id: '4'
author_id: '1'
pentry_id: '0'
forum_topic_id: null
ip_address: 0.0.0.0
title: 'Entries Tag'
url_title: proform-entries-tag
status: open
versioning_enabled: y
view_count_one: '0'
view_count_two: '0'
view_count_three: '0'
view_count_four: '0'
allow_comments: y
sticky: n
entry_date: '1325181076'
dst_enabled: n
year: '2011'
month: '12'
day: '29'
expiration_date: '0'
comment_expiration_date: '0'
edit_date: '20111229185617'
recent_comment_date: '0'
comment_total: '0'
last_written: '0'
fe_type: entry
---
<h1>
	Entries Tag (BETA)</h1>
<p>
	The <kbd>Entries Tag</kbd> provides a way to query and display any entry created through ProForm.</p>
<p class="tip">
	<b>Beta</b> This module is currently in a public beta phase, but has been used on multiple sites in an internal beta for several months. Any issues should be filed with the developer at the Devot:ee site for <kbd>ProForm</kbd> and will be fixed promptly.</p>
<h2>
	Contents</h2>
<ul>
	<li>
		<a href="#parameters">Parameters</a></li>
	<li>
		<a href="#variables">Variables</a></li>
	<li>
		<a href="#variable_pairs">Variable Pairs</a></li>
	<li>
		<a href="#sample_template">Sample Template Code</a></li>
</ul>
<h2>
	<a name="parameters">Parameters</a></h2>
<p>
	The following parameters are available:</p>
<ul>
	<li>
		<a href="#param_entry_id">entry_id="1"</a></li>
	<li>
		<a href="#param_form">form="contact_us"</a></li>
</ul>
<h3>
	<a name="param_entry_id">entry_id="1"</a></h3>
<p>
	The <b>entry_id</b> parameter allows you to filter the results to a single entry.</p>
<h3>
	<a name="param_form">form="contact_us"</a></h3>
<p>
	The <b>form</b> parameter allows you to filter the results to those form a particular form.</p>
<h2>
	<a name="variables">Single Variables</a></h2>
<p>
	All of the <a href="{site_url}documentation/proform/tags/form#variables">single variables</a> available in the <kbd>Form Tag</kbd> are also available in the <dfn>Entries Tag</dfn>.</p>
<p>
	Additionally, the following variables are available in the <dfn>Entries Tag</dfn> and notification templates:</p>
<ul>
	<li>
		<a href="#var_value_user_agent">{value:user_agent}</a></li>
	<li>
		<a href="#var_value_ip_address">{value:ip_address}</a></li>
</ul>
<h3>
	<a name="var_value_user_agent">{value:user_agent}</a></h3>
<p>
	The User-Agent header as sent by the visitor&#39;s browser when the entry was saved.</p>
<h3>
	<a name="var_value_ip_address">{value:ip_address}</a></h3>
<p>
	The IP address as sent by the visitor&#39;s browser when the entry was saved.</p>
<h2>
	<a name="variable_pairs">Variable Pairs</a></h2>
<p>
	All of the <a href="{site_url}documentation/proform/tags/form#variable_pairs">variable pairs</a> available in the <kbd>Form Tag</kbd> are also available in the <dfn>Entries Tag</dfn>.</p>
<p>
	Some additional values are available within these variable pairs.</p>
<h3>
	<a name="forms">{fields}</a></h3>
<p>
	In addition to the normal variables available within the {fields} variable pair, both at the root of the <dfn>Entries Tag</dfn> and within the {fieldrow} variable pair, the <dfn>Entries Tag</dfn> provides the following additional variables.</p>
<ul>
	<li>
		<a href="#var_value_form_entry_id">{value:form_entry_id}</a></li>
	<li>
		<a href="#var_value_set">{value:*}</a></li>
</ul>
<h5>
	<a name="var_value_form_entry_id">{value:form_entry_id}</a></h5>
<p>
	Provides the unique ID of the current entry in the entries loop.</p>
<h5>
	<a name="var_value_set">{value:*}</a></h5>
<p>
	One variable is created for each field that is submitted to the form. For instance, if you have a <kbd>first_name</kbd> field, a variable named <kbd>{value:first_name}</kbd> would be created to provide it&#39;s value.</p>
<p>
	Note however that it is often easier to use the {field_name} and {field_value} variables provided by the <a href="{site_url}documentation/proform/tags/form#var_fields">{fields} variable pair</a> to render a template with values from the entry. This way, a single template can render entries for any of your forms.</p>
<h2>
	<a name="sample_template">Sample Template Code</a></h2>
<p>
	Here&#39;s a simple example of using the <dfn>Entries Tag</dfn> to display all entries created in a particular form:</p>
<pre>
{exp:proform:entries form_name="{segment_2}"}&#10;    &lt;tr&gt;&#10;        &lt;th&gt;Row Number&lt;/th&gt;&#10;        &lt;th&gt;Form Entry ID&lt;/th&gt;&#10;        &lt;th&gt;User Agent&lt;/th&gt;&#10;        &lt;th&gt;IP Address&lt;/th&gt;&#10;        {fields}&#10;        &lt;th&gt;{field_label}&lt;/th&gt;&#10;        {/fields}&#10;    &lt;/tr&gt;&#10;&#10;    {if entries:no_results}&#10;    &lt;tr&gt;&#10;       &lt;td colspan="100" class="error"&gt;No entries!&lt;/td&gt;&#10;    &lt;/tr&gt;&#10;    {if:else}&#10;    &lt;tr&gt;&#10;        &lt;td&gt;{row:number}&lt;/td&gt;&#10;        &lt;td&gt;{value:form_entry_id}&lt;/td&gt;&#10;        &lt;td&gt;{value:user_agent}&lt;/td&gt;&#10;        &lt;td&gt;{value:ip_address}&lt;/td&gt;&#10;        {fields}&#10;            {if field_type == &#39;file&#39;}&#10;                {if field_value}&#10;                    &lt;td&gt;&lt;img src="{field_value}" /&gt;&lt;/td&gt;&#10;                {if:else}&#10;                    &lt;td&gt;&lt;/td&gt;&#10;                {/if}&#10;            {if:else}&#10;                &lt;td&gt;{field_value}&lt;/td&gt;&#10;            {/if}&#10;        {/fields}&#10;    &lt;/tr&gt;&#10;    {/if}&#10;{/exp:proform:entries}</pre>
