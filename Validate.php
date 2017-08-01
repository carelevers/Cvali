<?php
/**
 * CVali - A Carel made validation class
 *
 * @author      Carel Evers
 *
 * @version     1.1
 */

 
class Validate{

    /**
     * @var Array $fields containing input fields
     */
    private $fields = [];
    /**
     * @var  Array $values contain input values
     */
    private $values = [];
    
    /**
     * @var Array $validationRules containing the specific validation and sanitation rules
     */
    private $validationRules = [];
    
    
    /**
     * @var Array that holds the error messages
     */
    private $errors = [];
    
    /**
     * @var Array containg all the validator methods
     */
    private $validators = [ "validateEmail",
                            "validateAlpha",
                            "validateAlphaNumeric",
                            "validateInteger",
                            "validateBoolean",
                            "validateRequired",
                            "validateFloat",
                            "validateCreditcard",
                            "validateNumeric",
                            "validateStreetAddress",
                            "validateIban",
                            "validateDate",
                            "validatePhonenumber",
                            "validateRequired",
                            "validatePostalcode",
                            "validateTextarea",
                            "validateTextInput",
                            "validateBSN"
                          ];
    /** @var Array containg all the sanitation methods*/
    private $sanitizers = [ "SanitizeString",
                            "SanitizeHtmlencode",
                            "SanitizeEmail",
                            "SanitizeNumbers",
                            "SanitizeFloats"
                          ];
        
    
    /**
     * adds the values of the provided values and rules to the class vars
     *
     * @param array $values
     * @param array $registerRulesSet
     * @return boolean
     */
    public function addValidation($values,$registerRulesSet){
        foreach($values as $field => $value){       
            if(array_key_exists($field,$registerRulesSet)){
                $this->fields[] = $field;
                $this->values[] = $value;           
            }
        }
        
        if($this->checkFieldAndValue($registerRulesSet) && $this->checkRules($registerRulesSet)){
         
            $this->addRulesToFields($registerRulesSet);
          
            return true;
        }
        return false;
    }
    
    /**
     * Checks if the provided field and values count matches and are not empty
     *
     * @param array $registerRulesSet
     *
     * @return boolean
     */
    public function checkFieldAndValue($registerRulesSet)
    {
        $countFields = count ($this->fields);
        $countValues = count ($this->values);
        
        if($countFields == $countValues && !empty($countFields) && !empty($countValues)){
            
            return true;
        }
        return false;
    }
    /**
     * Checks if the provided rules exsist and add them to validationRules array
     *
     * @param array $registerRulesSet
     *
     * @return boolean
     */
    public function checkRules($registerRulesSet){  
        foreach($registerRulesSet as $rules){
            foreach($rules as $rule){
                if(in_array ($rule,$this->validators)){ 
                    $this->validationRules[] = $rule;
                   
                }else{
                  
                    return false;
                }
            }
        }
        if(!empty($this->validationRules)){
            
            $this->validationRules = array_unique ($this->validationRules );
           
            return true;
        }  
    }
    /**
     * adds the rules to the right fields
     * 
     * @param array $rules
     * 
     */
    public function addRulesToFields($rules){
       
        foreach($this->fields as $key => $field){
    
            $result[$field] = $rules[$field];
        }
        $this->fields = $result;
       
    }
    /**
     * Runs the validation specified in the rules
     *
     * @return boolean
     */
    public function runValidation(){
        $keys = 0;
        foreach($this->fields as $key => $value){
            foreach($value as $validators){
                $result[] = call_user_func_array(array($this, $validators), [$this->values[$keys],$key]); 
            }
        $keys++;
        }
        //add errors to error array
        $this->errors = $result;
        
        //returns false when array contains an error array
        $bool = true;
        foreach($result as $validated){
            if(is_array($validated)) {
                $bool = false;
            }
        }
        return $bool;
    }
    /**
     * empties all the values
     */
    public function cleanUpValidationClass(){
        $this->fields = array();
        $this->values = array();
        $this->validationRules = array();
    }
    /** Runs the sanitation based on the provided input
     * @param array $input
     * @return array
     */
    public function runSanitation($input){
        foreach($input as $key=>$value){
            $result[$key] = $value;
            if(is_string($value)){
               $result[$key]  = $this->sanitizeString($value);
               $result[$key]  = $this->sanitizeHtmlencode($value);
            }
            if($key == "email"){
               $result[$key] = $this->sanitizeEmail($value);   
            }
            if($key == "v_birthday"){
                $result[$key] = $this->sanitizeDate($value);
            }
            if($key == "v_startdate"){
                $result[$key] = $this->sanitizeDate($value);
            }
            if($key == "v_enddate"){
                $result[$key] = $this->sanitizeDate($value);
            }
        }
       
        return $result;
   
    }

