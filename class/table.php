<?php

function table($id, $data, $table, $where = "")
{
    // Start the table and add the id attribute
    $html = sprintf('<table id="%s" class="table table-bordered table-striped">', $id);

    // Add the table header
    $html .= '<thead><tr>';
    foreach ($data as  $key => $value) {
        $html .= sprintf('<th>%s</th>', $key);
    }
    $html .= '</tr></thead>';

    // if(strpos($val,"@")>0){
    $column = '';
    foreach ($data as  $key => $value) {

        $valueR = explode('@', $value);

        if ($key == '#') {
            $valueS = explode('@', $value);
            if (count($valueS) > 1) {
                foreach ($valueS as $valS) {
                    $valueB = explode('#', $valS);
                    $column .= $valueB[1] . ',';
                }
            } else {
                $valueB = explode('#', $value);
                $column .= $valueB[1] . ',';
            }
        } else {
            $column .= $valueR[0] . ',';
        }
    }

    if (substr($column, -1) == ',') {
        $column = substr($column, 0, -1);
    }

    $t_values = select($table, $column, $where);

    // Add the table body
    $html .= '<tbody>';
    foreach ($t_values as $cell) {
        $html .= sprintf('<tr id="%s">', reset($cell));
        foreach ($data as  $key => $value) {

            if ($key == '#') {
                $valueS = explode('@', $value);
                if (count($valueS) > 1) {
                    foreach ($valueS as $valS) {
                        $valueB = explode('#', $valS);
                        $row = $valueB[1];
                    }
                } else {
                    $valueB = explode('#', $value);
                    $row = $valueB[1];
                }
            } else {
                $valueR = explode('@', $value);
                $row = $valueR[0];
            }

            $valueC = explode('%',$value);

            $x_row = explode(',', $row);
            $txt = $cell[$x_row[0]];

            $td_data = '';


            if (isset($valueR[1]) && $valueR[1] == 'font_txt') {
                $txt = $valueR[2] . $cell[$row];
            }

            if (isset($valueR[1]) && $valueR[1] == 'back_txt') {
                $txt = $cell[$row] . $valueR[2];
            }

            if (isset($valueR[1]) && $valueR[1] == 'middle_txt') {
                $txt = $valueR[2] . $cell[$row] . $valueR[3];
            }

            if (isset($valueR[1]) && $valueR[1] == 'back_value_tag') {
                $x_row = explode(',', $row);
                $txt = $cell[$x_row[0]] . $valueR[2] . $cell[$valueR[4]] . $valueR[3];
            }

            if (isset($valueR[1]) && $valueR[1] == 'font_value_tag') {
                $x_row = explode(',', $row);
                $txt = $valueR[2] . $cell[$valueR[4]] . $valueR[3] . $cell[$x_row[0]];
            }

            if ($key == '#') {
                $valueS = explode('@', $value);
                if (count($valueS) > 1) {
                    $td_data .= ' style="display:flex;gap:5px;"';
                    $txt = '';
                    foreach ($valueS as $valS) {
                        $valueB = explode('#', $valS);
                        $txt .= $valueB[0] . $cell[$row] . $valueB[2];
                    }
                } else {
                    $valueB = explode('#', $value);
                    $txt = $valueB[0] . $cell[$row] . $valueB[2];
                }
            }

            if (isset($valueC[0]) && $valueC[0] == 'condition') {

                if($cell[$valueC[1]].$valueC[2]){
                    $txt='';
                }else{
                }
            }

            $html .= sprintf('<td %s>%s</td>', $td_data, $txt);
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    //table foot
    // $html .= '<tfoot><tr>';
    // foreach ($data as  $key => $value) {
    //     $html .= sprintf('<th>%s</th>', $key = '');
    // }
    // $html .= '</tr></tfoot>';

    // Close the table tag
    $html .= '</table>';

    // Return the constructed HTML string
    return $html;
}
