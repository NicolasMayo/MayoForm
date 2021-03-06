<?php

/**
 * Class MayoForm
 * @author MaYo
 */
abstract class MayoForm
{
    private $attributes = array(
        'method' => 'POST',
        'action' => ''
    );
    private $fields = array();
    private $errors = array();
    private $submit = '';
    private $equals = array();
    private $template;

    protected $submitName = 'send';

    public function __construct($attributes, $template = 'bootstrap')
    {
        foreach ($attributes as $key => $val) {
            $this->attributes[$key] = $val;
        }
        $this->template = $template;
    }

    public function addField($attributes, $template = '')
    {
        if(empty($template)) {
            $template = $this->template;
        }
        if (isset($attributes['equals'])) {
            $this->equals[$attributes['name']] = $attributes['equals'];
            unset($attributes['equals']);
        }
        if ($attributes instanceof Field) {
            $this->fields[$attributes['name']] = $attributes;
        } else {
            $this->fields[$attributes['name']] = new Field($attributes, $template);
        }
        return $this;
    }

    public function addSubmit()
    {
        $this->submit = $this->submitName;
        return $this;
    }

    public function validate($form)
    {
        foreach ($this->fields as $key => $field) {
            if (isset($form[$key])) {
                $valid = $field->validate($form[$key]);
            } else $valid = $field->validate('');
            if (!$valid) {
                array_push($this->errors, $field->getError());
            }
        }
        foreach ($this->equals as $field1 => $field2) {
            if ($this->fields[$field1]->getValue() != $this->fields[$field2]->getValue()) {
                $this->fields[$field1]->setIsError();
                array_push($this->errors, $this->fields[$field1]->getError());
            }
        }
        if (!empty($this->errors)) return false;
        return true;
    }

    public function populate($values)
    {
        foreach ($values as $name => $value) {
            isset($this->fields[$name]) ? $this->fields[$name]->set($value) : '';
        }
    }

    private function printAttributes($attributes)
    {
        $string = "";
        foreach ($attributes as $key => $val) {
            if (is_int($key)) {
                $string .= $val . ' ';
            } else {
                $string .= $key . '="' . $val . '" ';
            }
        }
        return $string;
    }

    public function field($name)
    {
        return $this->fields[$name];
    }

    public function __toString()
    {
        $string = '<form ';
        $string .= $this->printAttributes($this->attributes) . '>' . PHP_EOL;
        foreach ($this->fields as $field) {
            $string .= '    ' . $field . PHP_EOL;
        }
        if (!empty($this->submit)) {
            $string .= '    <div class="control-group">';
            $string .= '<div class="controls">';
            $string .= '<input class="btn btn-primary" value="Envoyer" type="submit" name="' . $this->submit . '" />';
            $string .= '<input class="btn" type="reset" value="Réinitialiser" />';
            $string .= '</div></div>' . PHP_EOL;
        }
        $string .= '</form>';

        return $string;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setIsError($field)
    {
        return isset($this->fields[$field]) ? $this->fields[$field]->setIsError() : FALSE;
    }

    public function getValue($field)
    {
        return isset($this->fields[$field]) ? $this->fields[$field]->getValue() : NULL;
    }

    public function hasBeenSent()
    {
        if(strtoupper($this->attributes['method']) === 'POST') {
            return isset($_POST[$this->submitName]);
        }
        return isset($_GET[$this->submitName]);
    }
}

/**
 * Class Field
 * @author MaYo
 */
class Field
{
    private $attributes = array('value' => '');
    private $options = array();
    private $error_msg = '';
    private $help = '';
    private $required = false;
    private $error = false;
    private $label = '';
    private $allowed = '';
    private $max_size = 0;