    /**
     *This generates user readable error messages
     *
     * @return array
     */
    public function errorGenerator(){
        $readError = [];
        foreach($this->errors as $error){
            if(is_array($error)){
                if($error['error'] == "required"){
                   $readError[$error['field']] = "Veld is verplicht";
                }
                if($error['error'] == "email"){
                   $readError[$error['field']] = "Ongeldig email adress";
                }
                if($error['error'] == "alpha"){
                   $readError[$error['field']] = "Ongeldige invoer, nummers en speciale tekens zijn niet toegestaan";
                }
                if($error['error'] == "alpha-num"){
                   $readError[$error['field']] = "Ongeldige invoer, speciale tekens zijn niet toegestaan";
                }
                if($error['error'] == "num"){
                   $readError[$error['field']] = "Ongeldige invoer, alleen nummers zijn toegestaan, voorbeeld : 5";
                }
                if($error['error'] == "int"){
                   $readError[$error['field']] = "Ongeldige invoer, alleen hele nummers zijn toegestaan";
                }
                if($error['error'] == "bool"){
                   $readError[$error['field']] = "Ongeldige invoer, er moet minstens één keuze worden gemaakt";
                }
                if($error['error'] == "float"){
                   $readError[$error['field']] = "Ongeldige invoer, Alleen komma getallen zijn toegestaan";
                }
                if($error['error'] == "creditcard"){
                   $readError[$error['field']] = "Creditcard nummer niet geldig";   
                }
                if($error['error'] == "cvccode"){
                   $readError[$error['field']] = "kaartverificatiecode(cvc) niet geldig";
                }
                if($error['error'] == "expiration"){
                   $readError[$error['field']] = "Creditcard is niet meer geldig";
                }
                if($error['error'] == "address"){
                   $readError[$error['field']] = "Ongeldig adres";
                }
                if($error['error'] == "postalcode"){
                   $readError[$error['field']] = "Ongeldige postcode";
                }
                if($error['error'] == "iban"){
                   $readError[$error['field']] = "Ongeldig iban nummer , voorbeeld : NL20INGB000123456";
                }
                if($error['error'] == "date"){
                   $readError[$error['field']] = "Ongeldige datum";
                }
                if($error['error'] == "textarea"){
                   $readError[$error['field']] = "Ongeldige invoer, speciale tekens zijn niet toegestaan";
                }
                if($error['error'] == "textinput"){
                    $readError[$error['field']] = "Ongeldige invoer, speciale tekens zijn niet toegestaan";
                }
                if($error['error'] == "bsn"){
                    $readError[$error['field']] = "Ongeldige BSN";
                }
            }
        }
        
        return $readError;
    }
    /**
     * @return array
     */
    public function getErrors(){
           return $this->errorGenerator();
    }

    /*--------VALIDATION METHODS-----------*/
    
