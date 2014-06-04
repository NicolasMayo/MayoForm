MayoForm
========

PHP Class to easily generate &amp; validate HTML Forms

<h1>Installation</h1>

To install, just copy the <strong>MayoForm.php</strong> file somewhere in your project (you can rename the file and/or the class to match
an eventual autoloader).

You will also need all the constant defined in the file <strong>Form/Template/bootstrap.php</strong>. You can either copy-paste them or
include the file directly (putting them in a file allows you to have several templates and choose the one you want to
load on each page).

Note that the default template uses bootstrap, so you can load bootstrap CSS and have pretty forms without writing any
CSS code.

<h1>Usage</h1>

To explain how to use this class, I will explain the included Form_Example file (you can directly look at the files, you
should be able to understand how to use it yourself).

To create a form, you have to create a new class which extends the MayoForm class
<pre><code>```php
class Form_Example extends MayoForm
{
    ...
}
```</code></pre>
