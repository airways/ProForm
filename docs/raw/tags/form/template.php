{ce:core:include template="global/_header" title="Sample Form Template"}

<h1>Sample Form Template</h1>

<p>The following template serves as a reference for the basic template functionality of <dfn>ProForm</dfn>.</p>
<p>This template makes use of some JavaScript and CSS code, which can be automatically inserted by using the &#123;exp:proform:head&#125; tag inside your &lt;head&gt;, or by copying it out of the <a href="{root_url}tags/form/template-script.html">script sample</a>.</p>

<p>This template uses &#123;segment_2&#125; in order to determine which form should be rendered. This allows the template to render any template that has been created through the drag and drop layout.</p>

<p class="tip">This template shows one of the main benefits of <dfn>ProForm</dfn> - it has the capability to render any form created through it's editor through a single well defined and relatively short template.</p>

<p class="tip">The easiest way to use ProForm is to skip writing your own template all together and just use the pre-built layout created by the <a href="{root_url}tags/form.html#simple">Simple Form Tag</a> instead.</p>

<p class="strongbox">Example Template</p>

<textarea cols="80" rows="40" style="width: 100%; font-family: courier;">
&#123;exp:proform:form form_name="&#123;segment_2&#125;" variable_prefix="pf_"&#125;
    &#123;if pf_no_results&#125;
        &#123;if pf_pref:invalid_form_message&#125;
            &#123;pf_pref:invalid_form_message&#125;
        &#123;if:else&#125;
            Invalid form name specified!
        &#123;/if&#125;
    &#123;/if&#125;
    
    &#123;if pf_complete&#125;
        &#123;if pf_pref:thank_you_message&#125;
            &#123;pf_pref:thank_you_message&#125;
        &#123;if:else&#125;
            Thank you for your submission!
        &#123;/if&#125;
    &#123;if:else&#125;

    &#123;pf_hidden_fields&#125;
        &lt;input type="hidden" name="&#123;pf_field_name&#125;" value="&#123;pf_field_value&#125;" /&gt;
    &#123;/pf_hidden_fields&#125;

    &lt;input type="hidden" name="_pf_current_step" value="&#123;pf_current_step&#125;" /&gt;
    &lt;input type="hidden" name="_pf_goto_step" value="" /&gt;

    &#123;if pf_multistep&#125;
        &lt;ul class="pf_steps"&gt;
            &#123;pf_steps&#125;
                &lt;li&gt;&lt;a href="#&#123;pf_step_no&#125;" class="pf_step &#123;pf_step_active&#125;"&gt;&#123;pf_step&#125;&lt;/a&gt;&lt;/li&gt;
            &#123;/pf_steps&#125;
        &lt;/ul&gt;
    &#123;/if&#125;

    &lt;div class="pf_wrap"&gt;

        &#123;pf_fieldrows&#125;
        &lt;ul class="pf_row"&gt;
            &#123;pf_fields&#125;
                &#123;if pf_field_type != "invisible"&#125;
                    &lt;li id="&#123;pf_field_html_id&#125;" class="pf_column &#123;pf_field_html_class&#125;"&gt;
                    &#123;if pf_field_html_block&#125;
                        &#123;pf_field_html_block&#125;
                    &#123;if:elseif pf_field_heading&#125;
                        &lt;h3&gt;&#123;pf_field_heading&#125;&lt;/h3&gt;
                    &#123;if:else&#125;
                        &#123;if pf_field_type != "checkbox"&#125;
                            &lt;label for="&#123;pf_field_name&#125;"&gt;&#123;pf_field_label&#125; &#123;if pf_field_is_required&#125;&lt;span class="required"&gt;*&lt;/span&gt;&#123;/if&#125;&lt;/label&gt;
                            &lt;div class="pf_field &#123;if pf_vertical&#125;pf_vertical&#123;/if&#125;"&gt;
                        &#123;/if&#125;
                        &#123;if pf_field_type == "string"&#125;
                            &#123;if pf_field_validation:valid_email&#125;
                                &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="validate-email &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                            &#123;if:else&#125;
                                &#123;if pf_field_length &lt;= 255&#125;
                                    &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="&#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                                &#123;if:else&#125;
                                    &#123;if pf_wysiwyg&#125;
                                        &lt;textarea name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="&#123;pf_field_is_required&#125;"&gt;&#123;pf_field_value&#125;&lt;/textarea&gt;
                                        &lt;script type="text/javascript"&gt;bkLib.onDomLoaded(function() &#123; new nicEditor(pf_nic_config).panelInstance('&#123;pf_field_name&#125;'); &#125;);&lt;/script&gt;
                                    &#123;if:else&#125;
                                        &lt;textarea name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="&#123;pf_field_is_required&#125;"&gt;&#123;pf_field_value&#125;&lt;/textarea&gt;
                                    &#123;/if&#125;
                                &#123;/if&#125;
                            &#123;/if&#125;
                        &#123;if:elseif pf_field_type == "text"&#125;
                            &#123;if pf_wysiwyg&#125;
                                &lt;textarea name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="&#123;pf_field_is_required&#125;"&gt;&#123;pf_field_value&#125;&lt;/textarea&gt;
                                &lt;script type="text/javascript"&gt;bkLib.onDomLoaded(function() &#123; new nicEditor(pf_nic_config).panelInstance('&#123;pf_field_name&#125;'); &#125;);&lt;/script&gt;
                            &#123;if:else&#125;
                                &lt;textarea name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="custom_class_here &#123;pf_field_is_required&#125;"&gt;&#123;pf_field_value&#125;&lt;/textarea&gt;
                            &#123;/if&#125;
                        &#123;if:elseif pf_field_type == "date"&#125;
                            &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="date &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                        &#123;if:elseif pf_field_type == "datetime"&#125;
                            &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="datetime &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                        &#123;if:elseif pf_field_type == "time"&#125;
                            &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="time &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                        &#123;if:elseif pf_field_type == "integer"&#125;
                            &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="validate-integer &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                        &#123;if:elseif pf_field_type == "float"&#125;
                            &lt;input type="text" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" class="validate-float &#123;pf_field_is_required&#125;" value="&#123;pf_field_value&#125;" placeholder="&#123;pf_field_placeholder&#125;" /&gt;
                        &#123;if:elseif pf_field_type == "file"&#125;
                            &lt;div class="pf_files"&gt;
                                &lt;input name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" type="file" class="&#123;pf_field_is_required&#125;" /&gt;
                            &lt;/div&gt;
                        &#123;if:elseif pf_field_type == "checkbox"&#125;
                            &lt;div class="pf_field"&gt;
                                &lt;div class="pf_option"&gt;
                                    &lt;input type="checkbox" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" value="y" &#123;if pf_field_checked&#125;checked="checked"&#123;/if&#125; class="&#123;pf_field_is_required&#125;" /&gt;&lt;label for="&#123;pf_field_name&#125;"&gt;&#123;pf_field_label&#125;&lt;/label&gt;
                                &lt;/div&gt;
                            &lt;/div&gt;
                        &#123;if:elseif pf_field_type == "list" || pf_field_type == "relationship"&#125;
                            &#123;if pf_field_setting_style == "check" || pf_field_setting_style == "radio"&#125;
                                &#123;pf_field_options&#125;
                                    &#123;if pf_is_divider&#125;
                                        &#123;if pf_divider_number &gt; 0&#125;
                                            &lt;/fieldset&gt;
                                        &#123;/if&#125;
                                        &lt;fieldset&gt;
                                            &lt;legend&gt;&#123;pf_label&#125;&lt;/legend&gt;
                                    &#123;if:else&#125;
                                        &lt;div class="pf_option &#123;if pf_vertical&#125;pf_vertical&#123;/if&#125;"&gt;
                                            &#123;if pf_field_setting_style == "check"&#125;
                                                &lt;input type="checkbox" name="&#123;pf_field_name&#125;[]" id="&#123;pf_field_name&#125;_&#123;pf_key&#125;" value="&#123;pf_key&#125;" &#123;if pf_selected&#125;checked="checked"&#123;/if&#125; class="&#123;pf_field_is_required&#125;" /&gt;
                                            &#123;/if&#125;
                                            &#123;if pf_field_setting_style == "radio"&#125;
                                                &lt;input type="radio" name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;_&#123;pf_key&#125;" value="&#123;pf_key&#125;" &#123;if pf_selected&#125;checked="checked"&#123;/if&#125; class="&#123;pf_field_is_required&#125;" /&gt;
                                            &#123;/if&#125;
                                            &lt;label for="&#123;pf_field_name&#125;_&#123;pf_key&#125;"&gt;&#123;pf_label&#125;&lt;/label&gt;
                                        &lt;/div&gt;
                                    &#123;/if&#125;
                                &#123;/pf_field_options&#125;
                                &#123;if pf_divider_count &gt; 0&#125;
                                    &lt;/fieldset&gt;
                                &#123;/if&#125;
                            &#123;if:else&#125;
                                &lt;select name="&#123;pf_field_name&#125;" id="&#123;pf_field_name&#125;" &#123;if pf_multiple&#125;multiple="multiple"&#123;/if&#125; class="&#123;pf_field_is_required&#125;"&gt;
                                    &#123;pf_field_options&#125;
                                        &#123;if pf_is_divider&#125;
                                            &#123;if pf_divider_number &gt; 0&#125;
                                                &lt;/optgroup&gt;
                                            &#123;/if&#125;
                                            &lt;optgroup label="&#123;pf_label&#125;"&gt;
                                        &#123;if:else&#125;
                                            &lt;option value="&#123;pf_key&#125;" &#123;pf_selected&#125;&gt;&#123;pf_row&#125;&lt;/option&gt;
                                        &#123;/if&#125;
                                    &#123;/pf_field_options&#125;
                                    &#123;if pf_divider_count &gt; 0&#125;
                                        &lt;/optgroup&gt;
                                    &#123;/if&#125;
                                &lt;/select&gt;
                            &#123;/if&#125;
                        &#123;if:else&#125;
                            &#123;if pf_field_driver&#125;
                                &#123;pf_field_driver&#125;
                            &#123;if:else&#125;
                                &lt;input type="&#123;pf_field_control&#125;" id="&#123;pf_field_name&#125;" name="&#123;pf_field_name&#125;" value="&#123;pf_field_value&#125;" class="&#123;pf_field_is_required&#125;" /&gt;
                            &#123;/if&#125;
                        &#123;/if&#125;

                        &#123;if pf_field_error&#125;&lt;div id="text-E" class="errMsg"&gt;&lt;span&gt;&#123;pf_field_error&#125;&lt;/span&gt;&lt;/div&gt;&#123;/if&#125;
                
                        &#123;if pf_field_type != "checkbox"&#125;
                            &lt;/div&gt;
                        &#123;/if&#125;
                    &lt;/li&gt;
                    &#123;/if&#125;
                &#123;/if&#125;
            &#123;/pf_fields&#125;
            &lt;/ul&gt;
            &lt;div class="pf_clear"&gt;&lt;/div&gt;
        &#123;/pf_fieldrows&#125;
        
        &lt;div class="pf_buttons"&gt;
            &#123;if pf_use_captcha&#125;
                &lt;div class="pf_captcha"&gt;
                    Enter this word: &#123;pf_captcha&#125;&lt;br/&gt;
                    &lt;input type="text" name="captcha" /&gt;&#123;if pf_error:captcha&#125;&lt;span class="error"&gt;&#123;pf_error:captcha&#125;&lt;/span&gt;&#123;/if&#125;
                &lt;/div&gt;
            &#123;/if&#125;
            &#123;if pf_multistep&#125;
                &lt;input type="submit" name="_pf_goto_previous" value="&lt; Previous" &#123;if pf_on_first_step&#125;disabled="disabled"&#123;/if&#125; /&gt;
                &lt;input type="submit" name="_pf_goto_next" value="Next &gt;" &#123;if pf_on_last_step&#125;disabled="disabled"&#123;/if&#125; /&gt;
            &#123;/if&#125;
            &#123;if pf_on_last_step&#125;
                &lt;input type="submit" name="_pf_finish" value="Submit" /&gt;
            &#123;if:else&#125;
                &lt;input type="submit" value="Submit" disabled="disabled" /&gt;
            &#123;/if&#125;
        &lt;/div&gt;
        &#123;/if&#125;
    &lt;/div&gt;
&#123;/exp:proform:form&#125;
</textarea>

{ce:core:include template="global/_footer"}