    /**
     * Check if the provivided value is a valid lenght
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    public function validateRequired($value,$field,$customLenght = false){
        
        if(!isset($value) || empty($value)){
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'required'
            );
        }else{
            return true;
        }
    }
     /**
     * Check if the provided email is valid.
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateEmail($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'email'
            );
        }else{
          return true;
        }
    }
    
    /**
     * Check if the provided value contains only alpha characters.
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateAlpha($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
       if (!preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) !== false) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'alpha'
            );
        }else{
            return true;
        }
    }
    /**
     * Check if the provided value contains only alpha-numeric characters.
     * 
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateAlphaNumeric($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) !== false) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'alpha-num'
            );
        }else{
            return true;
        }
    }
    /**
     * Check if the provided value is an number or numeric string.
     *
     *
     * @param string $value
     * 
     * @return mixed
     */
    protected function validateNumeric($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (!preg_match('/^[-]?[0]|[1-9][0-9]$/',$value) !== false) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'num'
            );
        }else{
            return true;
        }
    }
    /**
     * check if the provided value is an integer.
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateInteger($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'int'
            );
        }else{
            return true;
        }
    }
    /**
     * check if the provided value is a boolean.
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateBoolean($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if($value === true || $value === false) {
            return true;
        }else{
            return true;
        }
    }
    /**
     * Check if the provided value is a valid float.
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateFloat($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'float'
            );
        }else{
            return true;
        }
    }
    
    /**
     * Check if the provided value is a valid credit card number.
     *

     * @param string $value
     *
     * @return mixed
     */
    protected function validateCreditcard($value,$field)
    {
        global $creditcard;
        if (!isset($value) || empty($value)) {
            return true;
        }
        $number     = $creditcard::validCreditCard($value['number']);
        $cvcCode    = $creditcard::validCvc($value['cvc'], $number['type']);
        $expiration = $creditcard::validDate($value['dateyear'],$value['datemonth']);
        if (!$number['valid']) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'creditcard'
            );
        }else if (!$cvcCode) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'cvccode'
            );  
        }else if(!$expiration){
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'expiration'
            );  
        }else{
            return true;
        }
    }
   /**
     * Check if the provided value is probably a postal code
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validatePostalcode($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        // Theory, postalcode has : 1 number, 1 or more letters
        $hasLetter = preg_match('/[a-zA-Z]/', $value);
        $hasDigit = preg_match('/\d/', $value);
        $requirements = $hasLetter && $hasDigit;
        if (!$requirements) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'postalcode'
            );
        }else{
            return true;
        }
    }
    /**
     * Check if the provided value is probably an street address
     *
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function validateStreetAddress($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        // Theory, adress has: 1 number, 1 or more spaces, 1 or more words
        $hasLetter = preg_match('/[a-zA-Z]/', $value);
        $hasDigit = preg_match('/\d/', $value);
        $hasSpace = preg_match('/\s/', $value);
        $requirements = $hasLetter && $hasDigit && $hasSpace;
        if (!$requirements) {
            return array(
                'field' =>  $field,
                'value' => $value,
                'error' => 'address'
            );
        }else{
            return true;
        }
    }
     /**
     *TODO
     * check if the provided value is a valid Iban (ISO 8601).
     *
      * @param string $value
     *
     * @return mixed
     */
    
    protected function validateIban($value,$field)
    {
        global $iban;
        if (!isset($value) || empty($value)) {
            return true;
        }
        if(!$iban->Verify($value,true)) {
        return array(
                'field' => $field,
                'value' => $value,
                'error' => 'iban',
            );
        }else{
            return true;
        }

    }
    
    /**
     *
     * check if the provided value is a valid date (ISO 8601) format.
     *
     * @param string $value date or datetime
     *
     * @return mixed
     */
    protected function validateDate($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        $cdate1 = date('d-m-Y', strtotime($value));
        $cdate2 = date('d-m-Y H:i:s', strtotime($value));
        if ($cdate1 != $value && $cdate2 != $value) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'date'
            );
        }else{
            return true;
        }
    }
    /**
     *
     * check if the provided value doesn't contain any special chars
     *
     * @param string $value 
     *
     * @return mixed
     */
    protected function validateTextarea($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (!preg_match("/^[A-Za-z0-9.,+]/", $value)) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'textarea'
            );
        }else{
            return true;
        }
    }
    
    /**
     *
     * check if the provided value doesn't contain any special chars
     *
     * @param string $value 
     *
     * @return mixed
     */
    protected function validateTextInput($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        if (!preg_match("/^[A-Za-z0-9.,+he]/", $value)) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'textinput'
            );
        }else{
            return true;
        }
    }
    
     /**
     *
     * check if the provided value is a valid bsn
     *
     * @param string $value 
     *
     * @return mixed
     */
    protected function validateBSN($value,$field)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        $length = preg_match('/^\d{9}$/', $value);
    	$sum = 0;
        $check = false;
        if($length){
              for ($i=9; $i > 0; $i--) $sum += ($i == 1 ? -1 : $i) * $value[9 - $i];
              $check =  $sum && $sum % 11 == 0;
        }
		
        if (!$check && !$length) {
            return array(
                'field' => $field,
                'value' => $value,
                'error' => 'bsn'
            );
        }else{
            return true;
        }
    }
    
    /*-----SANITIZE METHODS----*/
    /**
     * Sanitize the value by removing script tags
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeString($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * Sanitize the value by replacing special chars with their html unicode equivalent
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeHtmlencode($value)
    {
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    /**
     * Sanitize email address
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeEmail($value)
    {
       
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    /**
     * Sanitize numbers
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeNumbers($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    /**
     * Sanitize floats
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeFloats($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    /**
     * Sanitize iban
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeIban($value)
    {
      
    }
    /**
     * Sanitize date
     * Actually converts date from Y-m-d to d-m-Y
     *
     * @param string $value
     *
     * @return string
     */
    protected function sanitizeDate($value)
    {
        $date = DateTime::createFromFormat('d-m-Y', $value);
        return $date->format('Y-m-d');
    }
}