{ce:core:include template="global/_header"}

<h1>Form Tag</h1>
<p>The <kbd>Form Tag</kbd> is the main tag used by a <dfn>ProForm</dfn> template to render a form. The goal of the <kbd>Form Tag</kbd> is to allow a single template to render any form created through the <dfn>ProForm</dfn> drag and drop layout editor.</p>

<!-- ********************************************************************** -->

<h2>Contents</h2>
<ul>
	<li><a href="#parameters">Parameters</a></li>
	<li><a href="#variables">Variables</a></li>
	<li><a href="#variable_pairs">Variable Pairs</a></li>
	<li><a href="template/">Sample Template</a></li>
</ul>

<!-- ********************************************************************** -->
<h2><a name="parameters">Parameters</a></h2>
<p>The following parameters are available:</p>
<ul>
	<li><a href="#param_form">form="contact_us"</a></li>
	<li><a href="#param_form_id">form_id="form_contact_us"</a></li>
	<li><a href="#param_form_class">form_class="form_layout_wide"</a></li>
	<li><a href="#param_form_url">form_url="forms/contact-us"</a></li>
	<li><a href="#param_error_url">error_url="forms/contact-us/error"</a></li>
	<li><a href="#param_thank_you_url">thank_you_url="forms/thank-you"</a></li>
	<li><a href="#param_notify">notify="sample@example.com"</a></li>
	<li><a href="#param_custom">Custom Params</a>
		<ul>
			<li><a href="#param_download_url">download_url="downloads/confirm"</a></li>
			<li><a href="#param_download_label">download_label="Download available"</a></li>
		</ul>
	</li>
</ul>

<h3><a name="param_form">form="contact_us"</a></h3>
<p>The <b>form</b> parameter is used to specify which form&#39;s information should be loaded. Typically this value can be taken from a URL segment, or from an entry field.</p>

<p>The first approach allows you to create a single URL that can serve all templates - such as <dfn>/forms/contact-us</dfn> - while the second allows your forms to be placed arbitrarily on any other template. Using this tag within an embed template would then allow the display logic to be used anywhere on the site.</p>

<h3><a name="param_form_id">form_id="form_contact_us"</a></h3>
<p>The <b>form_id</b> parameter allows you to override the default form ID generated in the resulting &lt;form&gt; tag.</p>

<p>The default form ID takes the following form:</p>

<pre class="brush: xml">
{form_name}_profom&#10;</pre>

<h3>
	<a name="param_form_class">form_class="form_layout_wide"</a></h3>
<p>The similarly to the <b>form_id</b> parameter, the <b>form_class</b> parameter allows you to override the default form class attached to the generated &lt;form&gt; tag.</p>
<p>The default form class takes the following form:</p>
<pre class="brush: xml">
{form_name}_profom&#10;</pre>

<h3><a name="param_form_url">form_url="forms/contact-us"</a></h3>
<p>The <b>form_url</b> parameter allows you to override what URL the form is set to post to.</p>
<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> handles the form submission when it sees the form&#39;s POST data from the browser.</p>
<h3>
	<a name="param_error_url">error_url="forms/contact-us/error"</a></h3>
<p>The <b>error_url</b> parameter allows you to override what URL the form redirected to when there is an error detected in the form&#39;s <a href="cp_fields.html#validation_filtering">validation</a>.</p>
<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> is not only used to display the form initially, but also used to display the form with error messages attached.</p>
<h3>
	<a name="param_thank_you_url">thank_you_url="forms/thank-you"</a></h3>
<p>The <b>thank_you_url</b> parameter allows you to override what URL the form redirected to after it&#39;s data has been saved to the database.</p>
<p>Typically, you will want to create a single thank you template to thank visitors for submitting all forms. This template should make use of the <kbd>Results Tag</kbd> to retrieve information about the particular form submitted.</p>
<h3>
	<a name="param_notify">notify="sample@example.com"</a></h3>
<p>The <b>notify</b> parameter allows you to add additional email addresses to be sent the notification as configured on the form&#39;s <a href="{site_url}documentation/proform/cp/forms#form_settings_notification_list">Notification List</a> settings. Note that this requires that the Notification List settings be properly configured in order to operate.</p>
<p>Multiple email addresses may be separated by a pip character:</p>
<p><dfn>notify="sample1@example.com|sample2@example.com"</dfn></p>
<p class="tip">
	Based on other form systems you may have used in ExpressionEngine, your first tendency may be to hard-code email addresses into your form templates using this parameter, but we strongly advise that you attempt to make use of the Notification List by itself and place all email addresses to send notifications to in the form&#39;s configuration instead.</p>
