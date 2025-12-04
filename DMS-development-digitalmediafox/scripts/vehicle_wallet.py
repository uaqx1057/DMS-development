# vehicle_wallet.py
import os
import time
from datetime import datetime, timedelta
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from dotenv import load_dotenv

# --- Load environment variables ---
load_dotenv()

# --- Vehicle Wallet Credentials (from .env) ---
VW_USERNAME = os.getenv("VW_USERNAME")
VW_PASSWORD = os.getenv("VW_PASSWORD")

# --- Test Allocation Data (edit as needed) ---
VEHICLE_PLATE = "9151TXA"
FUEL_AMOUNT = "10"            # provide either FUEL_AMOUNT or FUEL_VALUE
FUEL_VALUE = None
ALLOCATION_TYPE = "By Days"   # "One Time Allocation", "By Days", "By Month"
DAYS_OPTION = "1 day"        # used when ALLOCATION_TYPE == "By Days"
MONTH_OPTION = None           # used when ALLOCATION_TYPE == "By Month"
TIME_START = None             # optional, uses current date/time if None

# --- Selenium Setup ---
chrome_options = Options()
chrome_options.add_argument("--start-maximized")
# chrome_options.add_argument("--headless")  # uncomment for headless
driver = webdriver.Chrome(options=chrome_options)
wait = WebDriverWait(driver, 20)


def login_vehicle_wallet():
    driver.get("https://vehiclewallet.sa/vrp/client/login")
    wait.until(EC.presence_of_element_located((By.NAME, "loginEmail"))).send_keys(VW_USERNAME)
    driver.find_element(By.NAME, "password").send_keys(VW_PASSWORD)
    driver.find_element(By.XPATH, "//button[text()='Sign in']").click()
    # wait sidebar
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "ul.navigation-main")))
    fuel_link = wait.until(EC.element_to_be_clickable((By.XPATH, "//span[text()='Fuel Allocation']/parent::a")))
    fuel_link.click()
    # wait for search input on allocation page
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input.crud-search")))
    print("Login successful and Fuel Allocation page opened.")


def search_and_check_vehicle(plate):
    """Search vehicle and check its checkbox; wait for the allocation form container to appear."""
    search_input = driver.find_element(By.CSS_SELECTOR, "input.crud-search")
    search_input.clear()
    search_input.send_keys(plate)
    # small pause to let table filter
    time.sleep(1.2)

    # locate checkbox by label text and click via JS to avoid intercept
    checkbox = wait.until(EC.presence_of_element_located(
        (By.XPATH, f"//label[normalize-space()='{plate}']/preceding-sibling::input[@type='checkbox']")
    ))
    driver.execute_script("arguments[0].scrollIntoView({block:'center'})", checkbox)
    time.sleep(0.2)
    driver.execute_script("arguments[0].click()", checkbox)
    print(f"Checkbox for vehicle {plate} checked.")

    # Wait for the allocation row / form to appear under the page.
    # We look for the amount input which is present in the allocation form area that appears.
    amt = wait.until(EC.presence_of_element_located((By.NAME, "amount")))
    # Return the container element for the allocation controls (closest ancestor .row)
    container = amt.find_element(By.XPATH, "ancestor::div[contains(@class,'row') or contains(@class,'col-lg-12')][1]")
    # If ancestor selection returns the inner row, adjust to capture the full container with save button:
    # try ascending if the found container looks too small
    print("Vehicle search and checkbox done. Allocation container found.")
    return container


def select_react_option_by_typing(root_element, option_text, which_index=0, timeout=10):
    """
    Within root_element, find react-select input elements and type the option_text then Enter.
    which_index chooses which react-select input (0=first, 1=second, ...).
    Returns True if succeeded.
    """
    end_time = time.time() + timeout
    while time.time() < end_time:
        try:
            inputs = root_element.find_elements(By.CSS_SELECTOR, "input[id^='react-select'][id$='-input']")
            if len(inputs) > which_index:
                target = inputs[which_index]
                # scroll and click via JS (sometimes overlay elements intercept)
                driver.execute_script("arguments[0].scrollIntoView({block:'center'})", target)
                time.sleep(0.15)
                try:
                    driver.execute_script("arguments[0].click();", target)
                except Exception:
                    # clicking might not be necessary; continue
                    pass
                time.sleep(0.15)
                target.send_keys(option_text)
                time.sleep(0.25)
                target.send_keys("\n")
                time.sleep(0.6)  # allow React to register and update DOM
                return True
        except Exception:
            pass
        time.sleep(0.3)
    return False


