{ce:core:include template="global/_header"}

<h1>SpamGuard Driver</h1>

<p><dfn>ProForm</dfn> has an integrated anti-spam fighting driver called SpamGuard with these capabilities:</p>

<ul>
    <li>Basic <kbd>honeypot detection</kbd> - create a real-looking field that only spam-bots will be tempted to fill in</li>
    <li>JavaScript based <kbd>"transparent CAPTCHA"</kbd> - require JavaScript, which many spam-bots don't bother running</li>
</ul>

<!-- ********************************************************************** -->

<h2>Contents</h2>
<ul>
    <li><a href="#activate">Turning it on</a></li>
    <li><a href="#mode">Error Mode</a></li>
    <li><a href="#honey">Honeypot</a></li>
    <li><a href="#js">JavaScript Transparent CAPTCHA</a></li>
</ul>

<!-- ********************************************************************** -->

<h2><a name="activate">Turning it on</a></h2>

<p>Activating SpamGuard for a form is a simple process.</p>

<ol>
    <li>Naviate to ProForm via the main menu: Add-ons > Modules > ProForm</li>
    <li>Click <b>Edit Settings</b> next to the form you wish to activate SpamGuard for</li>
    <li>Click the <b>Advanced Settings</b> tab</li>
    <li>From the <b>Select a Setting to Add</b> dropdown at the bottom of the Advanced Settings screen, select SpamGuard</li>
    <li>Click the <b>+</b> add button</li>
    <li>Configure SpamGuard's settings according to the next sections</li>
</ol>

<!-- ********************************************************************** -->

<h2><a name="mode">Error Mode</a></h2>

<p>The <b>Error Mode</b> - sets how SpamGuard will react when one of the configured tests fails.</p>

<table>
    <tr><th>Mode</th><th>Description</th></tr>
    <tr><td>Exit Immediately</td>
        <td>In this mode, SpamGuard will stop processing of a form submission instantly when it discovers an error. This will return a blank response to the bot script in most cases. Until you have tested the configuration of SpamGuard in combination with your particular site, it is advised to avoid this mode since it is a very unfriendly response and will cause your site to appear to be broken. It also provides any attacker with the least amount of information about why their post was rejected!</td></tr>
    <tr>
        <td>Return Validation Error</td>
        <td>This mode returns a global form validation error to the form template, informing the user that their submission could not be accepted because it failed spam detection algorithms. The default message is:<br/><br/>
        "There has been a request validation error, please try again"<br/><br/>
        You may change this with a parameter to your form tag:<br/><br/>
        <pre class="brush: xml">
            message:spamguard_validation_error="Custom error message"
        </pre>
        Note that no specifics are available about which test fails, but this message does give away the fact that we are doing more advanced bot detection, so you may want to avoid this mode in cases where you have extreme spam problems.
        </td>
    </tr>
</table>


<!-- ********************************************************************** -->

<h2><a name="honeypot">Honeypot Validation</a></h2>

<p>The <b>Honeypot</b> test uses an invisible field that remains visible to many bots. Some types of bots attempt to fill in all fields that they find with a value in order to avoid missing a required field. Honeypots work by tricking the bot into filling in a field that a normal human user (yes, even one on a screen reader) would never fill in.</p>

<p>In order to use this test, you must specify the <b>Honeypot Field Label</b> and <b>Honeypot Field Name</b> values. The label should be a human-friendly field name, complete with title case and spaces between words. The field name should be a lowercase value with underscores between words, similar to ProForm fields. Make up a realistic label and name for the honeypot field, but ensure it is not one you actually use on the site.</p>

<p>It is important to use a different made up field label and name on each site that you build.</p>

<p>This test does not make use of JavaScript.</p>

<!-- ********************************************************************** -->

<h2><a name="js">JS Trap</a></h2>

<p>The <b>JS Trap</b> test evaluates a basic math equation in JavaScript, and places the result in a hidden field. This value is checked against the expected equation in the backend (which is stored in the encrypted form configuration for that user's session). If the values do not match, the post is rejected.</p>

{ce:core:include template="global/_footer"}