<h3>
	<a name="param_custom">Custom Params</a></h3>
<p>Any additional params sent to the <kbd>Form Tag</kbd> will be packed into the form configuration value and sent along with the rest of the form&#39;s data when it is submitted. This is a secure way to pass additional information to the thank you or error templates.</p>
<p>The next two parameters in particular are specifically handled in a way to allow you to create form that act as a gate for a file download.</p>
<h3>
	<a name="param_download_url">download_url="downloads/confirm"</a></h3>
<p>This documentation section is still under development.</p>
<h3>
	<a name="param_download_label">download_label="Download available"</a></h3>
<p>This documentation section is still under development.</p>
<!-- ********************************************************************** -->
<h2><a name="variables">Single Variables</a></h2>
<p>The following variables are available within the <kbd>Form Tag</kbd>:</p>
<ul>
	<li><a href="#var_form_id">{form_id}</a></li>
	<li><a href="#var_form_label">{form_label}</a></li>
	<li><a href="#var_form_name">{form_name}</a></li>
	<li><a href="#var_form_type">{form_type}</a></li>
	<li><a href="#var_fields_count">{fields_count}</a></li>
	<li><a href="#var_complete">{complete}</a></li>
	<li><a href="#var_error_count">{error_count}</a></li>
	<!-- <li><a href="#var_value_set">{value:*}</a></li>
    <li><a href="#var_error_set">{error:*}</a></li>
    <li><a href="#var_checked_set">{checked:*}</a></li> -->
</ul>
<h3>
	<a name="var_form_id">{form_id}</a></h3>
<p>Provides the unique numeric ID of the form.</p>
<h3>
	<a name="var_form_label">{form_label}</a></h3>
<p>Provides the friendly form label for the form. This can be any human readable string.</p>
<h3>
	<a name="var_form_name">{form_name}</a></h3>
<p>Provides the internal form name for the form. This should be all lowercase and only contained letters, numbers, and underscores.</p>
<h3>
	<a name="var_form_type">{form_type}</a></h3>
<p>Provides the type of the form being rendered. This is either set "form" for <dfn>Basic Forms</dfn> or "share" for <dfn>Share Forms</dfn>. For more about the types of forms available and their behavior, see the <a href="{site_url}documentation/proform/cp/forms#create_form">Creating a New Form</a> section.</p>
<h3>
	<a name="var_fields_count">{fields_count}</a></h3>
<p>Provides the total number of fields assigned to the form.</p>
<h3>
	<a name="var_complete">{complete}</a></h3>
<p>If the form has been successfully processed and the thank_you_url was set to the same URL as the form itself (the default), this value will be set to be true so that you may display a thank you message instead of or in addition to the blank form.</p>
<h3>
	<a name="var_error_count">{error_count}</a></h3>
<p>Provides the total number of fields that contain errors on the form. If the form has just been loaded for the first time and there are no errors, this will be set to 0. A conditional on this variable is the easiest way to determine if the template is loading an initial form view or loading a failed attempt to submit the form.</p>
<!-- ********************************************************************** -->
<h2><a name="variable_pairs">Variable Pairs</a></h2>
<p>The following variable pair are available within the <kbd>Form Tag</kbd>:</p>
<ul>
	<li><a href="#var_errors">{errors}</a></li>
	<li><a href="#var_fields">{fields}</a></li>
	<li><a href="#var_fieldrows">{fieldrows}</a></li>
</ul>
<h3>
	<a name="var_fields">{fields}</a></h3>
