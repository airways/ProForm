{ce:core:include template="global/_header"}

<h1>Field Management</h1>

<p>The Fields page in <dfn>ProForm</dfn> provides a list of all of the fields that are available to be used in forms.</p>

<h3>Contents</h3>
<ul>
    <li><a href="#create_field">Creating a New Field</a></li>
    <li><a href="#field_settings">Field Settings</a></li>
    <li><a href="#types">Field Types</a></li>
    <li><a href="#validation_filtering">Validation and Filtering</a></li>
    <li><a href="#delete_field">Deleting a Field</a></li>
</ul>

<h2><a name="create_field">Creating a New Field</a></h2>

<p>There are two ways to create a new field. The first is to <a href="{root_url}cp/forms.html#layout_adding_fields">add a field</a> from the <kbd>Toolbox</kbd> on an existing form's <a href="{root_url}cp/forms.html#layout">Layout</a> screen. You can also use the <kbd>Create a Field</kbd> button on <dfn>ProForm</dfn>'s global <kbd>Fields</kbd> page, accessible from <dfn>ProForm</dfn>'s top navigation.</p>

<p>In either case, when creating a field you will be asked for the settings in the following section.</p>

<h2><a name="field_settings">Field Settings</a></h2>

<p>After creating a new field, the same settings that are set when it is initially created can be updated at any time by clicking either the Edit link for the field from a form&#39;s layout, or by clicking the field&#39;s name on the Fields page.</p>

<p>The following settings are available for fields.</p>

<h4>Field Settings</h4>
<table>
    <tbody>
        <tr>
            <th width="170">Field</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><b>Full Field Name (Label)</b></td>
            <td>Human friendly field name, used in UI and used to label the field in the default template.</td>
        </tr>
        <tr>
            <td><b>Field Name</b></td>
            <td>Short field name which must be used as the actual name of the input or other HTML element in the form template.</td>
        </tr>
        <tr>
            <td><b>Type</b></td>
            <td>The type of the field. Most fields can be set as String fields. See the Field Types section below for more information.</td>
        </tr>
        <tr>
            <td><b>Length</b></td>
            <td>Length limit on the field. If the length is not specified, it will be set to 255 characters. Settings the limit to longer than this may cause a slight performance degradation as a different database column must be used for longer values.</td>
        </tr>
        <tr>
            <td><b>Validation</b></td>
            <td>Allows configuration of a large number of validation rules and filters to be applied to the value. This ensures that data entered matches expected patterns. See the Validation section below for more information on the types of rules available.</td>
        </tr>
        <tr>
            <td><b>Placeholder</b></td>
            <td>Optional HTML5 placeholder text to use for the field. Note that use of this depends on your implementation, if you use the <kbd>Full Form Tag</kbd>, and it is automatically used when you use the <kbd>Simple Form Tag</kbd>.</td>
        </tr>
        <tr>
            <td><b>Upload Directory</b></td>
            <td>See the <b>File</b> type in the next section.</td>
        </tr>
        <tr>
            <td><b>Mailing List</b></td>
            <td>See the <b>File</b> type in the next section.</td>
        </tr>
        <tr>
            <td><b>Reusable</b></td>
            <td>Places the field into the global <kbd>Library</kbd>, allowing you to add it to multiple forms. This is great for fields such as <b>First Name</b> which can be used on multiple forms and always have the same validation.<br/><br/>
                
                The forms that this field is assigned to are displayed in the <b>Assigned Form</b> section under the Reusable checkbox.
            </td>
        </tr>
    </tbody>
</table>

<p class="important"><strong>Warning</strong> If data has already been entered into any of the forms that use a field, changing the length of the field to be shorter than it was previously will likely cause data loss as any values that are too long will be truncated.</p>

<h2><a name="types">Field Types</a></h2>
<p><dfn>ProForm</dfn> supports multiple field types. Most fields can safely use the String type, however other field types can optimize the column chosen to store the data and work well with the default template code to suggest input types to correspond to the various field types. Additional types provide programatic functionality such as subscribing to mailing lists.</p>

<p>Note that the Default HTML Control values provided here are based on the <a href="{site_url}documentation/proform/tags/form/template">Sample Template</a>, and can easily be changed by simply modifying the template you use to generate your forms. <dfn>ProForm</dfn> does not generate any markup itself - it leaves all of the display of your forms to your exact specifications.</p>

