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
            <li><a href="#form_settings_advanced">Advanced Settings</a></li>
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
<p>To create a new form, use the <kbd>Create a Form</kbd> drop down menu to select one of the available types:</p>

<ul>
    <li>Basic Form</li>
    <li>Share Form</li>
</ul>
<br/>
<p>When you create either type of form you will be asked to specify various settings settings as listed in the <a href="#form_settings">Form Settings</a> section.</p>

<h3>Basic Form</h3>
<p>A Basic Form is used to create typical contact forms and other forms that save their data into the database. This data can be encrypted, and notification of new entries can be sent to various locations.</p>

<h3>Share Form</h3>
<p>A Share Form is meant to be used as a replacement to the Tell a Friend form. It does not save data to the database, but send the same types of notifications that can be sent for Basic Forms.</p>

<h2><a name="form_settings">Form Settings</a></h2>
<p>After creating a new form, the same settings that are set when it is initially created can be updated at any time by clicking the <kbd>Edit Settings</kbd> link on the form listing.</p>

<p>The following settings are available for the various types of forms.</p>

<p>These basic settings are available to all types of forms.</p>

<h4><a name="form_settings_basic">Basic Settings</a></h4>
<table>
    <tbody>
        <tr>
            <th width="20%">Option</th>
            <th>Description</th>
            <th width="20%">Example Value</th>
        </tr>
        <tr>
            <td><strong>Full Form Name</strong></td>
            <td>Human friendly form name, used in UI and likely displayed on your website.</td>
            <td>Contact Us</td>
        </tr>
        <tr>
            <td><strong>Form Short Name</strong></td>
            <td>Similar in concept to a "channel" name - used in templates to indicate what form&#39;s information is to be retrieved in order to render the form, or to list it&#39;s entries.</td>
            <td>contact_us</td>
        </tr>
        <tr>
            <td><strong>Reply-To Address</strong></td>
            <td>Optional email address to use in the Reply-To header for all notifications sent from this form. If not provided, the global <a href="{root_url}cp/settings.html">Module Setting</a> of the same name will be used instead.</td>
            <td>noreply@example.com</td>
        </tr>
        <tr>
            <td><strong>Reply-To Name</strong></td>
            <td>Optional name to use in the Reply-To header for all notifications sent from this form. If not provided, the global <a href="{root_url}cp/settings.html">Module Setting</a> of the same name will be used instead.</td>
            <td>Example Co. Notifications</td>
        </tr>
    </tbody>
</table>

<p>Additionally there are some <a href="{root_url}cp/forms/hidden-settings.html">Hidden Settings</a> available.</p>

<h4><a name="form_settings_notification_list">Notification List Settings</a></h4>

<p>The Notification List is a preconfigured list of email addresses to send notifications to.</p>

<p>When a new entry is submitted to the form, each of the addresses in the Notification List will be sent a separate email message, generated from the selected template. The Notification List is available to all types of forms.</p>

<p>The Notification List has the following settings for each form.</p>

<table>
    <tbody>
        <tr>
            <th width="20%">Field</th>
            <th>Description</th>
            <th width="20%">Example Value</th>
        </tr>
        <tr>
            <td><strong>Enable Notification List</strong></td>
            <td>Turns the Notification List on or off. Notifications to the listed addresses will not be sent if this option is not checked.</td>
            <td>n/a</td>
        </tr>
        <tr>
            <td><strong>Notification Template</strong></td>
            <td>ExpressionEngine Template used to render the emails sent to the Notification List. The available templates are listed from the Notification Template Group selected in <a href="{root_url}cp/settings.html">Module Settings</a>. For more information about what tags are available in a notification template, see <a href="{root_url}notifications.html">Notification Templates</a>.</td>
            <td>n/a</td>
        </tr>
        <tr>
            <td><strong>Notification List</strong></td>
            <td>List of email addresses to send this notification to. This constitutes the actual Notification List itself. List each email address to send the notification to on it&#39;s own line.</td>
            <td>admin@example.com<br/>
                support@example.com<br/>
                jdoe@example.com<br/>
            </td>
        </tr>
        <tr>
            <td><strong>Subject</strong></td>
            <td>The subject line of the notification email. This line is rendered as an ExpressionEngine template, and therefore can use conditionals and plugins in addition to the variables available in <a href="{root_url}notifications.html">Notification Templates</a>.</td>
            <td>New submission to form: {form_name}</td>
        </tr>
        <tr>
            <td><strong>Reply-To Field</strong></td>
            <td>This option can be set to the name of a field, the value of which will be used as the notification's email address in the Reply-To header. This means that any recipient of the notification will automatically be able to reply to the email address entered into this field in the form.<br/><br/>
                For instance, often times this option would be set to a field filled out by the visitor such as <strong>my_email_address</strong>. Whatever value is entered by the user would then be set as the Reply-To for the notifications. Assuming that the Notification List is sent to a customer service rep, or other person who would like to easily contact the submitter of the form, they can simply use their email client's <strong>Reply</strong> command to compose a new return message directly to the visitor.</td>
            <td>submitter_email_address</td>
        </tr>
        <tr>
            <td><strong>Send Attachments?</strong></td>
            <td>This option enables sending of attachments to this notification group. All uploaded files from file fields on the form will be sent to this notification group as attachments.</td>
            <td>n/a</td>
        </tr>
    </tbody>
