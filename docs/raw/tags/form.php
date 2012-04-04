{ce:core:include template="global/_header"}

<h1>Form Tags</h1>

<p><dfn>ProForm</dfn> provides two variations of it's form tag. The goal of the two types of <kbd>Form Tags</kbd> are to allow a single template to render any form created through the <dfn>ProForm</dfn> drag and drop layout editor, with differing degrees of control over the output.</p>

<ul>
    <li>{exp:proform:simple} - the <kbd>Simple Form Tag</kbd> - this tag allows you to render any form with one line of code</li>
    <li>{exp:proform:full} - the <kbd>Full Form Tag</kbd> - this tag allows you to custom markup for any form</li>
</ul>

<!-- ********************************************************************** -->

<h2>Simple Tag</h2>

<p>The <kbd>Simple Form Tag</kbd> - <b>{exp:proform:simple}</b> - allows you to render a complete form built through the drag and drop interface with a single line of template code.</p>

<ul>
    <li><a href="#simple_parameters">Parameters</a></li>
    <li><a href="#simple_variables">Variables</a></li>
    <li><a href="#simple_variables">Variable Pairs</a></li>
</ul>

<h2><a name="simple_parameters">Parameters</a></h2>
<p>Of the parameters supported by the <kbd>Full Form Tag</kbd>, the simple variation supports the following option:</p>

<ul>
    <li><a href="#param_form">form="contact_us"</a></li>
</ul>

<h2><a name="simple_variables">Simple Tag - Variables</a></h2>

<p>The <kbd>Simple Form Tag</kbd> supports the same  <a href="#variables">Single Variables</a> and <a href="#variable_pairs">Variables Pairs</a> supported by the full tag.</p>

<h2><a name="simple_sample">Simple Tag - Sample Template Code</a></h2>

<p>Using the <kbd>Simple Form Tag</kbd> couldn't be easier:</p>

<p class="strongbox">Example Template</p>
<pre class="brush: xml">
&#123;exp:proform:simple form_name="contact_us" &#125;
</pre>

<p>That's it! You're done. If you'd like more control over the markup of the template, you can make use of the full template tag.</p>

<h2>Full Tag</h2>
<p>The <kbd>Full Form Tag</kbd> - <b>{exp:proform:form}</b> - provides you with precise tools to control the exact HTML produced for your forms built through ProForm. Like the Simple Tag, it is capable of rendering any form created through the drag and drop interface.</p>

<ul>
    <li><a href="#parameters">Parameters</a></li>
    <li><a href="#variables">Variables</a></li>
    <li><a href="#variable_pairs">Variable Pairs</a></li>
    <li><a href="{root_url}tags/form/template.html">Sample Template</a></li>
</ul>

<!-- ********************************************************************** -->

<h2><a name="parameters">Full Parameters</a></h2>
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
{form_name}_profom&#10;
</pre>

<h3><a name="param_form_class">form_class="form_layout_wide"</a></h3>

<p>The similarly to the <b>form_id</b> parameter, the <b>form_class</b> parameter allows you to override the default form class attached to the generated &lt;form&gt; tag.</p>

<p>The default form class takes the following form:</p>

<pre class="brush: xml">
{form_name}_profom&#10;
</pre>

<h3><a name="param_form_url">form_url="forms/contact-us"</a></h3>

<p>The <b>form_url</b> parameter allows you to override what URL the form is set to post to.</p>

<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> handles the form submission when it sees the form&#39;s POST data from the browser.</p>

<h3><a name="param_error_url">error_url="forms/contact-us/error"</a></h3>

<p>The <b>error_url</b> parameter allows you to override what URL the form redirected to when there is an error detected in the form&#39;s <a href="{root_url}cp/fields.html#validation_filtering">validation</a>.</p>

<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> is not only used to display the form initially, but also used to display the form with error messages attached.</p>

<h3><a name="param_thank_you_url">thank_you_url="forms/thank-you"</a></h3>

<p>The <b>thank_you_url</b> parameter allows you to override what URL the form redirected to after it's data has been saved to the database.</p>

<p>Typically, you will want to create a single thank you template to thank visitors for submitting all forms. This template should make use of the <a href="{root_url}tags/results.html">Results Tag</a> to retrieve information about the particular form submitted.</p>

<h3><a name="param_notify">notify="sample@example.com"</a></h3>

<p>The <b>notify</b> parameter allows you to add additional email addresses to be sent the notification as configured on the form&#39;s <a href="{root_url}cp/forms.html#form_settings_notification_list">Notification List</a> settings. Note that this requires that the Notification List settings be properly configured in order to operate.</p>