<p>The <b>fields</b> variable pair provides a list of all of the fields in the selected form, regardless of the row they are set on.</p>
<p>The following variables are available within each row of the {fields} variable pair.</p>
<ul>
	<li><a href="#var_field_id">{field_id}</a></li>
	<li><a href="#var_field_name">{field_name}</a></li>
	<li><a href="#var_field_label">{field_label}</a></li>
	<li><a href="#var_field_type">{field_type}</a></li>
	<li><a href="#var_field_length">{field_length}</a></li>
	<li><a href="#var_field_is_required">{field_is_required}</a></li>
	<li><a href="#var_field_validation">{field_validation}</a></li>
	<li><a href="#var_field_error">{field_error}</a></li>
	<li><a href="#var_field_value">{field_value}</a></li>
	<li><a href="#var_field_checked">{field_checked}</a></li>
	<li><a href="#var_field_control">{field_control}</a></li>
	<li><a href="#var_field_preset_value">{field_preset_value}</a></li>
	<li><a href="#var_field_html_id">{field_html_id}</a></li>
	<li><a href="#var_field_html_class">{field_html_class}</a></li>
	<li><a href="#var_field_extra_1">{field_extra_1}</a></li>
	<li><a href="#var_field_extra_2">{field_extra_2}</a></li>
</ul>

<h5><a name="var_field_id">{field_id}</a></h5>
<p>Provides the unique ID of the field.</p>

<h5><a name="var_field_name">{field_name}</a></h5>
<p>Provides the short alpha numeric name of the field. This should be used as the input or textarea element&#39;s name parameter in order to have <dfn>ProForm</dfn> process the field&#39;s value correctly.</p>

<h5><a name="var_field_label">{field_label}</a></h5>
<p>A human friendly label for the field, typically contained within a label tag attached to the input element.</p>

<h5><a name="var_field_type">{field_type}</a></h5>
<p>The internal type name of the field. A conditional on this value can allow you to present custom rendering for any of the built-in field types.</p>

<h5><a name="var_field_length">{field_length}</a></h5>
<p>The constraint set for this field. Defaults to 255 characters for all field types.</p>

<h5><a name="var_field_is_required">{field_is_required}</a></h5>
<p>A flag indicating if this field is required or not. A conditional on this field will allow you to flag the field with a character or other visual indication that the field is required.</p>

<h5><a name="var_field_error">{field_error}</a></h5>
<p>Presents a string value of an error for this field. If there is no error detected in the field, or if it is the initial form load, 
this value will be blank.</p>

<h5><a name="var_field_value">{field_value}</a></h5>
<p>The value of the field as it was submitted. Use this to preserve values when errors have occurred in other fields.</p>

<h5><a name="var_field_checked">{field_checked}</a></h5>
<p>A flag indicating if the field is checked. Use this to preserve values when errors have occurred in other fields.</p>

<h5><a name="var_field_control">{field_control}</a></h5>
<p>The suggested input type to use to render this field. This will be set to one of the valid HTML input type values ("text" or "file"), or "textarea".</p>

<h5><a name="var_preset_value">{field_preset_value}</a></h5>
<p>A preset value for this field as set in the Default Value field of the form&#39;s layout editor.</p>

<h5><a name="var_field_html_id">{field_html_id}</a></h5>
<p>A HTML ID value for this field as set in the form&#39;s layout editor.</p>

<h5><a name="var_field_html_class">{field_html_class}</a></h5>
<p>A HTML class value for this field as set in the form&#39;s layout editor.</p>

<h5><a name="var_extra_1">{field_extra_1}</a></h5>
<p>An extra meta value for this field as set in the form&#39;s layout editor.</p>

<h5><a name="var_extra_1">{field_extra_2}</a></h5>
<p>An extra meta value for this field as set in the form&#39;s layout editor.</p>

<h3><a name="var_fieldrows">{fieldrows}</a></h3>
<p>The <b>fieldrows</b> variable pair provides a list of the selected form&#39;s field rows. Each row contains a nested {fields} list that provides a list of only the fields on each individual row. See the <a href="#var_fields">{fields}</a> documentation for more information on the data available inside the nested fields pair.</p>

<p>To preserve the view of the form as presented in the layout editor, this loop should create for each row a new strip of fields - typically in the form of a &lt;ul&gt; element with it&#39;s &lt;li&gt; elements floated to the left. See the <a href="form/template">Sample Template</a> for one way this can be accomplished</p>

<!-- ********************************************************************** -->

<h2><a name="sample_template_code">Sample Template Code</a></h2>
<p>See the full <a href="template/">Sample Template</a> for the <kbd>Form Tag</kbd>.</p>

{ce:core:include template="global/_footer"}