</table>
<h4>
    <a name="form_settings_notification_fields">Notification Field Settings</a></h4>
<p>There are two groups of Notification Field Settings available for each form. Using a Notification Field allows the destination for your notifications to be entered by the visitor when they fill out the form.</p>

<p>These notification settings are useful in a situation where you want to provide a pre-set list of available contact addresses, and ask the user to pick one of these options. By specifying the field which contains these options as the Notification Field Setting's <strong>Email Field</strong> option, you will then get a notification sent only to that individual address.</p>

<p>You can also use a Notification Field setting group to send a notification a the visitor, thanking them for their submission.</p>

<p>This is different from the Notification List settings above, in that the entire list of addresses specified on the Notification List always receive the notification - while there is only ever <strong>one</strong> recipient of a notification sent from a Notification Field. Another difference is that the Notification List uses a static list of addresses defined on the form's settings, while Notification Fields take their options form a field instead.</p>

<p>Notification Field settings are available to all types of forms.</p>

<p class="important">
    <strong>Warning</strong> Keep in mind that this feature provides what is essentially an open relay, which can allow anyone to send a message to any email address. It&#39;s very important that all forms that send notifications to addresses specified through a Notification Field have a CAPTCHA or other mechanisms in place to prevent the sending of spam.</p>

<table>
    <tbody>
        <tr>
            <th width="20%">Field</th>
            <th>Description</th>
            <th width="20%">Example Value</th>
        </tr>
        <tr>
            <td><strong>Enable Group</strong></td>
            <td>Turns the Notification Field on or off. Notifications to the addresses provided in the Email Field will not be sent if this option is not checked.</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Template</strong></td>
            <td>ExpressionEngine Template used to render the emails sent to the specified email address. The available templates are listed from the Notification Template Group selected in <a href="{root_url}cp/settings.html">Module Settings</a>. For more information about what tags are available in a notification template, see <a href="{root_url}notifications.html">Notification Templates</a>.</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Subject</strong></td>
            <td>The subject line of the notification email. This line is rendered as an ExpressionEngine template, and therefore can use conditionals and plugins in addition to the variables available in <a href="{root_url}notifications.html">Notification Templates</a>.</td>
            <td>Thank you for submitting the {form_name} form</td>
        </tr>
        <tr>
            <td><strong>Email Field</strong></td>
            <td>The email address to send notifications to will be taken from this field in the form.</td>
            <td>select_department</td>
        </tr>
        <tr>
            <td><strong>Reply-To Field</strong></td>
            <td>This option can be set to the name of a field, the value of which will be used as the notification's email address in the Reply-To header. This means that any recipient of the notification will automatically be able to reply to the email address entered into this field in the form.<br/><br/>
                For instance, often times this option would be set to a field filled out by the visitor such as <strong>my_email_address</strong>. Whatever value is entered by the user would then be set as the Reply-To for the notifications. Assuming that the Notification Field is used to specify a particular customer service rep or department to send this notification to, that person can simply use their email client's <strong>Reply</strong> command to compose a new return message directly to the visitor.</td>
            <td>submitter_email_address</td>
        </tr>
        <tr>
            <td><strong>Send Attachments?</strong></td>
            <td>This option enables sending of attachments to this notification group. All uploaded files from file fields on the form will be sent to this notification group as attachments.</td>
            <td>n/a</td>
        </tr>
    </tbody>
</table>

<h4><a name="form_settings_advanced">Advanced Settings</a></h4>

<p>The Advanced Settings tab allows you to configure some additional values and extend the functionality of the form. These settings are dependent on which Form Drivers you have installed, so be sure to check the documentation for any additional Drivers to see if they provide additional Advanced Settings.</p>

