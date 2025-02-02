<?php


$used = [];


function component(string $name, array $values=[], bool $echo=true, bool $fill=true, array $parents=[]) {

    global $used;

    $ROOT = __DIR__;

    $html_file_path = "{$ROOT}/component-{$name}/{$name}.html";
    $css_file_path = "{$ROOT}/component-{$name}/{$name}.css";

    if (!file_exists($html_file_path) || !file_exists($css_file_path)) {
        return "";
    }

    $html = file_get_contents($html_file_path);

    // Add stylesheet

    if (!isset($used[$name])) {
        $html = "<link rel='stylesheet' href='styles/build/{$name}.css'>" . $html;
        $used[$name] = true;
    }

    // Default components

    $ptrn = "/<component-([-a-zA-Z0-9]+)><\/component-\\1>/";
    preg_match_all($ptrn, $html, $matches);

    for ($i = 0; $i < count($matches[0]); $i ++) {
        if (!isset($parents[$matches[1][$i]])) {
            $parents[$name] = true;
            $html = str_replace($matches[0][$i], _component(name: $matches[1][$i], fill: false, parents: $parents), $html);
        } else {
            $html = str_replace($matches[0][$i], "<!-- Component '{$matches[1][$i]}' blocked due to infinite recursion -->", $html);
        }
    }

    // Looped components

    preg_match_all("/@!{([a-zA-Z0-9]+)}([\s\S]*?)!@/", $html, $looped_section_matches);

    // Component -> Looped Section -> Looped Element
    
    for ($looped_section_index = 0; $looped_section_index < count($looped_section_matches[1]); $looped_section_index++) {
    
        // Check if there are values present for the looped component
    
        $val_to_count = $values[$looped_section_matches[1][$looped_section_index]] ?? [];
    
        // Check its an array that we can loop through and not just some constant
    
        if (!is_array($val_to_count)) {
            $val_to_count = [];
        }
    
        $num_sections = count($val_to_count);
    
        $looped_section_name = $looped_section_matches[1][$looped_section_index];
        $looped_sections_hydrated = "";
    
        // Get the looped elements of the section (looped section without @!{rows} ... !@)
    
        for ($i = 0; $i < $num_sections; $i++) {
            $looped_elements[] = $looped_section_matches[2][$looped_section_index];
        }
    
        for ($looped_element_index = 0; $looped_element_index < $num_sections; $looped_element_index++) {
    
            // Get all the values in the looped element
    
            preg_match_all("/@[^@]*@/", $looped_elements[$looped_element_index], $looped_element_matches);
    
            // Hydrate the looped element values
    
            if ($fill) {
    
                foreach ($looped_element_matches[0] as $val) {
    
                    $val = trim($val, "@");
        
                    // Do we have a value to replace the placeholder
        
                    if (isset($values[$looped_section_name][$looped_element_index][$val])) {
        
                        // Replace the placeholder in the looped element
        
                        $hydration_value = $values[$looped_section_name][$looped_element_index][$val];
        
                        $hydration_value = str_replace("@", "{_temp_at_sign}", $hydration_value);
        
                        $looped_elements[$looped_element_index] = str_replace(
                            "@{$val}@",
                            $hydration_value,
                            $looped_elements[$looped_element_index]
                        );
        
                    }
        
                }
    
            }
    
            $looped_sections_hydrated .= $looped_elements[$looped_element_index];
    
        }
    
        $html = str_replace($looped_section_matches[0][$looped_section_index], $looped_sections_hydrated, $html);
    
    }

    // Values

    if ($fill) {
        preg_match_all("/@[^@]*@/", $html, $matches);
        foreach ($matches[0] as $val) {
            $val = trim($val, "@");
            $html = str_replace("@{$val}@", $values[$val] ?? "", $html);
        }
    }

    $html = str_replace("{_temp_at_sign}", "@", $html);

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }

}


function _component(string $name, array $values=[], bool $fill=true, array $parents=[]) {
    return component(
        name: $name,
        values: $values,
        echo: false,
        fill: $fill,
        parents: $parents
    );
}


function group(array $components) {

    $html = "";

    if (isset($components)) {

        foreach ($components as $component) {
            $html = $html . $component;
        }

    }

    return $html;

}


?>