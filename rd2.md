# Component PHP

Component PHP designed by [@78Sam](https://github.com/78Sam/). View the official site [here](https://sam-mccormack.co.uk/phpsite).

## Getting Started

Install PHP and Python on your device.

### Create a Component

Create a basic component
```
python create_component.py name-of-component
```

After you create your first component you should see a new folder appear under php/components, from there, you should see two new files, one a HTML file and one a CSS file.

Lets try creating a new component 'main'. I'm doing this on MAC, so I'll use 'python3'

```
python3 create_component.py main
```

I now have files 'main.html' and 'main.css' in the 'php/components/component-main' folder.

>main.html
```HTML
<div class="component-main {additional-classes}" {custom-id} {custom-style}>
	<!-- value -->
</div>
```

>main.css
```CSS
.component-main {

}
```

### Attributes, Values, and Default Values

From here we can style our component, and add any additional html we want inside it. Note the '{additional-classes}' '{custom-id}' and '{custom-style}' attributes. These can be called from our rendered PHP file, so lets leave them there.

HTML component files can also reference other components as default, say we had another component called 'section' we could add it as default to our main component as follows. Now any time you render the 'main' component, it will render the 'section' component too.

>main.html
```HTML
<div class="component-main {additional-classes}" {custom-id} {custom-style}>
	<!-- value -->
    <component-section></component-section>
</div>
```

Components also can have values, here we see that the value is called 'value' by default, but we can have any number of values and names for them.

Heres a simple example of how we could alter our 'main' component and render it.

>main.html
```HTML
<div class="component-main {additional-classes}" {custom-id} {custom-style}>
	<h1>
        <!-- main-heading -->
    </h1>
    <h3>
        <!-- subbyheading -->
    </h3>
    <p>
        <!-- para -->
    </p>
    <div>
        <!-- extra stuff -->
    </div>
</div>
```

>index.php
```PHP
<?php

    component(
        name: "main",
        attributes: ["custom-style"=>"color: red; background-color: black;"],
        values: [
            "main-heading"=>"Hello",
            "subbyheading"=>"World",
            "para"=>"First Component"
        ]
    );

?>
```

### Groups and Components as Parameters

It should also be noted that components can be passed as values to other components, you just need to add an _ to the start of component() to get a return value that can be passed as a string. For example.

>index.php
```PHP
<?php

    $comp = _component(
        name: "main",
        attributes: ["custom-style"=>"color: red; background-color: blue;"],
        values: [
            "main-heading"=>"Hello",
            "subbyheading"=>"World",
            "para"=>"First Component"
        ]
    );

    component(
        name: "main",
        attributes: ["custom-style"=>"color: red; background-color: black;"],
        values: [
            "main-heading"=>"Hello",
            "subbyheading"=>"World",
            "extra stuff"=>$comp
        ]
    );

?>
```

Also have a look at the group() function to pass multiple components to one value.

### Errors

If a component (its html or css files) are unable to be found, a default component will be inserted in its place. You can find this component in the folder php/components/component-standard-load-error. Feel free to alter or remove this component at will.

### DIR

The final PHP file of note is the file dir.php. This file is used to store all the locations, imports, and links for your site. Feel free to use other methods but using the dir file could help organise and refactor your site when needed. You will notice that index.php automatically imports php/components/component.php using dir.php.

### Local Hosting

Start a localhost server

```
python start_server.py
```

