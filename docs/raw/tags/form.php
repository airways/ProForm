{ce:core:include template="global/_header"}

<h1>Form Tags</h1>

<p><dfn>ProForm</dfn> provides two variations of it's form tag. The goal of the two types of <kbd>Form Tags</kbd> are to allow a single template to render any form created through the <dfn>ProForm</dfn> drag and drop layout editor, with differing degrees of control over the output.</p>

<ul>
    <li>{exp:proform:simple} - the <kbd>Simple Form Tag</kbd> - this tag allows you to render any form with one line of code</li>
    <li>{exp:proform:full} - the <kbd>Full Form Tag</kbd> - this tag allows you to customize the markup and behavior rendered forms</li>
</ul>

<!-- ********************************************************************** -->

<h2>Contents</h2>
<ul>
    <li><a href="#simple">Simple Form Tag</a></li>
    <li><a href="#full">Full Form Tag</a></li>
</ul>

<!-- ********************************************************************** -->

<h2><a name="simple">Simple Tag</a></h2>

<p>The <kbd>Simple Form Tag</kbd> - <b>{exp:proform:simple}</b> - allows you to render a complete form built through the drag and drop interface with a single line of template code.</p>


<p>In fact, the <kbd>Simple Form Tag</kbd> actually calls the <kbd>Full Form Tag</kbd> behind the scenes to generate it's markup. <dfn>ProForm</dfn> will never force any particular markup on you - you should have full control over the layout and implementation of your forms, if you so desire. At the same time, we provide the default markup in order to allow a easier time getting started with the add-on if your design does not require a custom layout.</p>


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

<h2><a name="simple_variables">Variables</a></h2>

<p>The <kbd>Simple Form Tag</kbd> supports the same  <a href="#variables">Single Variables</a> and <a href="#variable_pairs">Variables Pairs</a> supported by the full tag.</p>

<h2><a name="simple_sample">Sample Template Code</a></h2>

<p>Using the <kbd>Simple Form Tag</kbd> couldn't be easier:</p>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;exp:proform:simple form="contact_us" &#125;
    </pre>
</div>

<p>That's it! You're done. If you'd like more control over the markup of the template, you can make use of the <kbd>Full Form tag</kbd>.</p>

<h2><a name="full">Full Form Tag</a></h2>
<p>The <kbd>Full Form Tag</kbd> - <b>{exp:proform:form}</b> - provides you with precise tools to control the exact HTML produced for your forms built through ProForm. Like the Simple Tag, it is capable of rendering any form created through the drag and drop interface.</p>

<ul>
    <li><a href="#parameters">Parameters</a></li>
    <li><a href="#variables">Variables</a></li>
    <li><a href="#variable_pairs">Variable Pairs</a></li>
    <li><a href="{root_url}tags/form/template.html">Sample Template</a></li>
</ul>

<!-- ********************************************************************** -->

<h2><a name="parameters">Full Parameters</a></h2>
<p>The following parameters are available. All are optional, with the exception of the <a href="#param_form">form=""</a> parameter, which must be set to the name of the form you wish to render.</p>

<ul>
    <li><a href="#param_custom">Custom Params</a></li>
    <li><a href="#param_debug">debug="yes"</a></li>
    <li><a href="#param_error_delimiters">error_delimiters="&lt;p&gt;|&lt;/p&gt;"</a></li>
    <li><a href="#param_error_url">error_url="forms/contact-us/error"</a></li>
    <li><a href="#param_form">form="contact_us"</a></li>
    <li><a href="#param_form_class">form_class="form_layout_wide"</a></li>
    <li><a href="#param_form_id">form_id="form_contact_us"</a></li>
    <li><a href="#param_form_url">form_url="forms/contact-us"</a></li>
    <li><a href="#param_hidden_fields">hidden_fields="split"</a></li>
    <li><a href="#param_message">message:required="This field is required!", message:*=""</a></li>
    <li><a href="#param_notify">notify="sample@example.com"</a></li>
    <li><a href="#param_step">step="1"</a></li>
    <li><a href="#param_variable_prefix">param_variable_prefix="pf_"</a></li>
    <li><a href="#param_thank_you_url">thank_you_url="forms/thank-you"</a></li>
</ul>

<h3><a name="param_custom">Custom Params</a></h3>

