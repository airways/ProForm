{ce:core:include template="global/_header" title="Module Settings"}

<h1>Hidden Settings</h1>

<p>The following options must be set within your ExpressionEngine config file, usually located at <dfn>system/expressionengine/config/config.php</dfn>.</p>

<p class="critical"><strong>Warning</strong> These options are considered experimental and should be used with caution. Always make sure to keep frequent backups and verify the behavior of any hidden settings you are using (for instance, if you use the options to store form data in an encrypted format - ensure you are able to decrypt data and move it to other systems >


<h4>Hidden Settings</h4>
<table>
    <tbody>
        <tr>
            <th width="170">config.php entry</th>
            <th>Values</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><b>proform_allow_encryption</b></td>
            <td>yes/no (default: no)</td>
            <td>Allows use of the Encrypt Data option for forms. See the <a href="{root_url}cp/forms/advanced-settings">Form's Advanced Settings</a> page for more information.</td>
        </tr>
        <tr>
            <td><b>proform_allow_table_override</b></td>
            <td>yes/no (default: no)</td>
            <td>Allows use of the Table OVerride option for forms. See the <a href="{root_url}cp/forms/advanced-settings">Form's Advanced Settings</a> page for more information.</td>
        </tr>
    </tbody>
</table>

{ce:core:include template="global/_footer"}