<p>Some settings may be specific to the template you are using as well, meaning that they only have an effect if the template actually calls them.</p>

<p>All of these settings are available inside a {exp:proform:form} and {exp:proform:results} tags, as shown in the Variable column of this table. Some additional settings are available globally from the module's own <a href="{root_url}cp/settings.html#form_settings_advanced">Advanced Settings</a> tab.</p>

<table>
    <tbody>
        <tr>
            <th width="20%">Name</th>
            <th width="20%">Variable</th>
            <th>Description</th>
            <th width="20%">Example Value</th>
        </tr>
        <tr>
            <td><strong>Form HTML ID</strong></td>
            <td>{html_id}</td>
            <td>An HTML ID to insert into the generated form tag. If this value is not provided, an id based on the form's Short Name will be used instead.</td>
        </tr>
        <tr>
            <td><strong>Form HTML Class</strong></td>
            <td>{html_class}</td>
            <td>An HTML Class to insert into the generated form tag. If this value is not provided, the class "proform" will be used instead. Just as in HTML you can include multiple classes by separating them with spaces.<br/><br/>s
            Note: In order to keep ProForm's default styling you should include "proform" as one of the classes in this value. </td>
        </tr>
        <tr>
            <td><strong>Label for Extra 1</strong>, <strong>Label for Extra 2</strong></td>
            <td><strong>{extra1_label}</strong>, <strong>{extra2_label}</strong></td>
            <td>Changes the label visible under the Layout tab for the Extra 1 or 2 overrides. This can be useful to change the meaning of the Extra field in order to be more user friendly so the editors do not need to remember what you are using the Extra field for.</td>
            <td>Wrapper HTML Class</td>
        </tr>
        <tr>
            <td><strong>Thank You Message</strong></td>
            <td><strong>{thank_you_message}</strong></td>
            <td>Provides a custom message to be used in the default template and by the {exp:proform:simple} tag when the form has been submitted.</td>
            <td>Thank you for submitting a Support Request! We will get back to you shortly.</td>
        </tr>

    </tbody>
</table>

<p>Additionally there are some <a href="./../cp/forms/hidden-settings.html">Hidden Settings</a> available.</p>

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
    <li>Special Items - this type contains three subtypes:
        <ul>
            <li><a href="#layout_adding_headings">Headings</a> - useful to break up the flow of a form into logical chunks</li>
            <li><a href="#layout_adding_blocks">HTML Blocks</a> - used to insert any arbitrary HTML markup into the form</li>
            <li><a href="#multistep">Steps</a> - used to create a multistep form with separately validated steps</li>
        </ul>
    </li>
    <li><a href="#layout_library_fields">Library Fields</a> - these fields have been predefined and possibly are in use in multiple forms</li>
</ul>


<h3><a name="layout_adding_fields">Adding a Field</a></h3>

<p>To add a new field to the form, click the type of the field you wish to create in the <kbd>Toolbox</kbd> under the <kbd>Add an Item</kbd> tab on the right side of the layout screen.</p>

<p>This will bring you directly to the <a href="{root_url}cp/fields.html#create_field">New Field</a> page, and return you to the form's layout after adding the field to the form.</p>

<h3><a name="layout_field_overrides">Field Overrides</a></h4>

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
        <tr>
            <td>Show in Listing?</td>
            <td>{show_in_listing}</td>
            <td>Determines if the field will be shown in the <a href="#entries">entries listing</a> for the form. Hiding fields can be helpful to make the entries listing more usable.</td>
        </tr>
    </tbody>
</table>

<h3><a name="layout_library_fields">Library Fields</a></h3>
<p>Library Fields are created by marking a field as <kbd>Reusable</kbd> in its <a href="{root_url}cp/fields.html#field_settings">Field Settings</a>.</p>

<p>After a field is marked as <kbd>Reusable</kbd>, it is placed into the <kbd>Library</kbd> section of the <kbd>Toolbox</kbd>. This allows you to easily and quickly add the field to other forms in the system simply by clicking it's name.</p>

<p>To edit a field from the library, click it's <kbd>Edit...</kbd> button. This allows you to modify the field's settings, applying all of those changes to each form that uses the field from a single place.</p>

<p>Note that only fields that are not already assigned to the form will be listed in the <kbd>Library</kbd>.</p>