<p>Any additional params sent to the <kbd>Form Tag</kbd> will be packed into the form configuration value and sent along with the rest of the form&#39;s data when it is submitted. This is a secure way to pass additional information to the thank you or error templates.</p>

<p>The next two parameters in particular are specifically handled in a way to allow you to create form that act as a gate for a file download.</p>

<h3><a name="param_debug">debug="yes"</a></h3>

<p>The <b>debug</b> parameter turns on debug mode for <dfn>ProForm</dfn>, which will provide some additional information in order to assist in tracing it's behavior.</p>

<h3><a name="param_error_delimiters">error_delimiters="&lt;p&gt;|&lt;/p&gt;"</a></h3>

<p>The <b>error_delimiters</b> parameter allows you to specify custom tags to wrap around each error message generated for a field. This parameter requires two values - which must be separated by a pipe character as shown. The left side of the pipe is used to start the error block, and the right side is used to end it. The default values are:</p>

<div class="tip">
    <h6>Default error_delimiters</h6>
    <pre class="brush: xml">
        error_delimiters='&lt;div class="error"&gt;|&lt;/div&gt;'
    </pre>
</div>

<h3><a name="param_error_url">error_url="forms/contact-us/error"</a></h3>

<p>The <b>error_url</b> parameter allows you to override what URL the form redirected to when there is an error detected in the form&#39;s <a href="{root_url}cp/fields.html#validation_filtering">validation</a>.</p>

<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> is not only used to display the form initially, but also used to display the form with error messages attached.</p>


<h3><a name="param_form">form="contact_us"</a></h3>

<p>The <b>form</b> parameter is used to specify which form&#39;s information should be loaded. Typically this value can be taken from a URL segment, or from an entry field.</p>

<div class="tip"><b>Example Usage</b><br/>
    Assuming the URL for all forms on a site is <b>http://example.com/site/forms/url-title</b>, such as <b>http://example.com/site/forms/request-quote</b>, the template could load the requested form from {segment_3} like so:
    <pre class="brush: xml">
    &#123;exp:proform:form form="{segment_3}"&#125;

    </pre>
    
    </div>

<p>The first approach allows you to create a single URL that can serve all templates - such as <dfn>/forms/contact-us</dfn> - while the second allows your forms to be placed arbitrarily on any other template. Using this tag within an embed template would then allow the display logic to be used anywhere on the site.</p>

<h3><a name="param_form_class">form_class="form_layout_wide"</a></h3>

<p>The similarly to the <b>form_id</b> parameter, the <b>form_class</b> parameter allows you to override the default form class attached to the generated &lt;form&gt; tag.</p>

<p>The default form class takes the following form:</p>

<div class="tip">
    <h6>Default form_class</h6>
    <pre class="brush: xml">
        form_class="{form_name}_profom"
    </pre>
</div>

<h3><a name="param_form_id">form_id="form_contact_us"</a></h3>

<p>The <b>form_id</b> parameter allows you to override the default form ID generated in the resulting &lt;form&gt; tag.</p>

<p>The default form ID takes the following form:</p>

<div class="tip">
    <h6>Default form_id</h6>
    <pre class="brush: xml">
        form_class="{form_name}_profom"
    </pre>
</div>
<h3><a name="param_form_url">form_url="forms/contact-us"</a></h3>

<p>The <b>form_url</b> parameter allows you to override what URL the form is set to post to.</p>

<p>The default value is the current URL. Normally this should not be changed, as the <kbd>Form Tag</kbd> handles the form submission when it sees the form&#39;s POST data from the browser.</p>


<h3><a name="param_hidden_fields">hidden_fields="split"</a></h3>

<p>The <b>hidden_fields</b> parameter allows you to switch behavior for hidden field types between two modes:</p>

<table>
    <tr><th>Mode</th><th>Description</th></tr>
    <tr><td>split</td><td>The new default behavior - all hidden fields will be available only through the {hidden_fields} variable pair. Hidden fields will not be listed in either the &#123;fieldset&#125; or &#123;fields&#125; variable pairs.</td></tr>
    <tr>
        <td>hybrid</td>
        <td>
            The old behavior - all hidden fields will be included in the &#123;fieldset&#125; and &#123;fields&#125; variable pairs. Their position is not predictable (which is why the new default mode was added), based on the order they were added to the form. Use of this mode is not recommended but may be required for older templates which rely on it.<br/><br/>
            Even in hybrid mode, the new &#123;hidden_fields&#125; variable pair is still available to use if you wish.
        </td>
    </tr>