def fill_allocation_form(container):
    """
    container: the allocation form root element returned by search_and_check_vehicle()
    This function fills amount/fuel, selects allocation type, selects dependent dropdown (days/month),
    fills One Time datetime, and clicks Save.
    """
    print("Filling allocation form...")

    # 1) Amount or Fuel
    if FUEL_AMOUNT:
        try:
            amount_input = container.find_element(By.NAME, "amount")
            driver.execute_script("arguments[0].scrollIntoView({block:'center'})", amount_input)
            amount_input.clear()
            amount_input.send_keys(str(FUEL_AMOUNT))
            print(f"Filled Amount: {FUEL_AMOUNT}")
        except Exception as e:
            print("Could not fill amount:", e)
    elif FUEL_VALUE:
        try:
            fuel_input = container.find_element(By.NAME, "temp_gas_tank_capacity")
            driver.execute_script("arguments[0].scrollIntoView({block:'center'})", fuel_input)
            fuel_input.clear()
            fuel_input.send_keys(str(FUEL_VALUE))
            print(f"Filled Fuel: {FUEL_VALUE}")
        except Exception as e:
            print("Could not fill fuel:", e)

    # 2) Select Allocation Type (first react-select inside container)
    ok = select_react_option_by_typing(container, ALLOCATION_TYPE, which_index=0, timeout=8)
    if not ok:
        print("Failed to select allocation type:", ALLOCATION_TYPE)
        # continue and try to proceed (may still work)
    else:
        print("Selected allocation type:", ALLOCATION_TYPE)

    # 3) If By Days or By Month, select the dependent dropdown.
    # The dependent react-select usually appears as the next react-select input inside the same container.
    time.sleep(0.5)  # give React a moment to render dependent control

    if ALLOCATION_TYPE.lower().startswith("by day"):
        if not DAYS_OPTION:
            print("DAYS_OPTION not provided; skipping selection.")
        else:
            # attempt to select the second react-select within the allocation container
            ok_days = select_react_option_by_typing(container, DAYS_OPTION, which_index=1, timeout=8)
            if not ok_days:
                # as fallback try to find any input[name='select-days'] and type into it
                try:
                    alt = container.find_element(By.CSS_SELECTOR, "input[name='select-days']")
                    driver.execute_script("arguments[0].scrollIntoView({block:'center'})", alt)
                    alt.click()
                    time.sleep(0.15)
                    alt.send_keys(DAYS_OPTION)
                    time.sleep(0.2)
                    alt.send_keys("\n")
                    ok_days = True
                except Exception:
                    ok_days = False
            print("Selected days option:" if ok_days else "Failed to select days option.")

    elif ALLOCATION_TYPE.lower().startswith("by month"):
        if not MONTH_OPTION:
            print("MONTH_OPTION not provided; skipping selection.")
        else:
            ok_month = select_react_option_by_typing(container, MONTH_OPTION, which_index=1, timeout=8)
            print("Selected month option." if ok_month else "Failed to select month option.")

    # 4) If One Time Allocation, fill datetime-local input that is inside same container (name=time_start)
    # 4) If One Time Allocation, fill datetime-local input
    if ALLOCATION_TYPE.lower().startswith("one time"):
        try:
            # wait a moment for datetime input to appear
            time.sleep(1.5)

            date_input = container.find_element(By.NAME, "time_start")
            driver.execute_script("arguments[0].scrollIntoView({block:'center'})", date_input)

            # now read the min attribute
            min_time_str = date_input.get_attribute("min")  # e.g., '2025-12-03T15:19'
            min_time = datetime.strptime(min_time_str, "%Y-%m-%dT%H:%M")

            # add 6 hours
            new_time = min_time
            ds = new_time.strftime("%Y-%m-%dT%H:%M")

            # set value and trigger events
            driver.execute_script("""
                arguments[0].value = arguments[1];
                arguments[0].dispatchEvent(new Event('input', {bubbles:true}));
                arguments[0].dispatchEvent(new Event('change', {bubbles:true}));
            """, date_input, ds)

            print("Filled One Time Allocation datetime:", ds)
        except Exception as e:
            print("Could not fill One Time date/time:", e)



    # 5) Click Save button (search only within container)
# After clicking Save button
    try:
        save_btn = container.find_element(By.XPATH, ".//button[text()='Save']")
        driver.execute_script("arguments[0].scrollIntoView({block:'center'})", save_btn)
        time.sleep(0.2)
        driver.execute_script("arguments[0].click();", save_btn)
        print("Clicked Save button")

        # Wait for success notification (adjust selector to match your notification element)
        success = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".toast-success, .notification-success"))
        )
        print("Allocation successfully saved!")

    except Exception as e:
        print("Could not click Save button or verify success:", e)



def main():
    try:
        login_vehicle_wallet()
        container = search_and_check_vehicle(VEHICLE_PLATE)
        # Give the container a moment if it is still populating
        time.sleep(0.8)
        fill_allocation_form(container)
        # wait a bit for server response / UI update
        time.sleep(3.5)
    finally:
        driver.quit()


if __name__ == "__main__":
    main()
