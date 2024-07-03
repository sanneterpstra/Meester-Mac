from general import setupBrowser
from refurbisheddirect import navigate
from refurbisheddirect import login
from refurbisheddirect import await_filter_results
from seleniumwire import webdriver
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
import json
import sys
import os

def get_data(url):
	navigate(driver, url)
	
	results = setProductFilters()
	if results == 1:
		if driver.find_elements(By.CLASS_NAME, "pagination-nav"):
			page_counters = driver.find_element(By.CLASS_NAME, "pagination-nav").find_elements(By.CLASS_NAME, 'page-item')
			
			for key in range(1, (len(page_counters) -  4)):
				navigate(driver, url + "?p=" + str(key))

				scrape_data()
		else:
			scrape_data()

def setProductFilters():
	print("Setting iMac filters")

	element = driver.find_element(By.XPATH, "//*[normalize-space()='CPU']")
	driver.execute_script("arguments[0].scrollIntoView({'block':'center','inline':'center'})", element)

	driver.implicitly_wait(1)
	
	element.click()
	
	driver.implicitly_wait(3)

	if element.find_element(By.XPATH, '..').find_elements(By.XPATH, "//*[normalize-space()='Apple M1']"):
		element.find_element(By.XPATH, '..').find_element(By.XPATH, "//*[normalize-space()='Apple M1']").find_element(By.TAG_NAME, 'label').click()
		
		await_filter_results(driver)
		
		return 1
	else:
		return 0
	
def scrape_data():
	print("Scraping data")
	container = driver.find_element(By.CLASS_NAME, 'cms-listing-row')

	links = []

	for row in container.find_elements(By.CLASS_NAME, "cms-listing-col"):
		href = row.find_element(By.TAG_NAME, 'a').get_attribute("href")
		links.append(href)

	for link in links:
		navigate(driver, link)

		driver.implicitly_wait(3)

		# Get all variations of product and store the text of the condition in a list
		conditions = [];
		variants = driver.find_elements(By.CLASS_NAME, 'rd-product-detail-configurator-option__option-name')
		for variant in variants:
			conditions.append(variant.get_attribute('innerText'))

		for key, variant in enumerate(variants):
			# Click the variation button
			driver.find_element(By.XPATH, "//*[normalize-space()='{}']".format(conditions[key])).click()
			driver.implicitly_wait(3)
			
			# Skip variation if it has particularities
			if driver.find_elements(By.XPATH, "//*[normalize-space()='Dit product heeft een lichte vlek/verkleuring in beeld. Uw voordeel is reeds in de prijs verwerkt.']"):
				continue

			product = {};

			# Find specifications
			table = driver.find_element(By.CLASS_NAME, "product-detail-properties-table")
			for row in table.find_elements(By.CLASS_NAME, "properties-row"):
				propertyName = row.find_element(By.CLASS_NAME, 'properties-label').get_attribute('innerText')
				propertyValue = row.find_element(By.TAG_NAME, 'span').get_attribute('innerText')

				product[propertyName.replace(":", "")] = propertyValue


			product['Conditie'] = driver.find_element(By.XPATH, "//*[normalize-space()='{}']".format(conditions[key])).get_attribute('innerText')

			# Find stock
			select = driver.find_element(By.CLASS_NAME, 'product-detail-quantity-select')
			options = select.find_elements(By.TAG_NAME, "option")
			stock = int(options[len(options)-1].text)

			product['Stock'] = stock

			product['Product_id'] = driver.find_element(By.CLASS_NAME, 'product-detail-ordernumber').get_attribute('innerText')

			product['Price'] = driver.find_element(By.CLASS_NAME, 'product-detail-price').get_attribute('innerText')

			file.append(product)

driver = setupBrowser()
action = webdriver.ActionChains(driver)
file = []
fileStoragePath = sys.argv[1]

login(driver)
get_data("https://refurbisheddirect.com/desktops/apple-imac/")

os.makedirs(os.path.dirname(fileStoragePath), exist_ok=True)
with open(fileStoragePath, 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=4)
driver.quit()

print(fileStoragePath + " created")