<p>Multiple email addresses may be separated by a pip character:</p>

<p><dfn>notify="sample1@example.com|sample2@example.com"</dfn></p>

<p class="tip">Based on other form systems you may have used in ExpressionEngine, your first tendency may be to hard-code email addresses into your form templates using this parameter, but we strongly advise that you attempt to make use of the Notification List by itself and place all email addresses to send notifications to in the form's configuration instead.</p>

<h3><a name="param_custom">Custom Params</a></h3>

<p>Any additional params sent to the <kbd>Form Tag</kbd> will be packed into the form configuration value and sent along with the rest of the form&#39;s data when it is submitted. This is a secure way to pass additional information to the thank you or error templates.</p>

<p>The next two parameters in particular are specifically handled in a way to allow you to create form that act as a gate for a file download.</p>

<h3><a name="param_download_url">download_url="downloads/confirm"</a></h3>

<p>This documentation section is still under development.</p>

<h3><a name="param_download_label">download_label="Download available"</a></h3>

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

<h3><a name="var_form_id">{form_id}</a></h3>

<p>Provides the unique numeric ID of the form.</p>

<h3><a name="var_form_label">{form_label}</a></h3>

<p>Provides the friendly form label for the form. This can be any human readable string.</p>

<h3><a name="var_form_name">{form_name}</a></h3>

<p>Provides the internal form name for the form. This should be all lowercase and only contained letters, numbers, and underscores.</p>

<h3><a name="var_form_type">{form_type}</a></h3>

<p>Provides the type of the form being rendered. This is either set "form" for <dfn>Basic Forms</dfn> or "share" for <dfn>Share Forms</dfn>. For more about the types of forms available and their behavior, see the <a href="{root_url}cp/forms.html#create_form">Creating a New Form</a> section.</p>

<h3><a name="var_fields_count">{fields_count}</a></h3>

<p>Provides the total number of fields assigned to the form.</p>

<h3><a name="var_complete">{complete}</a></h3>

<p>If the form has been successfully processed and the thank_you_url was set to the same URL as the form itself (the default), this value will be set to be true so that you may display a thank you message instead of or in addition to the blank form.</p>

<h3><a name="var_error_count">{error_count}</a></h3>

<p>Provides the total number of fields that contain errors on the form. If the form has just been loaded for the first time and there are no errors, this will be set to 0. A conditional on this variable is the easiest way to determine if the template is loading an initial form view or loading a failed attempt to submit the form.</p>

<!-- ********************************************************************** -->
<h2><a name="variable_pairs">Variable Pairs</a></h2>

<p>The following variable pair are available within the <kbd>Form Tag</kbd>:</p>

<ul>
    <li><a href="#var_errors">{errors}</a></li>
    <li><a href="#var_fields">{fields}</a></li>
    <li><a href="#var_fieldrows">{fieldrows}</a></li>
</ul>

<h2><a name="var_errors">{errors}</a></h2>

<p>The <b>errors</b> variable pair provides a list of any errors that were detected when submitting the form.</p>

<p class="strongbox">Example Usage</p>
<pre class="brush: xml">
    &#123;if error_count&#125;
    &lt;div class="errors"&gt;
        &lt;p>The form has the following errors:&lt;/p&gt;
        &lt;ul&gt;
        &#123;errors&#125;
            &lt;li>&#123;error&#125;&lt;/li&gt;
        &#123;/errors&#125;
        &lt;/ul&gt;
    &lt;/div&gt;
    &#123;/if&#125;
</pre>


<h2><a name="var_fields">{fields}</a></h2>

<p>The <b>fields</b> variable pair provides a list of all of the fields in the selected form, regardless of the row they are set on.</p>

<p class="strongbox">Example Usage</p>

<p>See the full <a href="{root_url}tags/form/template.html">Sample Template</a> for the <kbd>Form Tag</kbd> to see how to use the {fields} variable pair.</p>


<h4>{fields} Variables</h4>
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

<h4>{fields} Variable Pairs</h4>
<p>The following variable pairs are available inside the {fields} pair.</p>

<ul>
    <li><a href="#pair_field_setting_list">{field_setting_list}</a></li>
</ul>

<h5>{fields} Single Variables</h5>

<h6><a name="var_field_id">{field_id}</a></h6>

<p>Provides the unique ID of the field.</p>

<h6><a name="var_field_name">{field_name}</a></h6>

