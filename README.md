# Form Fields - Fomit hook
Building web forms is a pain! But you know what's worse? Having to build the HTML email that gets sent to your client or user; sure, it's easy to throw together a template that matches your form, but what if the client needs another form... are you going to build another email? That makes no sense. 
Form Fields was born out of our frustrations of building good looking HTML emails that present submitted form information cleanly for many different forms with many different inputs. 

## How to use Form Fields
Form Fields is a Formit hook, just include it in your formit call before the email hook: ```[[!formit?  &hooks=`spam,formFields, email`]]```

If you are using a different email template that the default, include the placeholder `[[+formFields]]` in your HTML at the point you want your data to be outputted. 

If you are using your own repeater chunk you need to define the `ff_name` and `ff_value`, for example:

```
<table>
  <tr>
    <td> [[+ff_name]] </td>
    <td> [[+ff_value]] </td>
  </tr>
</table>
```

Out for the box this extra adds two chunks, `default_email_tpl` and `ff_email_repeater`, you can reference the default tpl in your FormIt snippet param ```&emailTpl=`default_email_tpl` ```. The `ff_email_repeater` chunk is used to output each indvidual field (used in the loop).

### Exmple

```
[[!FormIt?
     &hooks=`recaptchav3,FormItSaveForm,formFields,email`
     &emailTpl=`default_email_tpl`
     &emailTo=`[[++emailsender]]`
     &emailFrom=`no-reply@[[++http_host]]`
     &emailFromName=`[[++site_name]]@[[++http_host]]`
  ]]
```

## How does it work?
Form Fields comes with a default HTML template and a repeater chunk. When called within a Formit hook, it takes all submitted form data and loops it into the default template. It's that simple. 

## Can I customise Form Fields?
Yes, Form Fields comes with 5 settings which allow you to change the repeater chunk and define what data you want (or don't want) in your emails.

| Setting | Description |
|--|--|
| &ffRepeater | Repeater chunk to use when looping submitted data |
| &ffPrefix | By default any input with an underscore preceding it will be excluded from the data i.e. `<input name="_page-id" value="[[*id]]">` you can change this to whatever you want.|
|ffUseFormFields| By Default if you're using the FormItSaveForm hook and you specify which fields you'd like to save using ```&formFields=`your-field` ``` then only these will be included in the email. You can turn this off by setting ```&ffUseFormFields=`false` ``` or by setting which fields you'd like to process for the email.  |
| &ffProcess | If you only want to include certain fields then define them here in a comma-delimited string i.e. `first-name,email-address,telephone`. All other fields will be ignored |
| &ffExclude | Alternatively you can exclude certain fields, by default we exclude the token and action fields set by RecaptchaV2 hook. Define the fields you want to exlude with a comma-delimited string i.e. `submit,page-id,other-input`. This is handy if you have a field like `<input type-"submit" name="submit">` and you don't want it to show in your emails... Alternatively add an underscore to the name `_submit`, and it will be ignored. |
