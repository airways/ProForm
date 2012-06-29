{ce:core:include template="global/_header" index="yes"}

<h1>ProForm Module</h1>

<p><dfn>ProForm</dfn> is an advanced drag and drop form management module for ExpressionEngine 2.0, designed to make creation and management of forms easier for developers and end users.</p>

<h2>Features</h2>

<p><dfn>ProForm</dfn> has the following features:</p>

<ul>
    <li>Forms fully configured in the Control Panel</li>
    <li>Drag &amp; drop form layout in Control Panel</li>
    <li>Simple one line tag to render any form on your site</li>
    <li>Optional full ExpressionEngine template support for rendering multiple forms from a <strong>single</strong> custom template</li>
    <li>Multistep form support</li>
    <li>AJAX posting</li>
    <li>Mailing list opt-in</li>
    <li>CAPTCHA support to help prevent spam</li>
    <li>File uploads</li>
    <li>Send notifications, rendered using EE templates, to admins and/or any email address entered in the form</li>
    <li>CodeIgniter based validation including content filtering and encoding options (required, valid e-mail, strip HTML, base64 encode, etc.)</li>
    <li>Plentiful <a href="{root_url}extending.html">hooks</a>, allowing third party customization</li>
    <li>Separate database table for each form - no more field count limits, easy to work with in custom code</li>
    <li>IP address and user agent recording</li>
    <li>Optional database Encryption</li>
    <li>Preset values for use in share forms such as <dfn>Tell a Friend</dfn> to prevent email spam</li>
</ul>

<h2>ProForm Documentation</h2>

<h3>Control Panel</h3>

<ul>
    <li><a href="{root_url}installation.html">Installation</a></li>
        <li><a href="{root_url}cp.html">Control Panel</a>
        <ul>
            <li><a href="{root_url}cp/forms.html">Managing Forms</a></li>
            <li><a href="{root_url}cp/fields.html">Managing Fields</a></li>
            <li><a href="{root_url}cp/settings.html">Module Settings</a></li>
        </ul>
    </li>
</ul>

<h3>Tags</h3>
<ul>
    <li><a href="{root_url}tags/form.html">Form Tags</a></li>
        <ul>
            <li><a href="{root_url}tags/form.html#simple">Simple Form Tag</a></li>
            <li><a href="{root_url}tags/form.html#full">Full Form Tag</a></li>
        </ul>
    </li>
    <li><a href="{root_url}tags/results.html">Results Tag</a></li>
    <li><a href="{root_url}tags/entries.html">Entries Tag</a></li>
</ul>

<h3>Sample template</h3>
<ul>
    <li><a href="{root_url}tags/form/template.html">Sample Full Form Template</a></li>
    <li><a href="{root_url}tags/form/ajax-template.html">AJAX Sample Template</a></li>
    <li><a href="{root_url}notifications.html">Sample Notification Templates</a></li>
</ul>

<h3>Extending ProForm</h3>
<ul>
    <li><a href="{root_url}extending.html">Extension Hooks</a></li>
</ul>


{ce:core:include template="global/_footer"}
