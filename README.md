MayoForm
========

PHP Class to easily generate &amp; validate HTML Forms.

<h1>Installation</h1>

To install, just copy the <strong>MayoForm.php</strong> file somewhere in your project (you can rename the file and/or the class to match
an eventual autoloader).

You will also need all the constant defined in the file <strong>Form/Template/bootstrap.php</strong>. You can either copy-paste them or
include the file directly (putting them in a file allows you to have several templates and choose the one you want to
load on each page).

Note that the default template uses bootstrap, so you can load bootstrap CSS and have pretty forms without writing any
CSS code.

<h1>Usage</h1>

<h2>Form creation</h2>

To create a form, you have to create a new class which extends the MayoForm class.

``` php
class Form_Example extends MayoForm
{
    ....
}
```

First, you can override the submitName property if you want to set a custom name attribute to the submit button of the form.

``` php
class Form_Example extends MayoForm
{
    protected $submitName = 'my_custom_submit_name'
    
    ....
}
```

You can now create your form with the MayoForm methods, by overriding the constructor and giving an array filled with the form attributes as argument.

``` php
class Form_Example extends MayoForm
{
    ....
    
    public function __construct()
    {
        parent::__construct(array(
            'action' => 'index.php',
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'method' => 'POST'
        ));
    
    ....
}
```

Once the form is created, just add every field you need with the MayoForm::addField($array) method (see the <a href="#fields-options">Fields options</a> section for more details).

``` php
class Form_Example extends MayoForm
{
    ....
    
    $this->addField(array(
            'field' => 'input',
            'label' => 'Sexe *',
            'type' => 'radio',
            'required',
            'name' => 'sexe',
            'values' => array(
                'Man' => 'M',
                'Woman' => 'W'
            ),
            'class' => 'inline',
            'value' => 'M'))
        ->addField(array(
            'field' => 'input',
            'type' => 'text',
            'required',
            'pattern' => '^[a-zA-Z0-9]{4,30}$',
            'name' => 'login',
            'label' => 'Login *',
            'help' => 'From 4 to 30 characters (letters and digits only)',
            'placeholder' => 'Login'))
            
    ....
}
```

Once all your fields are created, you can add the submit button with the MayoForm::addSubmit() method (you can also use the MayoForm::addField() method if you want to personnalize the button with some attributes).

``` php
class Form_Example extends MayoForm
{
    ....
    
    $this->addSubmit();
}
```

Your form is now complete, we will now see how to print and validate it. You can check the Form_Example class included in this repository to see some other field examples.

<h2>Form manipulation</h2>

Now that your Form class is complete, you can print & validate the form using the following methods.

``` php
$form = new Form_Example();
$showForm = TRUE;

if($form->hasBeenSent()) {
    if($form->validate($_POST)) {
        echo 'Form sent : Sexe = '.$form->getValue('sexe').' - Login = '.$form->getValue('login');
        $showForm = FALSE;
    }
}

echo $form;
```

<h1>Fields options</h1>

You can add any option you desire, they will be considered and displayed like field's attributes (such as name, value, ...).
However, some options of the options listed below (the bold ones) are interpreted by MayoForm and are not displayed in the HTML code (those .

Keyword marked by * are required.

<h2>Common keyword</h2>

- <strong>field *</strong> : String - The type of the field. Can be 'input', 'submit', 'textarea' or 'select'
- <strong>label *</strong> : String - The label of the field
- name * : String - The name attribute of the field
- <strong>help</strong> : String - A description of what is expected in the field
- <strong>equals</strong> : String - The name attribute of the field which must be equal to this field

<h2>Input keyword</h2>

- type * : The type attribute of the input
- allowed : Array of String - Extensions allowed if the field is an input[file] (currently support .gif, .jpeg, .png)
