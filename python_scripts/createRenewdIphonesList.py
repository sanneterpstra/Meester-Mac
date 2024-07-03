from general import setupBrowser
from renewd import login
from renewd import navigate
from selenium.webdriver.common.by import By
import json
import math
import sys
import os

def get_data(url):

	navigate(driver, url)

	items_result_wrapper = driver.find_elements(By.CSS_SELECTOR, 'p#toolbar-amount')[0]
	page_counters = items_result_wrapper.find_elements(By.CSS_SELECTOR, 'span.toolbar-number')

	if len(page_counters) > 1:
		page_count = math.ceil(int(page_counters[2].text) / int(page_counters[1].text))

		for page in range(1, page_count+1):
			navigate(driver, url + "?p=" + str(page))

			scrape_data()
	else:
		scrape_data()

def scrape_data():
	print("Scraping data")

	mytable = driver.find_element(By.CSS_SELECTOR, "ol.product-items")
	for row in mytable.find_elements(By.CSS_SELECTOR, "li.product-item"):
		global file
		product = dict()
		cells = row.find_elements(By.CSS_SELECTOR, "div.product-item-details");
		if cells:
			for cell in cells:
				if 'sku' in cell.get_attribute("class").split():
					product["sku"] = cell.text
				if 'description' in cell.get_attribute("class").split():
					product['description'] = cell.find_element(By.CSS_SELECTOR, 'strong.name').text
				if 'price' in cell.get_attribute("class").split():
					price = cell.find_element(By.CSS_SELECTOR, 'span.price-wrapper')
					product["retail_price"] = "{:.2f}".format(float(price.get_attribute('data-price-amount')))
				if 'available' in cell.get_attribute("class").split():
					product["stock"] = cell.text

			file.append(product)

file = []
fileStoragePath = sys.argv[1]
driver = setupBrowser()

login(driver)

get_data("https://portal.renewd.com/renewd/phone")

os.makedirs(os.path.dirname(fileStoragePath), exist_ok=True)
with open(fileStoragePath, 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=4)
driver.quit()

print(fileStoragePath + " created")
