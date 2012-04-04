{ce:core:include template="global/_header"}

<h1>Sample Form Template (BETA)</h1>

<p>The following template serves as a reference for the basic template functionality of <dfn>ProForm</dfn>.</p>
<p>This template includes a small snippet of CSS, sufficient to create a multi column layout by aligning each fieldrow on it's own line.</p>

<p>This template also uses {segment_2} in order to determine which form should be rendered. This allows the template to render any template that has been created through the drag and drop layout.</p>

<p class="tip">Remember, this template shows one of the main benefits of <dfn>ProForm</dfn> - it has the capability to render any form created through it's editor through a single well defined and relatively short template.</p>

<p class="tip">The easiest way to use ProForm is to skip writing your own template all together and just use the pre-built layout created by the <a href="{root_url}/tags/form.html#simple">{exp:proform:simple}</a> tag.</p>

<p class="strongbox">Example Template</p>
<pre class="brush: xml">
&lt;style type="text/css"&gt;
    .error &#123;
        color: red;
    &#125;
    span.required &#123;
        color: red;
    &#125;
    .fieldRow &#123;
        clear:both;
        border: 1px dotted red;
    &#125;
    .fieldRow li &#123;
        display: inline;
        height: 30px;
        border: 1px dotted blue;
    &#125;
    .clear &#123;
        clear: both;
    &#125;
&lt;/style&gt;

&#123;exp:proform:form form_name="&#123;segment_2&#125;" thank_you_url="&#123;path=forms/thank_you&#125;"&#125;
    
    &lt;h1&gt;&#123;form_label&#125;&lt;/h1&gt;
    
    &#123;if error_count&#125;
    &lt;div class="errors"&gt;
        &lt;p&gt;The form has the following errors:&lt;/p&gt;
        &lt;ul&gt;
        &#123;errors&#125;
            &lt;li&gt;&#123;error&#125;&lt;/li&gt;
        &#123;/errors&#125;
        &lt;/ul&gt;
    &lt;/div&gt;
    &#123;/if&#125;
    &#123;if complete&#125;
        Thank you!
    &#123;if:else&#125;
        &#123;!-- render all hidden fields in one place - you can also do these inline if you like --&#125;
        &#123;fields&#125;
            &#123;if field_control == 'hidden'&#125;
                &lt;input type="&#123;field_control&#125;" name="&#123;field_name&#125;" value="&#123;field_value&#125;" /&gt;
            &#123;/if&#125;
        &#123;/fields&#125;

        &#123;!-- render normal fields in aligned rows --&#125;
        &#123;fieldrows&#125;
        &#123;!-- check if the row contains only hidden fields. if so, skip it because we already
             rendered it's fields. --&#125;
        &#123;if fieldrow:count &gt; fieldrow:hidden_count&#125;
            &lt;ul class="fieldRow"&gt;
            &#123;fields&#125;
                &#123;if field_control != 'hidden'&#125;&#123;!-- skip hidden fields --&#125;
                    &lt;li id="&#123;field_html_id&#125;" class="&#123;if field_error&#125;error&#123;/if&#125;
                            &#123;if field_is_required&#125;required&#123;/if&#125; &#123;field_html_class&#125;"&gt;
                        &#123;field_extra_1&#125;
                        &lt;label for="&#123;field_name&#125;"&gt;
                            &#123;field_label&#125;
                            &#123;if field_is_required&#125;
                                &lt;span class="required"&gt;*&lt;/span&gt;
                            &#123;/if&#125;
                        &lt;/label&gt;

                        &#123;if field_control == 'textarea'&#125;
                            &lt;textarea name="&#123;field_name&#125;" id="&#123;field_name&#125;"
                                cols="40" rows="10"&gt;&#123;field_value&#125;&#123;if error_count == 0&#125;&#123;field_preset_value&#125;&#123;/if&#125;&lt;/textarea&gt;
                        &#123;if:elseif field_control == 'checkbox'&#125;
                            &lt;input type="&#123;field_control&#125;" id="&#123;field_name&#125;" name="&#123;field_name&#125;" value="y" &#123;field_checked&#125; 
                                &#123;if field_preset_value == 'y' AND error_count == 0&#125;checked="checked"&#123;/if&#125; /&gt;
                        &#123;if:elseif field_control == 'select'&#125;

                            &lt;select id="&#123;field_name&#125;" name="&#123;field_name&#125;"&gt;
                                &#123;field_setting_list&#125;
                                    &lt;option value="&#123;row&#125;"&gt;&#123;row&#125;&lt;/option&gt;
                                &#123;/field_setting_list&#125;
                            &lt;/select&gt;
                        &#123;if:else&#125;
                            &lt;input type="&#123;field_control&#125;" id="&#123;field_name&#125;" name="&#123;field_name&#125;"
                                value="&#123;field_value&#125;&#123;if error_count == 0&#125;&#123;field_preset_value&#125;&#123;/if&#125;" /&gt;
                        &#123;/if&#125;
                        &#123;field_error&#125;
                        &#123;field_extra_2&#125;
                    &lt;/li&gt;
                &#123;/if&#125;
            &#123;/fields&#125;
            &lt;/ul&gt;
        &#123;/if&#125;
        &#123;/fieldrows&#125;


        &lt;p&gt;
            Enter this word: &#123;captcha&#125;&lt;br/&gt;
            &lt;input type="text" name="captcha" /&gt;&#123;if error:captcha&#125;&lt;span class="error"&gt;&#123;error:captcha&#125;&lt;/span&gt;&#123;/if&#125;
        &lt;/p&gt;

        &lt;p&gt;&lt;input type="submit" /&gt;&lt;/p&gt;
    &#123;/if&#125;
&#123;/exp:proform:form&#125;
</pre>

{ce:core:include template="global/_footer"}
