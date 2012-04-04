{ce:core:include template="global/_header"}

<h1>ProForm Module</h1>

<p><dfn>ProForm</dfn> is an advanced drag and drop form management module for ExpressionEngine 2.0, designed to make creation and management of forms easier for developers and end users.</p>

<h2>Features</h2>
<p><dfn>ProForm</dfn> has the following features:</p>

<ul>
    <li>Forms fully configured in the Control Panel</li>
    <li>Drag &amp; drop form layout in Control Panel</li>
    <li>Full ExpressionEngine template support for rendering multiple forms from a <strong>single</strong> template (or multiple templates if you prefer)</li>
    <li>Mailing list opt-in</li>
    <li>CAPTCHA support to help prevent spam</li>
    <li>File uploads</li>
    <li>Send notifications, also rendered using EE templates, to admins and/or any email address entered in the form</li>
    <li>CodeIgniter based validation including content filtering and encoding options (required, valid e-mail, strip HTML, base64 encode, etc.)</li>
    <li>Plentiful <a href="{root_url}extending/hooks.html">hooks</a>, allowing third party customization</li>
    <li>Separate database table for each form - no more field count limits, easy to work with in custom code</li>
    <li>IP address and user agent recording</li>
    <li>Optional database Encryption</li>
    <li>Preset values for use in share forms such as <dfn>Tell a Friend</dfn> to prevent email spam</li>
    <li>Cleanly organized OOP model</li>
    <li>Reusable <dfn>ProLib</dfn> library extending core EE and CI capabilities</li>
</ul>

<h2>ProForm Documentation</h2>
<h3>Control Panel</h3>
<ul>
    <li><a href="{root_url}cp.html">Control Panel</a>
        <ul>
            <li><a href="{root_url}cp/forms.html">Managing Forms</a></li>
            <li><a href="{root_url}cp/fields.html">Managing Fields</a></li>
            <li><a href="{root_url}cp/settings.html">Module Settings</a></li>
        </ul>
    </li>
</ul>

<h3>Templates</h3>
<ul>
    <li><a href="{root_url}tags.html">Tags</a>
        <ul>
            <li><a href="{root_url}tags/form.html">Form Tag</a>
                <ul>
                    <li><a href="{root_url}tags/form/template.html">Sample Template</a></li>
                </ul>
            </li>
            <li><a href="{root_url}tags/entries.html">Entries Tag</a></li>
            <li><a href="{root_url}tags/insert.html">Insert Tag</a> <i>(docs coming soon)</i></li>
        </ul>
    </li>
</ul>

<ul>
    <li><a href="{root_url}extending.html">Extending ProForm</a> <i>(docs coming soon)</i>
        <ul>
            <li><a href="{root_url}extending/hooks.html">Hooks</a> <i>(docs coming soon)</i></li>
            <li><a href="{root_url}extending/sample.html">Sample Extension</a> <i>(docs coming soon)</i></li>
        </ul>
    </li>
    <li><a href="{root_url}notifications.html">Notification Templates</a></li>
</ul>

{ce:core:include template="global/_footer"}