</table>

<h3><a name="param_message">message:required="This field is required!", message:*=""</a></h3>

<p>The <b>message*</b> parameter allows you to specify a custom error message to be displayed for each validation rule. The * should be replaced with a <b>key</b> corresponding to the validation rule you wish to set the message for. You may use the <b>message:*</b> parameter many times.</p>

<p>See the list of <a href="{root_url}cp/fields.html#validation_rules">Validation Rules</a> for a list of what keys to use for each validation rule.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us" message:required="Please enter a value for this field!"&#125;
        ...
        &#123;/exp:proform:form&#125;
    </pre>
</div>

<h3><a name="param_notify">notify="sample@example.com"</a></h3>

<p>The <b>notify</b> parameter allows you to add additional email addresses to be sent the notification as configured on the form&#39;s <a href="{root_url}cp/forms.html#form_settings_notification_list">Notification List</a> settings. Note that this requires that the Notification List settings be properly configured in order to operate.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <p>Multiple email addresses may be separated by a pipe character:</p>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us" notify="sample1@example.com|sample2@example.com"&#125;
        ...
        &#123;/exp:proform:form&#125;
    </pre>
</div>

<div class="tip">
    <h6>But there is a better way!</h6>
    <p>Based on other form systems you may have used in ExpressionEngine, your first tendency may be to hard-code email addresses into your form templates using this parameter, but I strongly advise that you attempt to make use of the Notification List by itself and place all email addresses to send notifications to in the form's <a href="{root_url}cp/forms.html#form_settings_notification_list">Notification List Settings</a> instead.</p>
</div>

<h3><a name="param_step">step="1"</a></h3>

<p>The <b>step</b> parameter allows you to explicitly request a particular step be loaded for the form.</p>

<p>By default, <dfn>ProForm</dfn> manages the active step using a session and hidden values passed inside the form. This parameter allows you to override this default behavior and specify the step to load based on your own criteria.</p>


<h3><a name="param_variable_prefix">variable_prefix="pf_"</a></h3>

<p>The <b>variable_prefix</b> parameter is used to specify a prefix to add to all variables provided by <dfn>ProForm</dfn>.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us" variable_prefix="pf_"&#125;
        ...
        &#123;/exp:proform:form&#125;

    </pre>
</div>

<p>For example, the following variables are renamed as shown when a prefix of <b>pf_</b> is used as in the example above:</p>

<table>
    <tr><th>Normal Name</th><th>Name With pf_ Prefix</th></tr>
    <tr><td>&#123;form_name&#125;</td><td>&#123;pf_form_name&#125;</td></tr>
    <tr><td>&#123;form_type&#125;</td><td>&#123;pf_form_type&#125;</td></tr>
    <tr><td>&#123;complete&#125;</td><td>&#123;pf_complete&#125;</td></tr>
    <tr><td>&#123;errors&#125;</td><td>&#123;pf_errors&#125;</td></tr>
    <tr><td>&#123;fields&#125;</td><td>&#123;pf_fields&#125;</td></tr>

    <tr><td>&#123;field_value&#125;</td><td>&#123;pf_field_value&#125;</td></tr>

</table>

<h3><a name="param_thank_you_url">thank_you_url="forms/thank-you"</a></h3>

<p>The <b>thank_you_url</b> parameter allows you to override what URL the form redirected to after it's data has been saved to the database.</p>

<p>Typically, you will want to create a single thank you template to thank visitors for submitting all forms. This template should make use of the <a href="{root_url}tags/results.html">Results Tag</a> to retrieve information about the particular form submitted.</p>




<!-- ********************************************************************** -->
<h2><a name="variables">Single Variables</a></h2>

<p>The following variables are available within the <kbd>Form Tag</kbd>:</p>

