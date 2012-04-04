{ce:core:include template="global/_header" title="Form Management"}

<h1>Form Management</h1>

<p>The main page of <dfn>ProForm</dfn> provides a list of all of the forms that have been created within the module.</p>

<h3>Contents</h3>
<ul>
    <li><a href="#create_form">Creating a New Form</a></li>
    <li><a href="#form_settings">Form Settings</a>
        <ul>
            <li><a href="#form_settings_basic">Basic Settings</a></li>
            <li><a href="#form_settings_notification_list">Notification List Settings</a></li>
            <li><a href="#form_settings_notification_fields">Notification Field Settings</a></li>
        </ul>
    </li>
    <li><a href="#layout">Form Layout</a>
        <ul>
            <li><a href="#layout_editing">Layout Editing</a></li>
            <li><a href="#layout_toolbox">The Toolbox</a></li>
            <li><a href="#layout_adding_fields">Adding Fields</a></li>
            <li><a href="#layout_field_overrides">Overriding Field Settings</a></li>
            <li><a href="#layout_adding_headings">Adding Headings</a></li>
        </ul>
    </li>
    <li><a href="#multistep">Multistep Forms</a></li>
    <li><a href="#entries">Viewing and Exporting Entries</a></li>
    <li><a href="#delete_form">Deleting a Form</a></li>
</ul>

<h2><a name="create_form">Creating a New Form</a></h2>
<p>To create a new form, use the <kbd>Create a Form</kbd> drop down menu to select one of the available types.</p>

<ul>
    <li>Basic Form</li>
    <li>Share Form</li>
</ul>

<p>When you create either type of form you will be asked to specify various settings settings as listed in the <a href="#form_settings">Form Settings</a> section.</p>

<h3>Basic Form</h3>
<p>A Basic Form is used to create typical contact forms and other forms that save their data into the database. This data can be encrypted, and notification of new entries can be sent to various locations.</p>

<h3>Share Form</h3>
<p>A Share Form is meant to be used as a replacement to the Tell a Friend form. It does not save data to the database, but send the same types of notifications that can be sent for Basic Forms.</p>

<h2><a name="form_settings">Form Settings</a></h2>
<p>After creating a new form, the same settings that are set when it is initially created can be updated at any time by clicking the <kbd>Edit Settings</kbd> link on the form listing.</p>

<p>The following settings are available for the various types of forms.</p>

<h4><a name="form_settings_basic">Basic Settings</a></h4>
<table>
    <tbody>
        <tr>
            <th width="170">
                Field</th>
            <th width="90">
                Form Types</th>
            <th>
                Description</th>
        </tr>
        <tr>
            <td><b>Full Form Name</b></td>
            <td>Basic, Share</td>
            <td>Human friendly form name, used in UI and likely displayed on your website.</td>
        </tr>
        <tr>
            <td><b>Form Short Name</b></td>
            <td>Basic, Share</td>
            <td>Similar in concept to a "channel" name - used in templates to indicate what form&#39;s information is to be retrieved in order to render the form, or to list it&#39;s entries.</td>
        </tr>
        <tr>
            <td><b>Encrypt Data</b></td>
            <td>Basic Only</td>
            <td>Turns database encryption on or off for the current form. Note that this option cannot be changed after data has been entered into the form&#39;s entries.<br />
                <br />
                Encryption comes in two flavors - a simple and insecure XOR technique and a more secure method based on the Mcrypt extension, which must be installed. The default is the insecure XOR unless you have Mcrypt installed and have set a secure encryption key in your <dfn>config.php</dfn> file. Please refer to the CodeIgniter reference&#39;s warnings about their encryption class for more details and instructions for how to set your encryption key:<br />
                <br />
                <a href="http://codeigniter.com/user_guide/libraries/encryption.html">http://codeigniter.com/user_guide/libraries/encryption.html</a><br />
                <br />
                (The config file is usually located at <dfn>system/expressionengine/config/config.php</dfn> instead of the location noted on that page.)</td>
        </tr>
    </tbody>
</table>
<h4>
    <a name="form_settings_notification_list">Notification List Settings</a></h4>
<p>
    The Notification List is a preconfigured list of email addresses to send notifications to. It has the following settings for each form.</p>
