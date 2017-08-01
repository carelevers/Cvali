# Cvali
Simple validation class
Validation types
* "validateEmail",
* "validateAlpha",
* "validateAlphaNumeric",
* "validateInteger",
* "validateBoolean",
* "validateRequired",
* "validateFloat",
* "validateCreditcard",
* "validateNumeric",
* "validateStreetAddress",
* "validateIban",
* "validateDate",
* "validatePhonenumber",
* "validateRequired",
* "validatePostalcode",
* "validateTextarea",
* "validateTextInput",
* "validateBSN"

# Usage
//Setup class
require_once 'Validate.php';
$validate = new Validate;
//Define the validation rules
$registerRulesSet = array(
			"EmailField"                   => array("validateEmail", "validateRequired"),
);
//Add rules and form values to class
$validate->addValidation($values,$registerRulesSet);
//Run the validation
$validate->runValidation();
//Retrieve errors in case when not validated
$errors = $validate->getErrors();
//clean class
$validate->cleanUpValidationClass();
# Sanitation
//runs the sanitation based on the provided input
$validate->runSanitation($values);

