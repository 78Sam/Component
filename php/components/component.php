<?php


function defaultRewriter(string $html) {

    $pattern = "/<component-([-a-zA-Z0-9]+)><\/component-([-a-zA-Z0-9]+)>/";
    preg_match_all($pattern, $html, $default_components);

    foreach ($default_components[0] as $default_component) {

        $name_pattern = "/(-[a-zA-Z0-9]+>)/";
        preg_match($name_pattern, $default_component, $component_name);
        $component_name = $component_name[0];
        $component_name = substr($component_name, 1, strlen($component_name)-2);

        $component = _component(name: $component_name);
        $replacements = 1;

        $html = str_replace($default_component, $component, $html, count: $replacements);
    }

    return $html;

}


function valueRewriter(string $html, array $values) {

    foreach ($values as $key => $value) {
        $html = str_replace("<!-- " . $key . " -->", $value, $html);
    }

    return $html;

}


function attributeRewriter(string $html, array $attributes) {

    $attribute_prefixes = [
        "custom-id"=>"id='%s'",
        "custom-style"=>"style='%s'",
        "additional-classes"=>"%s"
    ];

    foreach($attribute_prefixes as $key => $value) {
        if ($attributes[$key]) {
            $prefixed_value = sprintf($value, $attributes[$key]);
            $html = str_replace("{" . $key . "}", $prefixed_value, $html);
        } else {
            $html = str_replace(" {" . $key . "}", "", $html);
        }
    }

    // foreach ($attributes as $key => $value) {
    //     $prefixed_value = sprintf($attribute_prefixes[$key], $value);
    //     $html = str_replace("{" . $key . "}", $prefixed_value, $html);
    // }

    return $html;

}


$count = 0;
$used_components = [];


/**
 * Base component function
 * 
 * @param string $name The name of the component (must match component folder name)
 * @param array $attributes Default null. Attributes such as 'additional-classes', 'custom-id', and 'custom-style'
 * @param array $values Default null. Replaces HTML comments with values, e.g. ["value"=>"<p>Hello</p>"]
 * 
 */
function component(string $name, array $attributes = null, array $values = null, bool $echo = true) {

    global $attribute_prefixes;
    global $count;
    global $used_components;

    $path = __DIR__ . "/component-" . $name;

    $html_file = $path . "/" . $name . ".html";
    $css_file = "php/components/component-" . $name . "/" . $name . ".css";

    $standard_error_html = __DIR__ . "/component-standard-load-error/standard-load-error.html";
    $standard_error_css = "php/components/component-standard-load-error/standard-load-error.css";

    // Check components exist

    if (file_exists($html_file) and file_exists($css_file)) { // Files ok

        $html = file_get_contents($html_file);

    } else if (file_exists($standard_error_html) and file_exists($standard_error_css)) { // Something missing

        $html = file_get_contents($standard_error_html);
        $css_file = $standard_error_css;
        $attributes = [];
        $values = ["component-name"=>$name];

    } else { // All missing

        return "";

    }

    // Add Stylesheet

    if (!isset($used_components[$name])) {
        $html = '<link rel="stylesheet" href="' . $css_file . '">' . $html;
        $used_components[$name] = 1; // Not sure I need to actually set a value
    }

    // Get all the default components

    $html = defaultRewriter(html: $html);

    // Add attributes

    if ($attributes) {
        $html = attributeRewriter(html: $html, attributes: $attributes);
    } else {
        $html = attributeRewriter(html: $html, attributes: []);
    }

    // Add values

    if ($values) {
        $html = valueRewriter(html: $html, values: $values);
    }

    // Debugging

    // $html = $html . "<!-- component " . $name . " " . $count .  " -->";
    $count = $count + 1;

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }

}


function _component(string $name, array $attributes = null, array $values = null) {
    return component(
        name: $name,
        attributes: $attributes,
        values: $values,
        echo: false
    );
}


function group(array $components = null) {

    $html = "";

    if ($components) {

        foreach ($components as $component) {
            $html = $html . $component;
        }

    }

    return $html;

}

?>