<h3><a name="layout_adding_headings">Adding a Heading</a></h3>
<p>Adding a heading to a form layout is very similar to adding a field. To add a new heading to a form, first click the form's <strong>Edit Layout</strong> link from the form listing (or click the form's <strong>Layout</strong> tab if you already have the form open). Then, scroll down in the <kbd>Toolbox</kbd> to the <kbd>Special</kbd> section and click the <strong>Heading</strong> button.</p>

<p>After adding a heading, it can be moved around the form layout similarly to a field. Simply click the heading to select it, then drag to move it to a new position. To edit a heading, click its <strong>Edit</strong> link after selecting it.</p>

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
            <td>{field_heading}</td>
            <td>The value to use as the heading's text. In the sample template and the markup generated by the <kbd>Simple Form Tag</kbd>, headings are added as &lt;h3&gt; HTML elements.</td>
        </tr>
    </tbody>
</table>

<h3><a name="layout_adding_blocks">Adding a HTML Block</a></h3>
<p>HTML Blocks allow you to add any kind of HTML to a form's layout. They function almost exactly the same as Headings, but do not attempt to escape their output and provide a larger textarea for entering the markup.</p>

<p>To add a new HTML Block to a form, first click the form's <strong>Edit Layout</strong> link from the form listing (or click the form's <strong>Layout</strong> tab if you already have the form open). Then, scroll down in the <kbd>Toolbox</kbd> to the <kbd>Special</kbd> section and click the <strong>HTML Block</strong> button.</p>

<p>After adding a HTML Block, it can be moved around the form layout similarly to a field. Simply click the HTML Block to select it, then drag to move it to a new position. To edit a HTML Block, click its <strong>Edit</strong> link after selecting it.</p>

<p>Each HTML Block contains a single configuration value:</p>

<table>
    <tbody>
        <tr>
            <th width="170">Name</th>
            <th>Template Var</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>HTML Content</td>
            <td>{field_html_block}</td>
            <td>The HTML content to inject into the form.</td>
        </tr>
    </tbody>
</table>

<h2><a name="multistep">Multistep Forms</a></h2>

<p><dfn>ProForm</dfn> provides the capability to build multistep forms. Separating a form into multiple steps is extremely simple.</p>

<p>Adding a step to a form is similar to adding a heading or a field. To add a new step in a form, first click the form's <strong>Edit Layout</strong> link from the form listing (or click the form's <strong>Layout</strong> tab if you already have the form open). Then, scroll down in the <kbd>Toolbox</kbd> to the <kbd>Special</kbd> section and click the <strong>Step</strong> button. Enter a name for the step, then click <strong>Submit</strong>.</p>

<p>After adding a step, it can be moved around the form layout similarly to a field. Moving the step around the layout splits the form into different sized steps. To move a step (and therefore move fields between them into different steps), simply click the step to select it, then drag to move it to a new position. To edit a step, click its <strong>Edit</strong> link after selecting it.</p>

<p>Step options:</p>

<table>
    <tbody>
        <tr>
            <th width="170">Name</th>
            <th>Template Var</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>Step Label</td>
            <td>{step}</td>
            <td>The value to use as the step's text. In the sample template and the markup generated by the <kbd>Simple Form Tag</kbd>, each step is represented as a tab.</td>
        </tr>
    </tbody>
</table>

<h2><a name="entries">Viewing and Exporting Entries</a></h2>
<p>Entries for a form can be viewed by clicking the View Entries link of the form listing. The entries list for a form provides all of it&#39;s entries in a paginated list.</p>

<p>On the entries list for a form, you may export the entries from the form through the Export Entries button. The file will be downloaded in a CSV formatted text file.</p>

<h3>Listing Configuration and View Entry</h3>
<p>You can hide certain columns from the entries listing by turning off the <strong>Show in Listing?</strong> override for the field (see <a href="#layout_field_overrides">Overriding Field Settings</a>). This can help make the view much more usable for more complex forms.</p>

<p>You may view all of the values recorded for an entry by clicking it's View link or it's ID in the entry listing. This will also show the full content of any longer text fields, which are truncated on the listing screen.</p>

<h2><a name="delete_form">Deleting a Form</a></h2>
<p>Deleting a form removes all of it&#39;s settings and deletes all of it&#39;s data from the database.</p>

<p>You can delete a form and all of it&#39;s data through the Delete command on the form listing. You must confirm the deletion in the following page.</p>

<p class="important"><strong>Warning</strong> Deleting a form cannot be undone. Old forms can be kept in the database indefinitely without any performance penalties.</p>

{ce:core:include template="global/_footer"}