<h4>Field Types</h4>
<table>
    <tbody>
        <tr>
            <th width="120">Type</th>
            <th width="190">Default HTML Control</th>
            <th>Description</th>
            <th width="100">Database Type</th>
        </tr>
        <tr>
            <td><b>Checkbox</b></td>
            <td>&lt;input type="checkbox" ... /&gt;</td>
            <td>A simple boolean value stored as a y or n.</td>
            <td>VARCHAR</td>
        </tr>
        <tr>
            <td><b>Date</b></td>
            <td>&lt;input type="input" ... /&gt;</td>
            <td>A date field containing year, month, and day.</td>
            <td>DATE</td>
        </tr>
        <tr>
            <td><b>Date and Time</b></td>
            <td>&lt;input type="input" ... /&gt;</td>
            <td>A date field containing year, month, day as well as hours, minutes and seconds.</td>
            <td>DATETIME</td>
        </tr>
        <tr>
            <td><b>File</b></td>
            <td>&lt;input type="file" ... /&gt;</td>
            <td>A file upload field. When selected, an additional option is available:<br />
                <br />
                <b>Upload Directory</b> - Directory to upload files into.</td>
            <td>VARHCAR</td>
        </tr>
        <tr>
            <td><b>String</b></td>
            <td>&lt;input type="input" ... /&gt; or<br />
                &lt;textarea&gt;</td>
            <td>A simple text value. The length of the field determines which Database Type is used to store this field. If the length is 255 or less, a VARCHAR field is used, otherwise a TEXT field is used.</td>
            <td>VARCHAR or TEXT</td>
        </tr>
        <tr>
            <td><b>Text</b></td>
            <td>&lt;textarea&gt;</td>
            <td>A simple text value, for longer form values such as messages.</td>
            <td>TEXT</td>
        </tr>
        <tr>
            <td><b>Number: Integer</b></td>
            <td>&lt;input type="input" ... /&gt;</td>
            <td>An integer numeric value.</td>
            <td>INT</td>
        </tr>
        <tr>
            <td><b>Number: Float</b></td>
            <td>&lt;input type="input" ... /&gt;</td>
            <td>A floating point numeric value.</td>
            <td>FLOAT</td>
        </tr>
        <tr>
            <td><b>Number: Currency</b></td>
            <td>&lt;input type="input" ... /&gt;</td>
            <td>A currency value with a fixed point.</td>
            <td>DECIMAL</td>
        </tr>
        <tr>
            <td><b>List</b></td>
            <td>&lt;select&gt;</td>
            <td>A select box allowing the visitor to choose between options. When selected, an additional option is available:<br />
                <br />
                <b>Options</b> - A list of options available to the user. Uses either of the following formats for options, with one option on each line (these styles may be mixed within a single field if desired):
                <pre>
value : label&#10;value</pre>
            </td>
            <td>TEXT</td>
        </tr>
        <tr>
            <td><b>Mailing List Subscription</b></td>
            <td>&lt;input type="checkbox" ... /&gt;</td>
            <td>When this checkbox is selected, the user will be subscribed to the mailing list selected. When this type is selected, an additional option is available:<br />
                <br />
                <b>Mailing List</b> - The list to subscribe the user to when this checkbox is set.</td>
            <td>VARHCAR</td>
        </tr>
        <tr>
            <td><b>Hidden</b></td>
            <td>&lt;input type="hidden" ... /&gt;</td>
            <td>A simple text field rendered as a hidden input field. This can be used to attach additional data to the form. This field type is always set to an internal length limit of 255.</td>
            <td>VARCHAR</td>
        </tr>
        <tr>
            <td><b>Member Data</b></td>
            <td>&lt;input type="hidden" ... /&gt;</td>
            <td>Stores a piece of member information associated with the logged in user along with the form fields. Fields with this type cannot be set through the field POST, but are always set on the backend to the active user&#39;s information. This field type is always set to an internal length limit of 255. When selected, an additional option is available:<br />
                <br />
                <b>Field</b> - Select the Member Field you wish to save into this form field on submission.</td>
            <td>VARCHAR</td>
        </tr>
    </tbody>
</table>

<h2><a name="validation_filtering">Validation and Filtering</a></h2>

<p><dfn>ProForm</dfn> provides a robust set of validation rules and filtering operations. This system is based on the <a href="http://codeigniter.com/user_guide/libraries/form_validation.html">Form Validation Class</a> provided by CodeIgniter.</p>