<ul>
    <li><a href="#var_captcha">&#123;captcha&#125;, &#123;use_captcha&#125;</a></li>
    <li><a href="#var_checked">&#123;checked:*&#125;</a></li>
    <li><a href="#var_complete">&#123;complete&#125;</a></li>
    <li><a href="#var_error_count">&#123;error_count&#125;</a></li>
    <li><a href="#var_error">&#123;error:*&#125;</a></li>
    <li><a href="#var_form_id">&#123;form_id&#125;</a></li>
    <li><a href="#var_form_label">&#123;form_label&#125;</a></li>
    <li><a href="#var_form_name">&#123;form_name&#125;</a></li>
    <li><a href="#var_form_type">&#123;form_type&#125;</a></li>
    <!-- <li><a href="#var_formpref">&#123;formpref:*&#125;</a></li> -->
    <li><a href="#var_fields_count">&#123;fields_count&#125;</a></li>
    <li><a href="#var_on_first_step">&#123;on_first_step&#125;</a></li>
    <li><a href="#var_on_last_step">&#123;on_last_step&#125;</a></li>
    <li><a href="#var_step_count">&#123;step_count&#125;</a></li>
    <li><a href="#var_multistep">&#123;multistep&#125;</a></li>
    <li><a href="#var_value">&#123;value:*&#125;</a></li>

</ul>

<h3><a name="var_captcha">&#123;captcha&#125;, &#123;use_captcha&#125;</a></h3>

<p>The &#123;captcha&#125; variable is set to an &lt;img /&gt; tag, which displays a CAPTCHA image automatically. The characters in this image are checked against an input element you should create, also named <strong>captcha</strong>.</p>

<p>The &#123;use_captcha&#125; variable is set to true or false, depending on if a CAPTCHA is needed for the current user. Logged in members are never required to submit a CAPTCHA. If a visitor is not currently logged in, they will be required to enter a CAPTCHA.</p>

<p>Note that CAPTCHA validation is only activated if you actually reference the &#123;captcha&#125; variable in your template - if you do not, the CAPTCHA will not be checked for - allowing all visitors to submit forms.</p>

<div class="tip">
    <h6>Conditionals</h6>
    <p>The conditional &#123;if captcha&#125; will <strong>NOT</strong> work. You must instead use the &#123;if use_captcha&#125; conditional to check to see if a CAPTCHA is required.</p>
</div>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us"&#125;
            &#123;if use_captcha&#125;
                &lt;p&gt;Enter this word: &#123;captcha&#125;&lt;/p&gt;
                &lt;p&gt;&lt;input type="text" name="captcha" /&gt;&lt;/p&gt;
                &#123;if error:captcha&#125;
                    <p>Please try again with the new word!</p>
                &#123;/if&#125;
            &#123;/if&#125;
        &#123;/exp:proform:form&#125;
    </pre>
</div>

<h3><a name="var_checked">{checked:*}</a></h3>

<p>Provides a string which can used to recheck a checkbox when the form is reloaded. These variables are set when the form contains an error and needs to be redisplayed, <strong>or</strong> when a form step is reloaded (a step can be visited multiple times by the user).</p>

<p>The value will either be the string <strong>checked="checked"</strong>, or a blank string. Thus, it can be injected directly into a field in order to allow it to be rechecked, or it can be used in a conditional (since anything but a blank string and zero is considered "true").</p>

<p>Replace the * in the variable name with the name of a field.</p>

<p>The &#123;field_checked&#125; variable inside of a &#123;fields&#125; variable pair provides the same value for fields, and is easier to use when generating generic form markup.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <p>Assuming there is one field defined on the form, named <strong>agree_to_terms</strong>.</p>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us" variable_prefix="pf_"&#125;
            &lt;p&gt;Old Value for field: &#123;checked:agree_to_terms&#125;&lt;/p&gt;
            &#123;fields&#125;
                &lt;p&gt;&lt;input type="checkbox" name="&#123;agree_to_terms&#125;" value="y" {field_checked} /&gt;&lt;/p&gt;
            &#123;/fields&#125;
        &#123;/exp:proform:form&#125;

    </pre>
</div>

<h3><a name="var_complete">{complete}</a></h3>

<p>If the form has been successfully processed and the thank_you_url was set to the same URL as the form itself (the default), this value will be set to be true so that you may display a thank you message instead of or in addition to the blank form.</p>

<h3><a name="var_error_count">{error_count}</a></h3>

<p>Provides the total number of fields that contain errors on the form. If the form has just been loaded for the first time and there are no errors, this will be set to 0. A conditional on this variable is the easiest way to determine if the template is loading an initial form view or loading a failed attempt to submit the form.</p>

