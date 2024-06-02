import os
import shutil


if __name__ == "__main__":

    comps_dir = "components/"

    for file in os.listdir(comps_dir):
        if "component-" in file:
            for comp_file in os.listdir(f"{comps_dir}{file}"):
                if ".css" in comp_file:
                    # print(f"{comps_dir}{file}/{comp_file}")
                    shutil.copy(f"{comps_dir}{file}/{comp_file}", "public/styles/build")