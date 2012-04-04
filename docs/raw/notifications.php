---
entry_id: '24'
site_id: '1'
channel_id: '4'
author_id: '1'
pentry_id: '0'
forum_topic_id: null
ip_address: 0.0.0.0
title: Notifications
url_title: proform-notifications
status: open
versioning_enabled: y
view_count_one: '0'
view_count_two: '0'
view_count_three: '0'
view_count_four: '0'
allow_comments: y
sticky: n
entry_date: '1325181478'
dst_enabled: n
year: '2011'
month: '12'
day: '29'
expiration_date: '0'
comment_expiration_date: '0'
edit_date: '20111229185759'
recent_comment_date: '0'
comment_total: '0'
last_written: '0'
fe_type: entry
---
<h1>
	Notification Templates</h1>
<p class="tip">
	<b>Beta</b> This module is currently in a public beta phase, but has been used on multiple sites in an internal beta for several months. Any issues should be filed with the developer at the Devot:ee site for <dfn>ProForm</dfn> and will be fixed promptly.</p>
<p>
	Notification templates in <dfn>ProForm</dfn> are based on the same ExpressionEngine templates that you use to build any page on your site. Because of this, they can make use of any installed Module or Plugin, and have full conditionals support.</p>
<p>
	If you do not need a fancy display or other information about the entry, this is probably all you will need - a single template can render notifications for any form submitted to <dfn>ProForm</dfn>.</p>
<h2>
	Single Variables</h2>
<p>
	All of the <a href="{site_url}documentation/proform/tags/entries#variables">variables</a> available in the <kbd>Entries Tag</kbd> are available in the notification template when notifications are generated for a newly created entry.</p>
<h2>
	Variable Pairs</h2>
<p>
	Similarly, all of the <a href="{site_url}documentation/proform/tags/entries#variable_pairs">variable pairs</a> available in the <kbd>Entries Tag</kbd> are available in the notification template when notifications are generated for a newly created entry.</p>
<h2>
	Sample Template</h2>
<p>
	One of the simplest possible notification templates looks like this:</p>
<pre>
Someone submitted the {form_name} form:&#10;&#10;{fields}&#10;    {field_label}: {field_value}&#10;{/fields}</pre>
