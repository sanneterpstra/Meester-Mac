from general import setupBrowser
from refurbisheddirect import navigate
from refurbisheddirect import login
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.by import By
import time
import requests
import json
import sys
import os

def get_data(url):
	navigate(driver, url)
	
	if driver.find_elements(By.CLASS_NAME, "pagination-nav"):
		page_counters = driver.find_element(By.CLASS_NAME, "pagination-nav").find_elements(By.CLASS_NAME, 'page-item')
		
		for key in range(1, (len(page_counters) -  4)):
			navigate(driver, url + "?p=" + str(key))

			scrape_data()
	else:
		scrape_data()

def scrape_data():
	print("Scraping data")

	container = driver.find_element(By.CLASS_NAME, 'cms-listing-row')

	links = []

	for row in container.find_elements(By.CLASS_NAME, "cms-listing-col"):
		href = row.find_element(By.TAG_NAME, 'a').get_attribute("href")
		links.append(href)

	for link in links:
		navigate(driver, link)

		# Get all variations of product and store the text of the condition in a list
		conditions = [];
		variants = driver.find_elements(By.CLASS_NAME, 'rd-product-detail-configurator-option__option-name')
		for variant in variants:
			conditions.append(variant.get_attribute('innerText'))

		variantsCount = len(variants)
		for variantkey, variant in enumerate(variants):
			if variantsCount > 1:
				form = driver.find_element(By.CLASS_NAME, 'product-detail-configurator').find_element(By.TAG_NAME, 'form')
				formData = form.get_attribute('data-variant-switch-options')
				formUrl = json.loads(formData)['url']

				checkBox = driver.find_element(By.XPATH, "//*[normalize-space()='{}']/input".format(conditions[variantkey]))
				name = checkBox.get_attribute('name')
				value = checkBox.get_attribute('value')

				response = requests.get(url=formUrl, params={
					'options': '{"' + name + '":"' + value + '","":"on"}',
					'switched': name
				})
				navigate(driver, response.json()['url'])

			# Skip variation if it has particularities
			if driver.find_elements(By.XPATH, "//*[normalize-space()='Face ID is uitgeschakeld, ontgrendelen alleen mogelijk via toegangscode.']"):
				continue
			if driver.find_elements(By.XPATH, "//*[contains(text(), 'No Face ID')]"):
				print('Found')
				continue

			product = {};

			# Find specifications
			tables = driver.find_elements(By.CLASS_NAME, "product-detail-properties-table")

			for table in tables:
				rows = table.find_elements(By.CLASS_NAME, "properties-row")
				for key, row in enumerate(rows):
					propertyName = row.find_element(By.CLASS_NAME, 'properties-label').get_attribute('innerText')
					propertyValue = row.find_element(By.TAG_NAME, 'span').get_attribute('innerText')

					product[propertyName.replace(":", "")] = propertyValue

			product['Conditie'] = driver.find_element(By.XPATH, "//*[normalize-space()='{}']".format(conditions[variantkey])).get_attribute('innerText')

			# Find stock
			if driver.find_elements(By.CLASS_NAME, 'product-detail-quantity-select'):
				select = driver.find_element(By.CLASS_NAME, 'product-detail-quantity-select')
				options = select.find_elements(By.TAG_NAME, "option")
				stock = int(options[len(options)-1].text)
			else:
				continue

			product['Stock'] = stock

			product['Product_id'] = driver.find_element(By.CLASS_NAME, 'product-detail-ordernumber').get_attribute('innerText')

			product['Price'] = driver.find_element(By.CLASS_NAME, 'product-detail-price').get_attribute('innerText')

			file.append(product)

file = []
fileStoragePath = sys.argv[1]
driver = setupBrowser()

login(driver)
get_data("https://refurbisheddirect.com/iphone-se/")
get_data("https://refurbisheddirect.com/iphone-x/")
get_data("https://refurbisheddirect.com/iphone-11/")
get_data("https://refurbisheddirect.com/iphone-12/")
get_data("https://refurbisheddirect.com/iphone-13/")
get_data("https://refurbisheddirect.com/iphone-14/")

os.makedirs(os.path.dirname(fileStoragePath), exist_ok=True)
with open(fileStoragePath, 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=4)
driver.quit()

print(fileStoragePath + " created")