<table>
    <tbody>
        <tr>
            <th width="170">
                Field</th>
            <th width="90">
                Form Types</th>
            <th>
                Description</th>
        </tr>
        <tr>
            <td><b>Enable Notification List</b></td>
            <td>Basic, Share</td>
            <td>Turns the Notification List on or off. Notifications to the listed addresses will not be sent if this option is not checked.</td>
        </tr>
        <tr>
            <td><b>Notification Template</b></td>
            <td>Basic, Share</td>
            <td>ExpressionEngine Template used to render the emails sent to the Notification List. The available templates are listed from the Notification Template Group selected in <a href="{site_url}documentation/proform/cp/settings">Module Settings</a>. For more information about what tags are available in a notification template, see <a href="{site_url}documentation/proform/notifications">Notification Templates</a>.</td>
        </tr>
        <tr>
            <td><b>Notification List</b></td>
            <td>Basic, Share</td>
            <td>List of email addresses to send this notification to. This constitutes the actual Notification List itself. List each email address to send the notification to on it&#39;s own line.</td>
        </tr>
        <tr>
            <td><b>Subject</b></td>
            <td>Basic, Share</td>
            <td>The subject line of the notification email. This line is rendered as an ExpressionEngine template, and therefore can use conditionals and plugins in addition to the variables available in <a href="{site_url}documentation/proform/notifications">Notification Templates</a>.</td>
        </tr>
    </tbody>
</table>
<h4>
    <a name="form_settings_notification_fields">Notification Field Settings - A and B</a></h4>
<p>
    There are two groups of Notification Field Settings available for each form. Using a Notification Field allows the destination for your notifications to be entered by the visitor when they fill out the form.</p>
<p class="important">
    <strong>Warning</strong> Keep in mind that this feature provides what is essentially an open relay, which can allow anyone to send a message to any email address. It&#39;s very important that all forms that send notifications to addresses specified through a Notification Field have a CAPTCHA or other mechanisms in place to prevent the sending of spam.</p>
<table>
    <tbody>
        <tr>
            <th width="170">
                Field</th>
            <th width="90">
                Form Types</th>
            <th>
                Description</th>
        </tr>
        <tr>
            <td><b>Enable Group</b></td>
            <td>Basic, Share</td>
            <td>Turns the Notification Field on or off. Notifications to the addresses provided in the Email Field will not be sent if this option is not checked.</td>
        </tr>
        <tr>
            <td><b>Template</b></td>
            <td>Basic, Share</td>
            <td>ExpressionEngine Template used to render the emails sent to the specified email address. The available templates are listed from the Notification Template Group selected in <a href="{site_url}documentation/proform/cp/settings">Module Settings</a>. For more information about what tags are available in a notification template, see <a href="{site_url}documentation/proform/notifications">Notification Templates</a>.</td>
        </tr>
        <tr>
            <td><b>Subject</b></td>
            <td>Basic, Share</td>
            <td>The subject line of the notification email. This line is rendered as an ExpressionEngine template, and therefore can use conditionals and plugins in addition to the variables available in <a href="{site_url}documentation/proform/notifications">Notification Templates</a>.</td>
        </tr>
        <tr>
            <td><b>Email Field</b></td>
            <td>Basic, Share</td>
            <td>The email address to send notifications to will be taken from this field in the form.</td>
        </tr>
    </tbody>
</table>

<h2><a name="layout">Form Layout</a></h2>
<p>Forms have a user-defined layout that is created through the Form Layout tab of the form&#39;s settings.</p>

<p>You can get to this tab either by clicking it&#39;s name and then the Form Layout tab, or by clicking Form Layout directly from the from listing.</p>

<h3><a name="layout_editing">Layout Editing</a></h3>
<p>The layout for a form is manipulated as a series of rows of fields. Each field can be moved between rows by clicking and dragging the field.</p>

<h3><a name="layout_toolbox">The Toolbox</a></h3>
<p>All items which can be added to a form are accessible through the <kbd>Toolbox</kbd>, which lives under the <kbd>Add an Item</kbd> tab along the right side of the form edit screen's <kbd>Layout</kbd> page.</p>

<p>There are three types of items which can be added to the form directly from the <kbd>Toolbox</kbd>:</p>
<ul>
    <li><a href="#layout_adding_fields">New Fields</a> - the first section contains a list of Field Types which can be created directly in the form</li>
    <li>Special Items - this type contains two subtypes:
        <ul>
            <li><a href="#layout_adding_headings">Headings</a> - useful to break up the flow of a form into logical chunks</li>
            <li><a href="#multistep">Steps</a> - used to create a multistep form with separately validated steps</li>
        </ul>
    </li>
    <li><a href="#layout_library_fields">Library Fields</a> - these fields have been predefined and possibly are in use in multiple forms</li>
