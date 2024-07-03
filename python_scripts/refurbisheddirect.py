from seleniumwire import webdriver
from selenium.webdriver.chrome.options import Options as ChromeOptions
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
import os

username = os.getenv('REFURBISHEDDIRECT_USERNAME')
password = os.getenv('REFURBISHEDDIRECT_PASSWORD')

def login(driver):
	print('Logging in')

	navigate(driver, "https://refurbisheddirect.com/account/login")

	# WebDriverWait(driver=driver, timeout=10).until(
	# 	EC.element_to_be_clickable((By.XPATH, "//*[normalize-space()='Prijzen excl. BTW.']"))
	# )

	# if driver.find_elements(By.ID, 'maxiaTaxSwitchModal'):
	# 	driver.find_element(By.XPATH, "//*[normalize-space()='Prijzen excl. BTW.']").click()

	time.sleep(3)

	# find username/email field and send the username itself to the input field
	driver.find_element(By.NAME, "username").send_keys(username)

	# find password input field and insert password as well
	driver.find_element(By.NAME, "password").send_keys(password)


	# click login button
	driver.find_element(By.CLASS_NAME, 'login-submit').find_element(By.CLASS_NAME, 'btn').click()

	WebDriverWait(driver, 10).until(
		EC.element_to_be_clickable((By.ID, "CybotCookiebotDialogBodyLevelButtonLevelOptinAllowAll"))
	)

	driver.find_element(By.ID, "CybotCookiebotDialogBodyLevelButtonLevelOptinAllowAll").click()

	print('Logged in')

def navigate(driver, url):
	print('Navigating to: ' + url)
	driver.get(url)
	# wait the ready state to be complete
	WebDriverWait(driver=driver, timeout=10).until(
		lambda x: x.execute_script("return document.readyState === 'complete'")
	)

	element = driver.find_element(By.XPATH, "//*[@data-flyout-menu-trigger]")
	driver.execute_script("var element = arguments[0]; element.remove();", element)

def await_filter_results(driver):
	el = WebDriverWait(driver=driver, timeout=10).until(
		EC.visibility_of_element_located((By.CSS_SELECTOR, "div.cms-element-product-listing-wrapper"))
	)
	WebDriverWait(driver, 10).until(lambda d: 'has-element-loader' not in el.get_attribute('class'))
	time.sleep(1)
