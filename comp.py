from sys import argv
import os
import shutil


def clear() -> None:
    os.system('cls' if os.name == 'nt' else 'clear')


def build() -> None:

    comps_dir = "components/"

    for file in os.listdir(comps_dir):
        if "component-" in file:
            for comp_file in os.listdir(f"{comps_dir}{file}"):
                if ".css" in comp_file:
                    print(f"Building file: {comp_file}")
                    shutil.copy(f"{comps_dir}{file}/{comp_file}", "public/styles/build")

    print("Build complete.")


def generate() -> None:

    gen_type = input("Enter a generator type (view, component, form, middleware): ").lower()
    name = input(f"Enter a name for the {gen_type}: ")

    match gen_type:

        case "view":

            schematic = ""
            with open("components/schematics/view.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"views/{name}.php", "x") as view:
                view.write(schematic)

            with open("public/index.php", "r") as file:
                data = file.read()

            route = f'${name} = new Route(\n\taliases: ["/{name}"],\n\tpath: "{name}.php",\n\tmiddleware: []\n);\n\n// route-placeholder'

            data = data.replace("// route-placeholder", route)
            data = data.replace("// routes-placeholder", f"${name},\n\t// routes-placeholder")

            with open("public/index.php", "w") as file:
                file.write(data)

        case "component":

            os.mkdir(f"components/component-{name}")

            # HTML

            schematic = ""
            with open("components/schematics/component.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"components/component-{name}/{name}.html", "x") as component_html:
                component_html.write(schematic)

            # CSS

            schematic = ""
            with open("components/schematics/component_css.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"components/component-{name}/{name}.css", "x") as component_css:
                component_css.write(schematic)

        case "form":

            os.mkdir(f"components/component-{name}")

            # FORM

            schematic = ""
            with open("components/schematics/form.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"components/component-{name}/{name}.html", "x") as component_html:
                component_html.write(schematic)

            # CSS

            schematic = ""
            with open("components/schematics/component_css.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"components/component-{name}/{name}.css", "x") as component_css:
                component_css.write(schematic)

        case "middleware":

            schematic = ""
            with open("components/schematics/middleware.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"middleware/{name}.php", "x") as view:
                view.write(schematic)


        # YOUR SCHEMATICS

        case _:
            print(f"Unknown generator type: '{gen_type}', expected: 'component', 'form', 'view' or 'middleware'. Generation failed.")


def deBloat() -> None:

    bloat = [
        # Assets
        "public/assets/favi.png",
        "public/assets/images/splash.png",
        "public/assets/fonts/Bely-Display-W100-Regular.ttf",
        "public/assets/fonts/Karla-VariableFont_wght.ttf",
        "public/assets/fonts/Scary.ttf",
        # JS
        "public/js/nav.js",
        # CSS
        "public/styles/build/nav-bar.css",
        "public/styles/build/root.css",
        "public/styles/build/splash.css",
        # Views
        "views/home.php",
        # Components
        "components/component-nav-bar",
        "components/component-root",
        "components/component-splash",
    ]

    files = "".join(["" + file + "\n" for file in bloat])

    confirm = input(f"You are attempting to delete files:\n\n{files}\nFor {os.getcwd()}\nType 'confirm'")
    while confirm != "confirm":
        confirm = input("Please enter 'confirm' if you wish to debloat")

    for file in bloat:
        if os.path.isdir(file):
            shutil.rmtree(file)
        elif os.path.isfile(file):
            os.remove(file)

    # TODO: Replace public/index.php with fresh page
    # TODO: Remove all middlewares?

    return


def parseCMD(cmd: str) -> None:

    clear()
    msg = "Enter a command or type 'help' to view a list of all commands: "
    match cmd.lower():

        case "help":
            print("server: Start a localhost server\nbuild: Build files for deployment\ngen: Generate components, views, etc.\ndebloat: Remove pre-made files\nexit: quit")

        case "server":
            os.chdir("public/")
            os.system("php -S localhost:8000 -c ../php.ini")

        case "build":
            build()

        case "gen":
            generate()

        case "debloat":
            deBloat()

        case "exit":
            exit()

        case _:
            msg = f"Unknown command '{cmd}'. Enter a command or type 'help' to view a list of all commands: "

    cmd = input(msg)
    parseCMD(cmd)


def main() -> None:

    if len(argv) < 2:
        clear()
        cmd = input("Enter a command or type 'help' to view a list of all commands: ")
    else:
        cmd = argv[1]

    parseCMD(cmd)


if __name__ == "__main__":
    main()
