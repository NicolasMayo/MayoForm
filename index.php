<?php require 'MayoForm.php';
require 'Form/Form_Example.php';
$form = new Form_Example(); ?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/bootstrap.css" />
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
        <title>MayoForm example</title>
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="container">
            <h1>Form</h1>
            <?php
                if($form->hasBeenSent() && $form->validate($_POST)) {
                    echo '<p>Le formulaire a été envoyé !</p>';
                } else {
                    echo $form;
                }
            ?>
        </div>
    </body>
</html>
<?php
