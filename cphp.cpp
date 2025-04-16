#include <iostream>
#include <string>
#include <filesystem>
namespace fs = std::filesystem;
using namespace std;


int build() {

    string path = "./components";

    for (const auto &file : fs::directory_iterator(path)) {
        cout << file.path() << endl;
    }

    return 0;
}


int server() {
    cout << "Starting server" << endl;
    system("cd public && php -S localhost:8000 -c php.ini");
    return 0;
}


int main(int argc, char *argv[]) {

    if (argc == 1) {
        cout << "Requires arguments, --help for more info" << endl;
        return 0;
    }

    if (argc == 2) {

        for (int i = 0; i < strlen(argv[1]); i++) {
            argv[1][i] = tolower(argv[1][i]);
        }  

        if (strcmp(argv[1], "server") == 0) {
            return server();
        }

        if (strcmp(argv[1], "build") == 0) {
            build();
        }

    }

    return 0;
}