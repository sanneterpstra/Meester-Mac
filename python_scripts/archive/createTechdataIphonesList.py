#!/usr/bin/env python3
from logging import error
from seleniumwire import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options as ChromeOptions
import time
import json
import gzip

# Github credentials
accountid = "754653"
username = "STerpstra"
password = "-JsQtJiw6m3pzJ-eVRxq"

# initialize the Chrome driver
CHROMEDRIVER_PATH = '/usr/local/bin/chromedriver'
WINDOW_SIZE = "1920,1080"

options = ChromeOptions()
# options.add_argument('--headless')
options.add_argument('--ignore-certificate-errors')
options.add_argument('--no-sandbox')
options.add_argument("--disable-dev-shm-usage")

cloud_options = {}
cloud_options['browserName'] = "selenium"
cloud_options['selenoid:options'] = {"enableVNC": True}

options.set_capability('cloud:options', cloud_options)



# capabilities = {
# 	"browserName": "selenium",
# 	"selenoid:options": {
# 		"enableVNC": True
# 	}
# }



# capabilities.update(chrome_options.to_capabilities())

driver = webdriver.Remote(
	command_executor='http://selenium:4444/wd/hub',
	options=options,
	seleniumwire_options={
		'auto_config': False,
		'port': 8087,
		'addr': '0.0.0.0'
	}
)

# driver = webdriver.Remote('', options=chrome_options)

# head to github login page
driver.get("https://intouch.tdsynnex.com/InTouch/MVC/Microsite/Private?categorypageid=4352&msmenuid=8899&prodids=7347417,7347421,7347414,7347420,7347423,7347416,7347419,7347422,7347415&slctd=542&tabid=5164")

driver.find_element(By.NAME, "customerId").send_keys(accountid)

# # find username/email field and send the username itself to the input field
driver.find_element(By.NAME, "loginUserName").send_keys(username)

# # find password input field and insert password as well
driver.find_element(By.ID, "password").send_keys(password)


# # click login button
driver.find_element(By.ID, 'pageLoginBtn').click()

time.sleep(10)

if driver.find_elements(By.ID, 'onetrust-accept-btn-handler'):
	driver.find_element(By.ID, 'onetrust-accept-btn-handler').click()

	time.sleep(2)

links = driver.find_element(By.ID, 'lop-header-100').find_elements(By.TAG_NAME, 'a')
file = []

for key, link in enumerate(links):
	
	links = driver.find_element(By.ID, 'lop-header-100').find_elements(By.TAG_NAME, 'a')
	print('Navigate to page ' + links[key].get_attribute('innerText') + ' \n')
	links[key].click()
	time.sleep(5)

	if driver.find_element(By.ID, 'main-loader').is_displayed():
		driver.refresh()
		time.sleep(5)

	print('searching for container div \n')
	container = driver.find_element(By.CLASS_NAME, 'ps-view')

	for item in container.find_elements(By.CLASS_NAME, "ps-result-item"):

		product = {};
		print('searching for desc-text-product \n')
		product['name'] = item.find_element(By.CLASS_NAME, 'desc-text-product').get_attribute('innerText')
		print('searching for Manuf.nr.: \n')
		product['mpn'] = item.find_element(By.XPATH, ".//*[normalize-space()='Manuf.nr.:']/following-sibling::span[1]").get_attribute('innerText')
		print('searching for Partnr. \n')
		product['partnr'] = item.find_element(By.XPATH, ".//*[normalize-space()='Partnr.']/following-sibling::span[1]").get_attribute('innerText')
		print('Partnr = '+ product['partnr'] + '\n')
		print('searching for desc-text \n')
		product['description'] = item.find_element(By.CLASS_NAME, 'desc-text').get_attribute('innerText')
		print('searching for Beschikbare voorraad \n')
		product['stock'] = item.find_element(By.XPATH, ".//*[contains(text(), 'Beschikbare voorraad')]").find_element(By.CLASS_NAME, 'priceRight').get_attribute('innerText')
		print('Beschikbare voorraad = '+ product['stock'] + '\n')
		print('searching for priceBig \n')
		product['price'] = item.find_element(By.CLASS_NAME, 'priceBig').get_attribute('innerText')
		print('priceBig = '+ product['price'] + '\n')

		file.append(product)

file = json.dumps(file)
file = json.loads(file)

with open('techdata_iphones_test.json', 'w', encoding='utf8') as json_file:
	json.dump(file, json_file, indent=2)

driver.quit()