<h3><a name="var_error">{error:*}</a></h3>

<p>Provides any errors that might be set for a particular field. Replace the * in the variable name with the name of a field.</p>

<p>This is most useful with the <a href="#var_captcha">&#123;captcha&#125;</a> variable in order to determine if a CAPTCHA error has occured. See that variable's Example Usage for an example.</p>

<h3><a name="var_form_id">{form_id}</a></h3>

<p>Provides the unique numeric ID of the form.</p>

<h3><a name="var_form_label">{form_label}</a></h3>

<p>Provides the friendly form label for the form. This can be any human readable string.</p>

<h3><a name="var_form_name">{form_name}</a></h3>

<p>Provides the internal form name for the form. This should be all lowercase and only contained letters, numbers, and underscores.</p>

<h3><a name="var_form_type">{form_type}</a></h3>

<p>Provides the type of the form being rendered. This is either set "form" for <dfn>Basic Forms</dfn> or "share" for <dfn>Share Forms</dfn>. For more about the types of forms available and their behavior, see the <a href="{root_url}cp/forms.html#create_form">Creating a New Form</a> section.</p>

<!-- <h3><a name="var_formpref">{formpref:*}</a></h3>

<p>Provides the value set for a form preference.</p> -->

<h3><a name="var_fields_count">{fields_count}</a></h3>

<p>Provides the total number of fields assigned to the form.</p>

<h3><a name="var_on_first_step">{on_first_step}</a></h3>

<p>Provides a boolean value indicating if the form is currently on it's <strong>first</strong> step.</p>

<h3><a name="var_on_last_step">{on_last_step}</a></h3>

<p>Provides a boolean value indicating if the form is currently on it's <strong>last</strong> step.</p>

<h3><a name="var_step_count">{step_count}</a></h3>

<p>Provides a count of the total number of steps in the form. All forms always have at least one step - this value can never be less than "1".</p>

<h3><a name="var_multistep">{multistep}</a></h3>

<p>Provides a boolean value indicating if the form has multiple steps or not.</p>

<h3><a name="var_value">{value:*}</a></h3>

<p>Provides the value entered for a field on the form. These variables are set when the form contains an error and needs to be redisplayed, <strong>or</strong> when a form step is reloaded (a step can be visited multiple times by the user).</p>

<p>Replace the * in the variable name with the name of a field.</p>

<p>The &#123;field_value&#125; variable inside of a &#123;fields&#125; variable pair provides the same value for fields, and is easier to use when generating generic form markup.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <p>Assuming there is one field defined on the form, named <strong>first_name</strong>.</p>
    <pre class="brush: xml">
        &#123;exp:proform:form form="contact_us" variable_prefix="pf_"&#125;
            &lt;p&gt;Old Value for field: &#123;value:first_name&#125;&lt;/p&gt;
            &#123;fields&#125;
                &lt;p&gt;&lt;input type="text" name="&#123;field_name&#125;" value="&#123;field_value&#125;" /&gt;&lt;/p&gt;
            &#123;/fields&#125;
        &#123;/exp:proform:form&#125;

    </pre>
</div>


<!-- ********************************************************************** -->
<h2><a name="variable_pairs">Variable Pairs</a></h2>

<p>The following variable pair are available within the <kbd>Form Tag</kbd>:</p>

<ul>
    <li><a href="#var_errors">&#123;errors&#125;</a></li>
    <li><a href="#var_fields">&#123;fields&#125;</a></li>
    <li><a href="#var_fieldrows">&#123;fieldrows&#125;</a></li>
    <li><a href="#var_hidden_fields">&#123;hidden_fields&#125;</a></li>
    <li><a href="#var_steps">&#123;steps&#125;</a></li>
</ul>

<h2><a name="var_errors">&#123;errors&#125;</a></h2>


<p>The <b>errors</b> variable pair provides a list of any errors that were detected when submitting the form.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <p>This example also demonstrates the &#123;error_count&#125; variable being used in a conditional to prevent rendering a wrapper for the errors when there are none.</p>
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
</div>

<h2><a name="var_fields">{fields}</a></h2>

