G2Valid
=======

An Easy PHP Form Validation Class to make your life much easier when it comes to form validation.<br/>
**@author**  Gayan Silva  busygayan@gmail.com<br/>
PHP 5.0 or above is required

**License:**
GPL v2 http://www.gnu.org/licenses/gpl-2.0.txt

## Usage

* Include the G2Valid class to your php file
* Create an instance of the class
* Start validating :D

Basic Demo
--------------

```php
// Including The G2Valid Class
require('class.php');

/**
* Dummy Data to work with
* Replace With Your Own $_POST or %_GET 
*/
$_POST['name']      =   'Gayan';
$_POST['email']     =   'busygayan@gmail.com';
$_POST['number']    =   '111';
$_POST['web']       =   'http://dsignvilla.com';

/**
* Create a new instance of G2Valid
* Then parse the $_POST for validation
*/
$validator = new G2Valid($_POST);

// check if $_POST['email'] is a valid email
$validator->field('email')->validEmail();
// check if $_POST['web'] is a valid web address
$validator->field('web')->validURL();

/**
* Its Time to see if the form has any errors
* Lets check for any errors in the form
*/

if($validator-validate())
{
    // Do Anything with validated values 
    echo 'Everything Cool, I will do something';
    sendEmail();
}
else
{
    // If Any Errors are occoured, they can be displayed here
    echo $validator-field('email')->getError();
    echo $validator-field('web')->getError();
}
```

--------------

> The demo above only displayed few posibilities of the the class, lets dig in deeper to see whats available for us to use.

--------------

##Validation Options
    isEmpty($param)        - Checks for empty input (@param | String : custom error message)
    validEmail($param)      - Checks for valid email (@param | String : custom error message)
    validURL($param)        - Checks for valid URL according to http://www.faqs.org/rfcs/rfc2396 
                              (@param | String : custom error message)
                              
    isSame($field,$error)   - Checks if the $field is the same as the instace field
                              (@$field  | String : field to be compared with)
                              (@error   | String : custom error message)
                              Ex Use : ->field('passwd')->isSame('passConfirm','Didnt Match');
                             
    validIP($param)         - Checks for valid Ip Address  (@param : custom error message)
    minLength($min,$error)  - Check for Minimum Length of a string
                              ($min     | int : the minimum string size)
                              (@error   | String : custom error message)
                              
    maxLength($max,$error)  - Check for Maximum Length of a string
                              ($max     | int : the minimum string size)
                              (@error   | String : custom error message)
    
    alpha($param)           - Checks for A-Za-z and space (@param | String : custom error message)
    alphanumeric($param)    - Checks for A-Za-z 0-9 (@param | String : custom error message)
    isInt($param)           - Checks for an valid int (@param | String : custom error message)
    isFloat($param)         - Checks for an valid float (@param | String : custom error message)
    
--------------

##Error Reporting Options
Errors can be displayed in numerous ways, lets see whats available for us to use
<br/><br/>

**1- Get all Errors of a field as array** (@return array)<br/>
This wil return all the errors of a specific field as an array when more than 1 validation rule is set

    Validation  : $instance->field('username')->isEmpty()->minLength(5)->maxLength(10);
    Get Errors  : $instance->>field('username')->getErrorsAsArray();
<br/>
**2- Get Error of a field** (@return string)<br/>
Get the error of a field, if multiple validation rules are set, the 1st error occurance will be returned and also note that by setting **getError(true)** will return a detailed output with the error message and field name

    Validation  : $instance->field('username')->isEmpty('Enter a username');
    Get Errors  : $instance->>field('username')->getError();
<br/>
**3- Get all Errors of a field as a string** (@return string)<br/>
This wil return all the errors of a specific field as a string if multiple validation rules are set, also note that by setting **getErrorAsString(true)** will output the field names along with the error

    Validation  : $instance->field('website')->isEmpty()->validURL();
    Get Errors  : $instance->>field('website')->getErrorAsString();
<br/>
**4- Get all Errors of the form or instance** (@return array)<br/>
This might be good for debugging or if you plan to take validatoin to the next level, this will return all the errors of the instance as an array

    Validation
    ----------
    $instance->field('website')->isEmpty('Web site is required')->validURL('Enter Valid Website');
    $instance->field('username')->isEmpty('username is required')->minLength(5)->maxLength(10);
    $instance->field('email')->isEmpty('email is required')->validEmail('Enter Valid Email');
    
    Get Errors
    ----------
    $instance;
    
    // To simply inspect, you can use
    print_r($instance);

--------------

Dynamically Do Stuff
=======
 Okay, we do understand that its not only $_GET and $_POST stuff which should be validated, you might need to validate other thigns as well so in order to do that, I added support for dynamic fields to be created and validated.


```php
/** 
    Lets create a dynamic field, this will be a continuation from the basic demo
    So lets begin by creating a new field
*/
$validator->addField('db_id');

// Yeah its simple as that, but how can we set a value ? its easy too
$validator->field('db_id')->setValue($db->getId());

// Without using multiple lines, you can do it in a single line
$validator->addField('db_id')->setValue($db->getId()->isEmpty('No Db record')->isInt('Invalid id');
```
> Note that you can change values of any field by using **setValue();** method

So a complete demo
=======
Okay, Let me give show you a complete demo
```php
// Including The G2Valid Class
require('class.php');

/**
* For the time being,
* Just think that this is a registration form of a web site
*/
$_POST['name']      =   'Gayan-1';
$_POST['address']   =   '222/1 Some Lane Sri Lanka';
$_POST['password']  =   'mypass';
$_POST['passwd2']   =   'mypassz';
$_POST['email']     =   'busygayan@gmail.com';
$_POST['age']       =   '21';
$_POST['web']       =   'http://dsignvilla.com';

/**
* Create a new instance of G2Valid
* Then parse the $_POST for validation
*/
$validator = new G2Valid($_POST);

// Validate Name
$validator->field('name')->isEmpty('name is required')->alpha('Name can have only letters');
// Validate Address
$validator->field('address')->isEmpty('Address is required')->alphanumeric('enter a valid address');
// Validate Password
$validator->field('password')->minLength(5,'a password should contain atleast 5 chars');
// Password Confirmation
$validator->field('password')->isSame('passwd2','Passwords Did Not Match');
// Validate Email with custom error message
$validator->field('email')->validEmail('Valid Email Required');
// Validate Age
$validator->field('age')->isInt('Type age in numeric form');
// Validate Web
$validator->field('web')->validURL('Enter a correct web site address / URL');

/**
* Now Lets check for validation
*/
if($validator->validate())
{
    // Do something, register may be
    $user->register($_POST);
}
else
{
    /**
    * Their are too many ways to display the error messages, but i'm gonna show one by one :P
    */
    
    echo '<br/>' .  $validator->field('name')->getError();
    echo '<br/>' .  $validator->field('address')->getError();
    echo '<br/>' .  $validator->field('password')->getError();
    echo '<br/>' .  $validator->field('email')->getError();
    echo '<br/>' .  $validator->field('age')->getError();
    echo '<br/>' .  $validator->field('web')->getError();
}
```
> Thank you.
