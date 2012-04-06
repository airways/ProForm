{ce:core:include template="global/_header" title="Module Settings"}

<h1>
    Module Settings (BETA)</h1>
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

{ce:core:include template="global/_footer"}