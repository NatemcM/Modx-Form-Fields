<?php
/**
 * Get all submitted fields that have been posted using a Formit
 * form and display them in the email without having to manually 
 * input them each time.
 * 
 * CHANGELOG
 * =========
 * 
 * Ver 0.1.1 BETA
 * Converted this to a formit hook, that way we don't have to sanitize any of the 
 * data received as Formit already handles that for us. 
 * 
 * Ver 0.1.0
 * Initial release
 * 
 */

// Set the output string 
$output = '';

// Set default settings, these can be overridden by using &setting=`` in formit call

/**
 * ffRepeater
 * @type string 
 * Name of chunk contianing email optomised HTML and variables [[+key]] [[+value]]
 * that is to be repeated with the given inputs
 */
$tpl            = $modx->getOption('ffRepeater', $hook->formit->config, 'ff_email_repeater');

/**
 * ffExcludePrefix
 * @type string 
 * Exclude inputs from your email by adding a _ or your own defined prefix
 * Note: This just removes the input from the form_files repeater tpl, you 
 * can still access the input using [[+_field-name]] in your email templates 
 */
$excludePrefix  = $modx->getOption('ffPrefix', $hook->formit->config, '_');

/**
 * ffields
 * @type array 
 * @dilimter ,
 * Only include these fields in your email. This is ideal if you don't want 
 * people to dynamically add in their own inputs to your form i.e. using 
 * the console to add <input name="bad" value="really bad" /> to your form.
 * By Default if you're using the formitSaveForm hook and you specify which 
 * fields you'd like to save using &formFields=`your-field` then only these 
 * will be included in the email. You can turn this off by setting 
 * &ffUseFormFields=`false` or by setting which fields you'd like to process
 * for the email.
 */
$useFormFields  = $modx->getOption('ffUseFormFields', $hook->formit->config, true);
$processFields  = $modx->getOption('ffProcess', $hook->formit->config, $useFormFields ? $modx->getOption('formFields', $hook->formit->config, false) : false );


/**
 * ffExcludeFields
 * @type array 
 * @dilimter ,
 * Set this field if you want to exclude inputs from being added to the repeater tpl. 
 * By default we stop the recpatcha3 action and token, if you have a hook that adds 
 * additional fields to your form and you don't want them to be proccessed in the 
 * email repeater, you can exclude them here
 */
$excludeFields  = $modx->getOption('ffExclude', $hook->formit->config, false);

// Get all fields for processing 
$fields = $hook->getValues();
 
// Exclude recaptcha fields by default
$excludeFieldsArray = array (
    $modx->getOption('recaptchav3.action_key'),
    $modx->getOption('recaptchav3.token_key')
);

// Add excluded fields to array
if ($excludeFields) {
    $ffExclude = explode(',', $excludeFields);
    foreach($ffExclude as $v) {
        $excludeFieldsArray[] = $v;
    }
}

if ($processFields) {
    
    // Reset fields array
    $fields = array();
    
    // Create a new array from string to loop through the formit->values
    $fieldsArray = explode(',',$processFields);
    
    // Loop through and set the $fields array with formit->values
    foreach ($fieldsArray as $fieldName) {
        $fields[$fieldName] = $hook->getValue($fieldName);
    }
}


foreach ($fields as $fieldKey => $fieldValue){
        
    // Check the key doesn't contian the excluded prefix i.e. '_' and it's not in the excluded fields array
    if (substr($fieldKey, 0, 1) != $excludePrefix && !in_array($fieldKey, $excludeFieldsArray)) {
        
        // Replace '-' to make words i.e. first-name == First name
        $fieldKey = str_replace('-',' ', $fieldKey);
        
        // Proper case the sentence i.e. first name == First name
        $output .= $modx->getChunk($tpl, array('ff_name' => htmlspecialchars(ucfirst($fieldKey)), 'ff_value' => htmlspecialchars($fieldValue)));
    }

}

$hook->setValue('form_fields', $output);
return true;