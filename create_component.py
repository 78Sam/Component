import sys
import os


def main() -> None:

    if len(sys.argv) < 2:
        print("Name required")
        return
    
    name = sys.argv[1]

    os.mkdir(f"php/components/component-{name}")

    if "form" in name.lower():
        component = f'<form action="php/actions/{{action}}" method="{{method}}" class="component-{name} {{additional-classes}}" {{custom-id}} {{custom-style}}>\n\t<!-- inputs -->\n\t<input type="submit" value="<!-- submit-name -->">\n</form>'
    else:
        component = f'<div class="component-{name} {{additional-classes}}" {{custom-id}} {{custom-style}}>\n\t<!-- value -->\n</div>'

    with open(f"php/components/component-{name}/{name}.html", "x") as file:
        file.write(component)

    with open(f"php/components/component-{name}/{name}.css", "x") as file:
        file.write(f".component-{name} {{\n\n}}")
    

if __name__ == "__main__":
    main()