<p>The <b>fields</b> variable pair provides a list of all of the fields in the selected form, regardless of the row they are set on.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <p>See the full <a href="{root_url}tags/form/template.html">Sample Template</a> for the <kbd>Form Tag</kbd> to see how to use the {fields} variable pair.</p>
</div>


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
<p><strong>Only valid for 'select' type fields</strong>. The {field_setting_list} loop, which must be used inside of the {fields} pair, provides a list of all of the options available for fields with the type <b>list</b>.</p>

<p>This variable pair has two variables available for each option:</p>

<ul>
  <li><strong>{key}</strong> - the value as stored in the database</li>
  <li><strong>{row}</strong> - the label of the value shown to the user</li>
</ul>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;fields&#125;
        &#123;if field_control == 'select'&#125;
          &lt;select id="{field_name}" name="{field_name}"&gt;
            &#123;field_setting_list&#125;
            &lt;option value="{key}"&gt;&#123;row&#125;&lt;/option&gt;
            &#123;/field_setting_list&#125;
          &lt;/select&gt;
        &#123;/if&#125;
        &#123;/fields&#125;
    </pre>
</div>

<h2><a name="var_fieldrows">{fieldrows}</a></h2>
<p>The <b>fieldrows</b> variable pair provides a list of the selected form&#39;s field rows. Each row contains a nested {fields} list that provides a list of only the fields on each individual row. See the <a href="#var_fields">{fields}</a> documentation for more information on the data available inside the nested fields pair.</p>

<p>To preserve the view of the form as presented in the layout editor, this loop should create for each row a new strip of fields - typically in the form of a &lt;ul&gt; element with it&#39;s &lt;li&gt; elements floated to the left. See the <a href="{root_url}tags/form/template.html">Sample Template</a> for one way this can be accomplished.</p>

<h2><a name="var_hidden_fields">{hidden_fields}</a></h2>

<p>The <b>hidden_fields</b> variable pair provides a list of all hidden fields within the form. It otherwise functions almost identically to the normal {fields} loop. See the list of variables inside the <a href="#var_fields">{fields}</a> loop above for more info on what is available inside this loop.</p>

<!-- ** steps ******************************************************************** -->

<h2><a name="var_steps">&#123;steps&#125;</a></h2>

<p>The <b>step</b> variable pair provides a list all of the steps configured for the form. There is always at least one step - which will by default have the same name as the form itself. Additional steps can be added easily in the <a href="{root_url}cp/form.html#multistep">Form Layout</a>.</p>

<div class="tip">
    <h6>Example Usage</h6>
    <pre class="brush: xml">
        &#123;if multistep&#125;
            &lt;ul class="pf_steps"&gt;
                &#123;pf_steps&#125;
                    &lt;li>&lt;a href="#&#123;pf_step_no&#125;" class="pf_step &#123;pf_step_active&#125;">&#123;pf_step&#125;&lt;/a>&lt;/li&gt;
                &#123;/pf_steps&#125;
            &lt;/ul&gt;
        &#123;/if&#125;

    </pre>
</div>


<h4>&#123;steps&#125; Variables</h4>
<p>The following variables are available within each row of the &#123;steps&#125; variable pair.</p>

<ul>
    <li><a href="#var_step">&#123;step&#125;</a></li>
    <li><a href="#var_step_active">&#123;step_active&#125;</a></li>
    <li><a href="#var_step_no">&#123;step_no&#125;</a></li>
</ul>


<h5>&#123;steps&#125; Single Variables</h5>

<h6><a name="var_step">{step}</a></h6>

<p>Provides the text for the step's name as defined in the layout for the form.</p>

<h6><a name="var_step_active">{step_active}</a></h6>

<p>Provides a string value that indicates if this step is active. If the step is active, this variable returns the word <strong>active</strong>, if not it returns a blank string. This may be used in a conditional (since anything but a blank string and zero is considered "true"), as well as directly in a class="" parameter for an HTML element, as in the example above.</p>

<h6><a name="var_step_no">{step_no}</a></h6>

<p>Provides the number of the step. Steps use 1-based numbering: the first step is number 1, the second is number 2, etc.</p>

<!-- ** sample template ******************************************************************** -->
<h2><a name="sample_template_code">Sample Template Code</a></h2>

<p>See the full <a href="{root_url}tags/form/template.html">Sample Template</a> for the <kbd>Form Tag</kbd>.</p>


{ce:core:include template="global/_footer"}