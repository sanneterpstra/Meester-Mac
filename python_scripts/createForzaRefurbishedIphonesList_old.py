from general import setupBrowser
from forzarefurbished import login
from forzarefurbished import navigate
from selenium.webdriver.common.by import By
from selenium.webdriver.support.color import Color
import json
import time
import sys
import os

def get_data(url):
    navigate(driver, url)
    # Wait for table to be loaded completely
    driver.implicitly_wait(5)

    scrape_data()

def scrape_data():
    print("Scraping data")
    mytable = driver.find_element(By.ID, "products")
    tableBodies = mytable.find_elements(By.TAG_NAME, 'tbody');
    for tableBody in tableBodies:
        if tableBody.is_displayed():
            for row in tableBody.find_elements(By.TAG_NAME, "tr"):
                product = {}
                cells = row.find_elements(By.TAG_NAME, "td");
                if cells:
                    product["product_name"] = cells[1].get_attribute('innerText')
                    for cell in cells:
                        if cell.get_attribute("style") == "display: none":
                            continue
                        if cell.get_attribute("data-th") == "sku":
                            product["sku"] = cell.get_attribute('innerText')
                        if cell.get_attribute("data-th") == "quality":
                            product["product_grade"] = cell.get_attribute('innerText')
                        if cell.get_attribute("data-th") == "color":
                            div = cell.find_element(By.CLASS_NAME, "item__color")
                            color = Color.from_string(div.value_of_css_property('background-color')).hex

                            if color == "#959a9e":
                                product["product_color"] = "Spacegrijs"
                            if color == "#e4e4e2":
                                product["product_color"] = "Zilver"
                            if color == "#dfccb7":
                                product["product_color"] = "Goud"

                            if color == "#1f2020":
                                product["product_color"] = "Zwart"
                            if color == "#ffffff":
                                product["product_color"] = "Wit"
                            if color == "#a5282c":
                                product["product_color"] = "Rood"

                            if color == "#ee7762":
                                product["product_color"] = "Koraal"
                            if color == "#249166":
                                product["product_color"] = "Groen"
                            if color == "#4e5851":
                                product["product_color"] = "Middernacht groen"
                            if color == "#f3d160":
                                product["product_color"] = "Geel"
                            if color == "#5eafe5":
                                product["product_color"] = "Blauw"
                            if color == "#fac7c4":
                                product["product_color"] = "Roze"
                            if color == "#b8afe6":
                                product["product_color"] = "Paars"
                        if cell.get_attribute("data-th") == "ean":
                            product["ean"] = cell.get_attribute('innerText')
                        if cell.get_attribute('data-th') == 'memory':
                            product["product_storage"] = cell.get_attribute('innerText')
                        if cell.get_attribute("data-th") == "retail_price":
                            price = cell.find_element(By.CLASS_NAME, 'excl')
                            product["price"] = price.get_attribute('innerText')
                        if cell.get_attribute("data-th") == "stock":
                            product["stock"] = cell.get_attribute('innerText')

                    file.append(product)

file = []
fileStoragePath = sys.argv[1]
driver = setupBrowser()

login(driver)
get_data("https://www.forza-refurbished.nl/reseller/iphone")

os.makedirs(os.path.dirname(fileStoragePath), exist_ok=True)
with open(fileStoragePath, 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=4)
driver.quit()

print(fileStoragePath + " created")