<p>Provides the short alpha numeric name of the field. This should be used as the input or textarea element&#39;s name parameter in order to have <dfn>ProForm</dfn> process the field&#39;s value correctly.</p>

<h6><a name="var_field_label">{field_label}</a></h6>

<p>A human friendly label for the field, typically contained within a label tag attached to the input element.</p>

<h6><a name="var_field_type">{field_type}</a></h6>

<p>The internal type name of the field. A conditional on this value can allow you to present custom rendering for any of the built-in field types.</p>

<h6><a name="var_field_length">{field_length}</a></h6>

<p>The constraint set for this field. Defaults to 255 characters for all field types.</p>

<h6><a name="var_field_is_required">{field_is_required}</a></h6>

<p>A flag indicating if this field is required or not. A conditional on this field will allow you to flag the field with a character or other visual indication that the field is required.</p>

<h6><a name="var_field_error">{field_error}</a></h6>

<p>Presents a string value of an error for this field. If there is no error detected in the field, or if it is the initial form load, this value will be blank.</p>

<h6><a name="var_field_value">{field_value}</a></h6>

<p>The value of the field as it was submitted. Use this to preserve values when errors have occurred in other fields.</p>

<h6><a name="var_field_checked">{field_checked}</a></h6>

<p>A flag indicating if the field is checked. Use this to preserve values when errors have occurred in other fields.</p>

<h6><a name="var_field_control">{field_control}</a></h6>

<p>The suggested input type to use to render this field. This will be set to one of the valid HTML input type values ("text" or "file"), or "textarea".</p>

<h6><a name="var_preset_value">{field_preset_value}</a></h6>

<p>A preset value for this field as set in the Default Value field of the form&#39;s layout editor.</p>

<h6><a name="var_field_html_id">{field_html_id}</a></h6>

<p>A HTML ID value for this field as set in the form&#39;s layout editor.</p>

<h6><a name="var_field_html_class">{field_html_class}</a></h6>

<p>A HTML class value for this field as set in the form&#39;s layout editor.</p>

<h6><a name="var_extra_1">{field_extra_1}</a></h6>

<p>An extra meta value for this field as set in the form&#39;s layout editor.</p>

<h6><a name="var_extra_1">{field_extra_2}</a></h6>

<p>An extra meta value for this field as set in the form&#39;s layout editor.</p>

<h6><a name="var_pair_field_setting_multiselect">{field_setting_multiselect}</a></h6>

<p><strong>Only valid for 'select' type fields</strong>. This field is set to "y" if the select is a multiselect, or a blank value otherwise.</p>

<h5>{fields} Variable Pairs</h5>

<h6><a name="pair_field_setting_list">{field_setting_list}</a></h6>
<p><strong>Only valid for 'select' type fields</strong>. The {field_setting_list} loop, which must be used inside of the {fields} pair, provides a list of all of the options availalbe for fields with the type <b>list</b>.</p>

<p>This variable pair has two variables available for each option:</p>

<ul>
  <li><strong>{key}</strong> - the value as stored in the database</li>
  <li><strong>{row}</strong> - the label of the value shown to the user</li>
</ul>

<p class="strongbox">Example Usage</p>
<pre class="brush: xml">
  &#123;fields&#125;
    &#123;if field_control == 'select'&#125;
      &lt;select id="{field_name}" name="{field_name}"&gt;
        &#123;field_setting_list&#125;
        &lt;option value="{key}"&gt;&#123;row&#125;&lt/option&gt;
        &#123;/field_setting_list&#125;
      &lt;/select&gt;
    &#123;/if&#125;
  &#123;/fields&#125;


</pre>

<h2><a name="var_fieldrows">{fieldrows}</a></h2>
<p>The <b>fieldrows</b> variable pair provides a list of the selected form&#39;s field rows. Each row contains a nested {fields} list that provides a list of only the fields on each individual row. See the <a href="#var_fields">{fields}</a> documentation for more information on the data available inside the nested fields pair.</p>

<p>To preserve the view of the form as presented in the layout editor, this loop should create for each row a new strip of fields - typically in the form of a &lt;ul&gt; element with it&#39;s &lt;li&gt; elements floated to the left. See the <a href="{root_url}tags/form/template.html">Sample Template</a> for one way this can be accomplished</p>

<!-- ********************************************************************** -->
<h2><a name="sample_template_code">Sample Template Code</a></h2>

<p>See the full <a href="{root_url}tags/form/template.html">Sample Template</a> for the <kbd>Form Tag</kbd>.</p>


{ce:core:include template="global/_footer"}