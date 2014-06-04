<?php
class Form_Example extends MayoForm
{
    protected $submitName = 'inscription';

    public function __construct()
    {
        parent::__construct(array(
            'action' => 'index.php',
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'method' => 'POST'
        ));
        $egTimeZone[] = array(
            'value' => 'Europe/Paris',
            'string' => 'Europe/Paris'
        );
        foreach(DateTimeZone::listIdentifiers() as $timeZone) {
            $egTimeZone[] = array(
                'value' => $timeZone,
                'string' => $timeZone
            );
        }

        $this->addField(array(
            'field' => 'input',
            'label' => 'Sexe *',
            'type' => 'radio',
            'required',
            'name' => 'sexe',
            'values' => array(
                'Man' => 'M',
                'Woman' => 'F'
            ),
            'class' => 'inline',
            'value' => 'M'))
            ->addField(array(
                'field' => 'input',
                'type' => 'text',
                'required',
                'pattern' => '^[a-zA-Z0-9]{4,30}$',
                'name' => 'pseudo',
                'label' => 'Pseudo *',
                'help' => 'De 4 à 30 caractères (lettres et chiffres uniquement)',
                'placeholder' => 'Pseudo'))
            ->addField(array(
                'field' => 'input',
                'type' => 'password',
                'required',
                'pattern' => '^.{8,40}$',
                'name' => 'pass',
                'label' => 'Mot de passe *',
                'help' => 'De 8 à 40 caractères',
                'placeholder' => 'Mot de passe'))
            ->addField(array(
                'field' => 'input',
                'type' => 'password',
                'required',
                'equals' => 'pass',
                'pattern' => '^.{8,40}$',
                'name' => 'password2',
                'label' => 'Confirmation *',
                'help' => 'Doit être identique au champ Mot de passe',
                'placeholder' => 'Confirmation'))
            ->addField(array(
                'field' => 'input',
                'type' => 'email',
                'required',
                'name' => 'mail',
                'label' => 'Email *',
                'help' => 'Doit être valide',
                'placeholder' => 'email@domain.com'))
            ->addField(array(
                'field' => 'input',
                'type' => 'date',
                'name' => 'birthDate',
                'label' => 'Date de naissance',
                'class' => 'datepicker',
                'placeholder' => 'JJ/MM/YYYY'))
            ->addField(array(
                'field' => 'select',
                'name' => 'timeZone',
                'options' => $egTimeZone,
                'label' => 'Fuseau horaire',
                'required'
            ))
            ->addField(array(
                'field' => 'input',
                'type' => 'file',
                'name' => 'photo',
                'label' => 'Photo',
                'max_size' => '200000',
                'allowed' => array('gif', 'jpeg', 'png'),
                'help' => 'Format GIF, JPEG et PNG seulement (200 ko maximum)'
            ))
            ->addSubmit();
    }
}