</ul>


<h3><a name="layout_adding_fields">Adding a Field</a></h3>

<p>To add a new field to the form, click the type of the field you wish to create in the <kbd>Toolbox</kbd> under the <kbd>Add an Item</kbd> tab on the right side of the layout screen.</p>

<p>This will bring you directly to the <a href="{root_url}cp/fields.html#create_field">New Field</a> page, and return you to the form's layout after adding the field to the form.</p>

<h3><a name="layoutfield_overrides">Field Overrides</a></h4>

<p>Certain values can be overridden for each field on the form. To set a field&#39;s overridden values, click on the field to select it. The Edit Field box on the right will then display the field&#39;s current override values.</p>

<h4>Field Override Values</h4>
<table>
    <tbody>
        <tr>
            <th width="170">Name</th>
            <th>Template Var</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>Field Label</td>
            <td>{field_label}</td>
            <td>Overrides the value returned for {field_label} for this field. Normally this value is taken from the field&#39;s own settings. This allows the same field to have multiple labels depending on which form it is used on.</td>
        </tr>
        <tr>
            <td>Field Default Value</td>
            <td>{field_preset_value}</td>
            <td>Allows entering the default value for this field.</td>
        </tr>
        <tr>
            <td>Force Default Value</td>
            <td>{field_preset_forced}</td>
            <td>Removes the field from the list of fields returned by the {fields} variable pair, and forces the value to always be set to the Field Default Value. This prevents the visitor from changing the value of this field, even if they modify the POST form to change the data submitted.</td>
        </tr>
        <tr>
            <td>Field Id</td>
            <td>{field_html_id}</td>
            <td>A value to be used as the field&#39;s HTML id in the template.</td>
        </tr>
        <tr>
            <td>Field Class</td>
            <td>{field_html_class}</td>
            <td>A value to be used as the field&#39;s HTML class in the template.</td>
        </tr>
        <tr>
            <td>Extra 1, Extra 2</td>
            <td>{field_extra1}, {field_extra2}</td>
            <td>Extra meta values to be used by the template. These values can be used for any purpose desired, such as additional HTML classes or a custom field description for the form.</td>
        </tr>
    </tbody>
</table>

<h3><a name="layout_library_fields">Library Fields</a></h3>
<p>Library Fields are created by marking a field as <kbd>Reusable</kbd> in its <a href="{root_url}cp/fields.html#field_settings">Field Settings</a>.</p>

<p>After a field is marked as <kbd>Reusable</kbd>, it is placed into the <kbd>Library</kbd> section of the <kbd>Toolbox</kbd>. This allows you to easily and quickly add the field to other forms in the system simply by clicking it's name.</p>

<p>To edit a field from the library, click it's <kbd>Edit...</kbd> button. This allows you to modify the field's settings, applying all of those changes to each form that uses the field from a single place.</p>

<p>Note that only fields that are not already assigned to the form will be listed in the <kbd>Library</kbd>.</p>

<h3><a name="layout_adding_headings">Adding a Heading</a></h3>
<p>Adding a heading to a form layout is very similar to adding a field. To add a new heading to a form, first click the form's <b>Edit Layout</b> link from the form listing (or click the form's <b>Layout</b> tab if you already have the form open). Then, scroll down in the <kbd>Toolbox</kbd> to the <kbd>Special</kbd> section and click the <b>Heading</b> button.</p>

<p>Each heading contains a single configuration value:</p>

<table>
    <tbody>
        <tr>
            <th width="170">Name</th>
            <th>Template Var</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>Heading Label</td>
            <td>{heading}</td>
            <td>The value to use as the heading's text.</td>
        </tr>
    </tbody>
</table>
    
<h2><a name="entries">Viewing and Exporting Entries</a></h2>
<p>Entries for a form can be viewed by clicking the View Entries link of the form listing. The entries list for a form provides all of it&#39;s entries in a paginated list.</p>

<p>On the entries list for a form, you may export the entries from the form through the Export Entries button. The file will be downloaded in a CSV formatted text file.</p>

<h2><a name="delete_form">Deleting a Form</a></h2>
<p>Deleting a form removes all of it&#39;s settings and deletes all of it&#39;s data from the database.</p>

<p>You can delete a form and all of it&#39;s data through the Delete command on the form listing. You must confirm the deletion in the following page.</p>

<p class="important"><strong>Warning</strong> Deleting a form cannot be undone. Old forms can be kept in the database indefinitely without any performance penalties.</p>

{ce:core:include template="global/_footer"}
