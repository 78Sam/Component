import os


def main():
    os.chdir("public/")
    os.system("php -S localhost:8000")


if __name__ == "__main__":
    main()
