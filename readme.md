# Component PHP

Component PHP designed by [@78Sam](https://github.com/78Sam/). View the official site [here](https://component.sam-mccormack.co.uk/).

### Requirements

- PHP 8.2+
- Composer

When opening the project for the first time run:

```
composer install
```

Then to ensure everything has set up correctly run

```
python3 comp.py
```

and enter the command ` server `, this should spin up a localhost:8000 server and you should see the
ComponentPHP homepage.

Note, nothing that you don't want users seeing should be placed inside the public folder.

## Overview

ComponentPHP works by creating views, routes, middleware, components, and database files. All these
elements work together to create your functioning web app. Almost everything that you make will first
be generated using the ` comp.py ` file located in the root directory. I would also recommend you read the
short php files ` components/component.php `, ` database/database.php `, ` public/index.php ` and ` route.php `
to gain a feel for how the framework works.

The commands of the ` comp.py ` file are as follows:
- ` help `: See a list of available commands
- ` server `: Start a localhost:8000 server
- ` gen `: Generate views, components, forms, and middleware
- ` build `: Build all your css files for deployment (note you need to run this to see changes in any css files)
- ` debloat `: Remove the setup of the bundled ComponentPHP webpage
- ` exit `: Exit the program

### Schematics

You are free to change any of the templates used by the ` gen ` command in ` comp.py`. The schematic files are stored under ` components/schematics ` (just be sure not to change the names of the files themselves). Also note that schematics have a single fillable value called 'name' which can be used via the double brace notation: ` {{name}} `, this value will be filled with the name of the generated schematic.

## Components

Components are blocks of HTML and CSS that can be imported into views. Components can contain placeholders in
their templates that can be updated at runtime to your desired values. CSS files are automatically imported into
the view along with their corresponding HTML file. You can also automatically import other components into a
components HTML file.

>components/component-root/root.html
```html
<div class="component-root @_classes@" id="@_id@" style="@_style@">
    <component-nav-bar></component-nav-bar>
    @comps@
</div>
```

Here is an example of a component that is used in the example that comes with ComponentPHP. Here we notice the two
main features. Anything surrounded with @s means that that value can be replaced in the view file. Any HTML tag
beginning with 'component-' means that corresponding component will automatically be brought in when it is rendered.

In this example we automatically import the nav component. We also define a value that can be filled called 'comps' along with some others in the div tag.
It should also be noted that you can set up components to be loopable using a special syntax that I wont go into here.

## Views

Views are the actual pages of your website. Within views we write standard HTML and PHP, as well as import components and interact with our database.

>views/home.php
```php
$sections = [
    _component(
        name: "splash",
        values: [
            "title"=> "ComponentPHP",
            "image"=>"splash2.png",
            "_id"=>"splash-page"
        ]
    ),
];

component(
    name: "root",
    values: ["comps"=>group($sections)]
);
```

Here we see in our home view a few ways to interact with components. First we create an instance of a component called 'splash' using the underscored version of the function. Calling ` _component() ` instead of ` component() ` returns the value as a string instead of immediately displaying it. We then pass the list of one component to the 'root' component we saw earlier using a grouper. ` group() ` takes a list of components and turns it into a single string. This is then passed to the 'comps' placeholder we saw earlier. Finally the root component using the splash component is displayed on the page - remembering that the nav component is also loaded when we use the root component.

## Routing

Routing is the act of redirecting users to desired views via URLs. All the sites routes are managed within the ` public/index.php ` file. Note, you should NOT remove the two comments ` route-placeholder ` and ` routes-placeholder ` as these are used by ` comp.py ` to generate new views.

Whenever you generate a new view, a new route will be automatically filled in ` index.php `, this is where you can define aliases and middlewares that you wish your view to use.

>public/index.php
```php

$home = new Route(
	aliases: ["/", "/home"],
	path: "home.php",
	middleware: []
);

```

This is the route for the homepage of the example site. Here we can see that users can navigate to this view via either the '/' or '/home' paths. The path value is the name of the view file, in this case 'home.php'. In this case we have no middleware for the route.