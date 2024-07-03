from seleniumwire import webdriver
from selenium.webdriver.chrome.options import Options as ChromeOptions

def setupBrowser():
	options = ChromeOptions()
	options.add_argument("--window-size=1920,1080")
	options.add_argument("--start-maximized")
	# options.add_argument('--headless')
	options.add_argument('--ignore-certificate-errors')
	options.add_argument('--no-sandbox')
	options.add_argument("--disable-dev-shm-usage")

	cloud_options = {}
	cloud_options['browserName'] = "selenium"
	cloud_options['selenoid:options'] = {"enableVNC": True}

	options.set_capability('cloud:options', cloud_options)

	return webdriver.Remote(
		command_executor='http://selenium:4444/wd/hub',
		options=options,
		seleniumwire_options={
			'auto_config': False,
			'port': 8087,
			'addr': '0.0.0.0'
		}
	)
