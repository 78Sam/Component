import sys
import os


def main() -> None:

    if len(sys.argv) < 2:
        print("Type and Name required")
        return
    
    gen_type = sys.argv[1].lower()
    name = sys.argv[2]

    match gen_type:

        case "view":

            schematic = ""
            with open("components/schematics/view.txt") as schema:
                schematic = schema.read()

            schematic = schematic.replace("{{name}}", name)

            with open(f"views/{name}.php", "x") as view:
                view.write(schematic)

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

        # YOUR SCHEMATICS

        case _:
            print(f"Unknown generator type: '{gen_type}', expected: 'component', 'form' or 'view'")
    

if __name__ == "__main__":
    main()
