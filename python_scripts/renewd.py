from seleniumwire import webdriver
from selenium.webdriver.chrome.options import Options as ChromeOptions
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
from twocaptcha import TwoCaptcha
import os

solver = TwoCaptcha(os.getenv('TWO_CAPTCHA_TOKEN'))
username = os.getenv('RENEWD_USERNAME')
password = os.getenv('RENEWD_PASSWORD')

def login(driver):
	print('Logging in')
	# head to login page
	navigate(driver, "https://portal.renewd.com/")

	# find username/email field and send the username itself to the input field
	driver.find_element(By.NAME, "login[username]").send_keys(username)

	# find password input field and insert password as well
	passwordObject = driver.find_element(By.NAME, "login[password]").send_keys(password)

	time.sleep(5)
	# click login button
	driver.find_element(By.ID, "send2").click()

	time.sleep(10)

	if driver.find_elements(By.CSS_SELECTOR, '#g-recaptcha-response-1'):
		print('Solving Captcha')
		result = solver.recaptcha(
			sitekey='6LecQeEcAAAAAAFnJl7PCfN-fXG0kupDjohQIn-l',
			url='https://portal.renewd.com/customer/account/login/,'
		)

		driver.execute_script(
			'document.getElementById("g-recaptcha-response-1").innerHTML = "%s"'% result['code']
		)

		time.sleep(3)

		driver.execute_script("___grecaptcha_cfg.clients['1']['B']['B']['callback']('%s')"% result['code'])
		print('Captcha solved')

	print('Logged in')

def navigate(driver, url):
	print('Navigating to: ' + url)
	driver.get(url)

	WebDriverWait(driver=driver, timeout=10).until(
		lambda x: x.execute_script("return document.readyState === 'complete'")
	)
