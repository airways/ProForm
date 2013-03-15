{ce:core:include template="global/_header" title="Extending ProForm"}

<h1>Extending ProForm</h1>
<p>ProForm provides a number of hooks in order to allow easy customization of it's functionality.</p>

<h3>Hooks</h3>

<table>
<tr><th>Hook</th><th>Params</th><th>Return</th>
<!-------------------->
<tr><td><b>proform_form_start</b></td><td>$module, $form_obj</td><td>$form_obj</td></tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>Called near the beginning of processing the &#123;exp:proform:form&#125; tag.</p>
    <p>This hook can be used to temporarily modify the form's properties in order to change the behavior of the form tag. It's also a good point to make queries on any custom database columns or tables you may have added, in order ot use them in later hooks.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_form_preparse</b></td>
    <td>$module, $tagdata, $form_obj, $variables, $var_pairs</td>
    <td><b>array</b>($tagdata, $form_obj, $variables, $var_pairs)</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>Called just before parsing the contents of the &#123;exp:proform:form&#125; tag.</p>
    <p>This hook can be used to inject any custom parsing you want into the tagdata, as well as changing the variables parsed by the tag. Any custom variables added will be parsed automatically. Note that for variable pairs, you must add their name to the $var_pairs array.</p>
    <p>Remember to pass back *all* of the noted values, enclosed in an array.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_entries_start</b></td>
    <td>$module, $form_obj</td>
    <td>$form_obj</td></tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just prior to processing the &#123;exp:proform:entries&#125; tag.</p>
    <p>Similarly to the proform_form_start hook, this can be used to modify the form's properties temporarily before it is used to process the entries request.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_entries_row</b></td>
    <td>$module, $form_obj, $row_vars</td>
    <td><b>array</b>($form_obj, $row_vars)</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called once for each row of data returned by the &#123;exp:proform:entries&#125; tag. You can use this hook to customize the database results before they are parsed.</p>
    <p>Remember to pass back *all* of the noted values, enclosed in an array.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_entries_end</b></td>
    <td>$module, $form_obj, $return_data</td>
    <td>$return_data</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just prior to returning the HTML result to the template from the &#123;exp:proform:entries&#125; tag.<p>
    <p>You can use this hook to customize the results before they are displayed.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_forms_start</b></td>
    <td>$module, $forms</td>
    <td>$forms</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is after getting the list of forms to be passed back by &#123;exp:proform:forms&#125;.<p>
    <p>You can use this to dynamically hide particular forms or otherwise change the forms in the listing.</p></td>
</tr> 
<!-------------------->
<tr><td><b>proform_forms_row</b></td>
    <td>$module, $form_obj, $row_vars</td>
    <td>$row_vars</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
  <p>This hook is called once for each of the database rows in &#123;exp:proform:forms&#125;, just prior to parsing it.</p>
  <p>This hook can be used to customize the data sent to the parsing function by modifying $row_vars.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_forms_end</b></td>
    <td>$module,  $forms, $return_data</td>
    <td>$return_data</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just before returning the result of the &#123;exp:proform:forms&#125; tag to the template. Use it to customize the parsing of the tag.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_process_start</b></td>
    <td>$module, $form_obj, $form_config, $form_session</td>
    <td><b>array</b>($form_obj, $form_config, $form_session)</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called at the beginning of processing a form submission. Use it to modify the form configuration, the form object, or change values in the session.<p>
    <p>Remember to pass back *all* of the noted values, enclosed in an array.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_validation_start</b></td>
    <td>$module, $form_obj, $form_session, $data</td>
    <td><b>array</b>($form_obj, $form_session, $data)</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called at the beginning of processing a form submission's validation. Use it to modify the form configuration, the form object, or change form values to be processed.<p>
    <p>Remember to pass back *all* of the noted values, enclosed in an array.</p></td>
</tr>
<!-------------------->
<tr><td><b>proform_validation_check_rules</b></td>
    <td>$module, $form_obj, $form_session, $data, $validation_rules</td>
    <td>$validation_rules</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called after the complete rule set for the form has been built, but before processing it. You can inject dynamic rules at this point by modifying the $validation_rules array.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_validation_field</b></td>
    <td>$module, $form_obj, $data, $field, $field_error</td>
    <td>$field_error</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called after processing the validation for each field. If there is an error for the field, it will be passed in through $field_error. This value can be modified by adding an error or removing an existing error to prevent validation from failing.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_insert_start</b></td>
    <td>$module, $form_obj, $data</td>
    <td>$data</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just before inserting the data for the form submission into the database table for the form. This can be used to push the final data array to an external source, generating some sort of custom notification, as well as changing the data to actually be stored in the DB.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_insert_start_session</b></td>
    <td>$module, $form_obj, $form_session</td>
    <td>$form_session</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>Similarly to proform_insert_start, this hook is called just before inserting the data for the form submission into the database table for the  form. The entire form_session can be inspected and modified if needed.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_insert_end</b></td>
    <td>$module, $form_session</td>
    <td>none</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just after inserting the data for the form submission into the database. This can be used to push the final data array to an external source, generating some sort of custom notification, or a number of other purposes.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_insert_end_ex</b></td>
    <td>$module, $form_obj, $form_session</td>
    <td>none</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called just after inserting the data for the form submission into the database. This can be used to push the final data array to an external source, generating some sort of custom notification, or a number of other purposes.<p></td>
</tr>
<!-------------------->
<tr><td><b>proform_no_insert</b></td>
    <td>$module, $form_obj, $data</td>
    <td>$data</td>
</tr>
<tr><td colspan="3">
    <p><b>Description</b></p>
    <p>This hook is called instead of proform_insert_start and proform_insert_end for <em>share</em> type forms, where no insert is needed. Remember that you <b>must</b> return the data array in order for the form submission to work correctly.<p>
    <p>This can be used to push the final data array to an external source, generating some sort of custom notification, or a number of other purposes.</p></td>
</tr>



</table>

{ce:core:include template="global/_footer"}