    public function __construct($attributes, $template)
    {
        $specialKeys = array('options', 'error_msg', 'help', 'label', 'max_size', 'allowed');

        // Default values
        $this->error_msg = 'Champ ' . $attributes['name'] . ' incorrect';

        foreach ($attributes as $key => $val) {
            if(in_array($key, $specialKeys, TRUE)) {
                if($key === 'options') {
                    foreach ($attributes['options'] as $option) {
                        $this->options[$option['value']] = $option;
                    }
                } else {
                    $this->{$key} = $val;
                }
            } else if (is_int($key)) {
                $this->attributes[$val] = '';
            } else {
                $this->attributes[$key] = $val;
            }
        }

        if (isset($this->attributes['required']) && isset($attributes['type']) && ($attributes['type'] == 'radio' || $attributes['type'] == 'checkbox')) {
            $this->required = true;
            unset($this->attributes['required']);
        }

        require_once 'Form' . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR . $template . '.php';
    }

    public function __toString()
    {
        $string = '';
        $attributes = $this->attributes;
        $field = $attributes['field'];
        unset($attributes['field']);

        if ($field == 'input') {
            if ($attributes['type'] == 'radio' || $attributes['type'] == 'checkbox') {
                $values = $attributes['values'];
                unset($attributes['values']);
                if (!isset($attributes['class'])) $class = $attributes['type'] . ' ';
                else {
                    $class = $attributes['type'] . ' ' . $attributes['class'];
                    unset($attributes['class']);
                }
                foreach ($values as $label => $value) {
                    $tmpString = '<input ';
                    $tmpString .= $this->printAttributes($attributes, false);
                    $tmpString .= 'value="' . htmlspecialchars($value) . '" ';
                    if (in_array($value, (array) $attributes['value'])) {
                        $tmpString .= 'checked="checked" ';
                    }
                    $tmpString .= '/>' . $label;
                    $string .= sprintf(MAYOFORM_RADIO_CHECKBOX_LABEL, $class, $tmpString);
                }
            } else {

                if ($this->attributes['type'] == 'password' && $this->error == 'error') {
                    $printValue = false;
                } else {
                    $printValue = true;
                }
                $string .= '<input ';
                $string .= $this->printAttributes($attributes, $printValue);
                $string .= ' />';
            }
        } else if ($field == 'textarea') {
            $string .= '<textarea ';
            $string .= $this->printAttributes($attributes, false);
            $string .= '>' . htmlspecialchars($attributes['value']) . '</textarea>';
        } else if ($field == 'select') {
            $string .= '<select ';
            $string .= $this->printAttributes($attributes, false);
            $string .= '>';
            foreach ($this->options as $opt) {
                $string .= '<option ';
                if (in_array($opt['value'], (array)$attributes['value'])) {
                    $string .= 'selected="selected" ';
                }
                $optionString = $opt['string'];
                unset($opt['string']);
                $string .= $this->printAttributes($opt) . 'value="' . htmlspecialchars($opt['value']) . '" '. '>';
                $string .= $optionString . '</option>';
            }
            $string .= '</select>';
        }

        if (!empty($this->help)) $string .= sprintf(MAYOFORM_INPUT_HELP, $this->help);
        if (!empty($this->label)) {
            $string = sprintf(MAYOFORM_FIELD_CONTAINER, $string);
            $string = sprintf(MAYOFORM_INPUT_LABEL, $attributes['name'], $this->label) . $string;
        } else $string = sprintf(MAYOFORM_FIELD_CONTAINER, $string);

        return sprintf(MAYOFORM_FIELD_GLOBAL_CONTAINER, $this->error, $string);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function addOption($option)
    {
        $this->options[] = $option;
    }

    public function set($value)
    {
        $this->attributes['value'] = $value;
    }

    private function printAttributes($attributes, $printValue = true)
    {
        $string = "";
        foreach ($attributes as $key => $val) {
            if (!($key == 'value' && !$printValue)) {
                if ($key == 'value') {
                    $string .= 'value="' . htmlspecialchars($val) . '" ';
                } else if ($key == 'name' && isset($this->attributes['type']) && $this->attributes['type'] == 'checkbox') {
                    $string .= 'name="' . $val . '[]" ';
                } else {
                    $string .= $key . '="' . $val . '" ';
                }
            }
        }
        return $string;
    }

    public function validate($value)
    {
        $this->attributes['value'] = $value;
        if ($this->attributes['field'] == 'input') {
            if ($this->attributes['type'] == 'radio' || $this->attributes['type'] == 'checkbox') {
                if ($this->required && empty($value)) {
                    $this->error = 'error';
                    return false;
                }
                if (!empty($value)) {
                    foreach ((array) $value as $val) {
                        if (!in_array($val, array_values($this->attributes['values']))) {
                            $this->error = 'error';
                            return false;
                        } else {
                            $this->error = 'success';
                        }
                    }
                }

            } if ($this->attributes['type'] == 'file') {
                if (isset($this->attributes['required']) && empty($_FILES[$this->attributes['name']]['name'])) {
                    $this->error = 'error';
                    return false;
                }
                if (!empty($_FILES[$this->attributes['name']]['name'])) {
                    $files = $_FILES[$this->attributes['name']];
                    if (!is_uploaded_file($files['tmp_name'])) {
                        $this->error = 'error';
                        return false;
                    }
                    $type = getimagesize($files['tmp_name']);
                    $type = $type['mime'];
                    if ($type == 'image/jpeg' || $type == 'image/pjpeg') $type = 'jpeg';
                    if ($type == 'image/png' || $type == 'image/x-png') $type = 'png';
                    if ($type == 'image/gif') $type = 'gif';
                    $size = filesize($files['tmp_name']);
                    if ($this->max_size != 0 && $size > 0 && $this->max_size < $size) {
                        $this->error = 'error';
                        return false;
                    }
                    if (!empty($this->allowed)) {
                        if (!in_array($type, (array) $this->allowed)) {
                            $this->error = 'error';
                            return false;
                        }
                    }
                }
                $this->error = 'success';

            } else if ($this->attributes['type'] != 'submit') {
                if (!empty($value) && $this->attributes['type'] == 'email') {
                    if (!preg_match('#^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$#', $value)) {
                        $this->error = 'error';
                        return false;
                    }
                } else if (!empty($value) && $this->attributes['type'] == 'date') {
                    $date = DateTime::createFromFormat("d/m/Y", $value);
                    if (!$date) {
                        $this->error = 'error';
                        return false;
                    }
                }
                if (
                    (isset($this->attributes['pattern']) && !preg_match('#'.$this->attributes['pattern'].'#', $value)) ||
                    (isset($this->attributes['required']) && empty($value))
                ) {
                    $this->error = 'error';
                    return false;
                }

                $this->error = 'success';
                $this->attributes['value'] = $value;
            }

        } else if ($this->attributes['field'] == 'textarea') {
            if (isset($this->attributes['pattern']) && !preg_match('#'.$this->attributes['pattern'].'#', $value)) {
                $this->error = 'error';
                return false;
            }
            if (isset($this->attributes['required']) && empty($value)) {
                $this->error = 'error';
                return false;
            }
            $this->error = 'success';
            $this->attributes['value'] = $value;

        } else if ($this->attributes['field'] == 'select') {
            if (isset($this->attributes['required']) && empty($value)) {
                $this->error = 'error';
                return false;
            } else {
                $this->error = 'success';
                $this->attributes['value'] = $value;
            }
            foreach ((array) $value as $val) {
                if (!in_array($val, array_keys($this->options))) {
                    $this->error = 'error';
                    return false;
                } else {
                    $this->error = 'success';
                    $this->attributes['value'] = (array) $this->attributes['value'];
                    $this->attributes['value'][] = $val;
                }
            }
        }
        return true;
    }

    public function getError()
    {
        return $this->error_msg;
    }

    public function getValue()
    {
        return $this->attributes['value'];
    }

    public function setIsError()
    {
        $this->error = 'error';
        return TRUE;
    }
}