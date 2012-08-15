{ce:core:include template="global/_header" title="Module Settings"}

<h1>Module Settings</h1>
<p>
    The following options are available to globally configure <dfn>ProForm</dfn>&#39;s functionality.</p>
<h4>
    Module Settings</h4>
<table>
    <tbody>
        <tr>
            <th width="170">Field</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><b>Notification Template Group</b></td>
            <td>Selects the template group which contains notification templates for your forms. This should be a template group dedicated only to email notification templates. Until a template group is created and selected here, notifications cannot be sent from form submissions.</td>
        </tr>
        <tr>
            <td><b>From Address</b></td>
            <td>Optional address to use in the notification email's From header. *</td>
        </tr>
        <tr>
            <td><b>From Name</b></td>
            <td>Optional name to use in the notification email's From header. *</td>
        </tr>
        <tr>
            <td><b>Reply-To Address</b></td>
            <td>Optional email address to use in the Reply-To header for all notifications sent by <dfn>ProForm</dfn>. Each form may also override this value in it's <a href="{root_url}cp/forms.html#form_settings_basic">Form Settings</a>.</td>
        </tr>
        <tr>
            <td><b>Reply-To Name</b></td>
            <td>Optional name to use in the Reply-To header for all notifications sent by <dfn>ProForm</dfn>. Each form may also override this value in it's <a href="{root_url}cp/forms.html#form_settings_basic">Form Settings</a>.</td>
        </tr>

    </tbody>
</table>

<p><b>*</b> The From Address typically should match your ExpressionEngine email settings in order to avoid having your notifications marked as spam. If not provided, the value will default to that set in ExpressionEngine's global settings - so setting this option isn't recommended. Instead, use the Reply-To settings.</p>

<h4><a name="form_settings_advanced">Advanced Settings</a></h4>

<p>The Advanced Settings tab allows you to configure some additional values and extend the functionality of the module. Be sure to check the documentation for any additional Drivers to see if they provide additional Advanced Settings.</p>

<p>Some settings may be specific to the template you are using as well, meaning that they only have an effect if the template actually calls them.</p>

<p>All of these settings are available inside a {exp:proform:form} and {exp:proform:results} tags, as shown in the Variable column of this table. Some additional settings are available at the form level through the form's own <a href="{root_url}cp/forms.html#form_settings_advanced">Advanced Settings</a> tab.</p>

<table>
    <tbody>
        <tr>
            <th width="20%">Name</th>
            <th width="20%">Variable</th>
            <th>Description</th>
            <th width="20%">Example Value</th>
        </tr>
        <tr>
            <td><strong>Invalid Form Message</strong></td>
            <td><strong>{invalid_form_message}</strong></td>
            <td>A message to display when the requested form isn't found.</td>
            <td>The requested form doesn't exist. Please check the Form Directory for a list of available forms.</td>
        </tr>
        <tr>
            <td><strong>Thank You Message (Default)</strong></td>
            <td><strong>{thank_you_message}</strong></td>
            <td>Provides a custom message to be used in the default template and by the {exp:proform:simple} tag when the form has been submitted. If a form provides it's own <strong>Thank You Message</strong> through it's Advanced Settings tab, that message will be used instead.</td>
            <td>Thank you for submitting a form from the Form Directory. Your submission will be processed as soon as possible.</td>
        </tr>

    </tbody>
</table>

<p>Additionally there are some <a href="{root_url}cp/hidden-settings.html">Hidden Settings</a> available.</p>

{ce:core:include template="global/_footer"}