<h4>Validation Rules</h4>
<p>The following validation rules can be applied to a field.</p>

<table>
    <tbody>
        <tr>
            <th>Rule</th>
            <th>Description</th>
            <th>Requires Param</th>
        </tr>
        <tr>
            <td>Always Required</td>
            <td>Marks the field as required on all forms that it is used on.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Matches Value</td>
            <td>Fails if the field&#39;s value does not match the value specified.</td>
            <td>Value</td>
        </tr>
        <tr>
            <td>Matches Field</td>
            <td>Fails if the field&#39;s value does not match the value of another field.</td>
            <td>Field name</td>
        </tr>
        <tr>
            <td>Min Length</td>
            <td>Fails if the field&#39;s value is not of the required length.</td>
            <td>Length</td>
        </tr>
        <tr>
            <td>Max Length</td>
            <td>Fails if the field&#39;s value is longer than the set length.</td>
            <td>Length</td>
        </tr>
        <tr>
            <td>Exact Length</td>
            <td>Fails if the field&#39;s value is shorter or longer than the set length.</td>
            <td>Length</td>
        </tr>
        <tr>
            <td>Alpha Characters Only</td>
            <td>Fails if any characters not in the alphabet are used in the field&#39;s value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Alpha Numeric Characters Only</td>
            <td>Fails if any characters not in the alphabet or the counting numbers (0-9) are used in the field&#39;s value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Alpha Numeric Characters, Underscores and Dashes Only</td>
            <td>Fails if any characters not in the alphabet, the counting numbers (0-9), or underscores (_) or dashes (-) are used in the field&#39;s value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Numeric Characters Only</td>
            <td>Fails if any characters other than the counting numbers (0-9) are used in the field&#39;s value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Numeric Characters and Dashes Only</td>
            <td>Fails if any characters other than the counting numbers (0-9), or dashes (-) are used in the field&#39;s value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Integer Number</td>
            <td>Fails if the field&#39;s value is not an integer number.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Natural Number</td>
            <td>Fails if the field&#39;s value is not a natural number.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Natural Number other than zero</td>
            <td>Fails if the field&#39;s value is not a natural number above or below zero.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Valid E-mail Address</td>
            <td>Fails if the field&#39;s value is not a properly formatted email address.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Valid E-mail Addresses separated by commas</td>
            <td>Fails if the field&#39;s value is not list of one or more properly formatted email address separated by commas.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Valid IP Address</td>
            <td>Fails if the field&#39;s value is not a properly formatted IPv4 dotted decimal address.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Valid Base 64 Encoded Value</td>
            <td>Fails if the value is not a valid Base 64 value.</td>
            <td>None</td>
        </tr>
    </tbody>
</table>

<h4>Filters</h4>
<p>In addition to the validation rules, the following filters can be applied to the data before or after any validation to modify submitted values to match the expected values.</p>

<table>
    <tbody>
        <tr>
            <th>Rule</th>
            <th>Description</th>
            <th>Requires Param</th>
        </tr>
        <tr>
            <td>Strip HTML (filter)</td>
            <td>Removes all HTML tags from the value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Trim (filter)</td>
            <td>Removes any whitespace from the beginning and ending of the value.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Base 64 Encode (filter)</td>
            <td>Encodes the value as Base 64.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>Base 64 Decode (filter)</td>
            <td>Decodes the value from Base 64.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>URL Encode (filter)</td>
            <td>Encodes the value according to URL specifications.</td>
            <td>None</td>
        </tr>
        <tr>
            <td>URL Decode (filter)</td>
            <td>Decodes value from URL encoding.</td>
            <td>None</td>
        </tr>
        <!-- <tr><td>Parse URL Component (filter)</td><td></td><td>None</td></tr> -->
    </tbody>
</table>

<h2><a name="delete_form">Deleting a Field</a></h2>
<p>Deleting a field removes all of it&#39;s settings, removes it from all forms that it is assigned to, deletes all of it&#39;s data from all forms in the database.</p>

<p>You can delete a field and all of it&#39;s data through the Delete command on the Fields page. You must confirm the deletion in the following page.</p>

<p class="important"><strong>Warning</strong> Deleting a field cannot be undone. Remember that <strong>all</strong> forms that have this field assigned will lose any data saved into it.</p>

{ce:core:include template="global/_footer"}