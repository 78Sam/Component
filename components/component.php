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

    preg_match_all("/@!{([a-zA-Z0-9]+)}([\s\S]*?)!@/", $html, $loop_element_matches);

    $looped_elements = [];
    for ($i = 0; $i < count($loop_element_matches[1]); $i++) {

        $looped_elements[] = str_repeat($loop_element_matches[2][$i], count($values[$loop_element_matches[1][$i]]));
        preg_match_all("/{i}/", $looped_elements[$i], $index_matches);

        $count = 1;
        foreach ($index_matches[0] as $counter) {
            $index = strpos($looped_elements[$i], $counter);
            if ($index !== false) {
                $looped_elements[$i] = substr_replace($looped_elements[$i], $count, $index, strlen($counter));
            }
            $count += 1;
        }

        $html = str_replace($loop_element_matches[0][$i], $looped_elements[$i], $html);

    }

    $adds = [];
    foreach ($values as $val) {
        if (is_array($val)) {
            $i = 1;
            foreach ($val as $section) {
                foreach ($section as $key => $value) {
                    $adds["{$key}-{$i}"] = $value;
                    $i++;
                }
            }
        }
    }

    $values = array_merge($values, $adds);

    // Values

    if ($fill) {
        preg_match_all("/@[^@]*@/", $html, $matches);
        foreach ($matches[0] as $val) {
            $val = trim($val, "@");
            $html = str_replace("@{$val}@", $values[$val] ?? "", $html);
        }
    }

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