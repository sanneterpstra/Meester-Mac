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
                    product["sku"] = cells[2].get_attribute('innerText')
                    product["ean"] = cells[3].get_attribute('innerText')
                    product["product_memory"] = cells[5].get_attribute('innerText')
                    product["product_grade"] = cells[6].get_attribute('innerText')
                    product["product_storage"] = cells[7].get_attribute('innerText')
                    product["processor"] = cells[8].get_attribute('innerText')
                    product["price"] = cells[9].get_attribute('innerText')
                    product["stock"] = cells[10].get_attribute('innerText')

                    color = Color.from_string(cells[4].find_element(By.CLASS_NAME, "item__color").value_of_css_property('background-color')).hex
                    if color == "#959a9e":
                        product["product_color"] = "Space Gray"
                    if color == "#e4e4e2":
                        product["product_color"] = "Silver"
                    if color == "#dfccb7":
                        product["product_color"] = "Gold"

                    file.append(product)

file = []
fileStoragePath = sys.argv[1]
driver = setupBrowser()

login(driver)
get_data("https://www.forza-refurbished.nl/reseller/macbook")

os.makedirs(os.path.dirname(fileStoragePath), exist_ok=True)
with open(fileStoragePath, 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=4)
driver.quit()

print(fileStoragePath + " created")
