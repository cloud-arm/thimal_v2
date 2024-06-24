<?php

function input($name, $type = 'text', $value = '', $id = null, $width = '4', $label = null)
{
    // Use the name as id if id is not provided
    if ($id === null) {
        $id = $name;
    }

    if ($label === null) {
        $label = $name;
    }

    // Create the input element as a string
    $inputElement = sprintf(
        '<div class="col-md-%s"> 
        <div class="form-group"> 
        <label>%s</label>
         <input type="%s" name="%s" value="%s" id="%s" class="form-control">
         </div></div>',
        htmlspecialchars($width, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($id, ENT_QUOTES, 'UTF-8')
    );

    // Return the input element string
    return $inputElement;
}

function selector($table, $where, $name, $values, $id = null, $width = '4', $label = null, $onchange = '', $class = '')
{

    // Use the name as id if id is not provided
    if ($id === null) {
        $id = $name;
    }

    if ($label === null) {
        $label = $name;
    }

    $selectorElement = sprintf(
        '<div class="col-md-%s"> 
        <div class="form-group"> 
        <label>%s</label>
        <select class="form-control select2 %s" name="%s" id="%s" style="width: 100%;" onchange="%s">',
        htmlspecialchars($width, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($onchange, ENT_QUOTES, 'UTF-8')
    );

    $data = select($table, '*', $where);

    foreach ($data as $row) {
        $selectorElement .= sprintf(
            '<option value="%s" > %s </option>',
            $row[$values[0]],
            $row[$values[1]]
        );
    }
    $selectorElement .= '</select></div></div>';

    return $selectorElement;
}




function form($action, $method, $data)
{
    $form_output = sprintf(
        '<form method="%s" action="%s"> <div class="row">' . $data . '
    <div class="col-md-2">
    <div class="form-group">
    <input type="submit" value="Save" class="btn btn-success" >
    </div> </div>
    </div></form>',
        htmlspecialchars($method, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($action, ENT_QUOTES, 'UTF-8')
    );

    return $form_output;
}
