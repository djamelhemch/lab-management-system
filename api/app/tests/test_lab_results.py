# quick_test.py
import requests
import socket
from datetime import datetime

BASE_URL = "http://localhost:8000"


# ----------------------------------------
# BASIC TESTS
# ----------------------------------------

def test_api_health():
    """Quick API connectivity test"""
    try:
        response = requests.get(f"{BASE_URL}/lab-results/")
        print(f"✅ API Status: {response.status_code}")
        return response.status_code == 200
    except Exception as e:
        print(f"❌ API Error: {e}")
        return False


def test_device_connectivity(host="127.0.0.1", port=2575):
    """Test device simulator connectivity"""
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(5)
        sock.connect((host, port))
        sock.close()
        print(f"✅ Device {port}: Connected")
        return True
    except Exception as e:
        print(f"❌ Device {port}: {e}")
        return False


# ----------------------------------------
# EXISTING TEST
# ----------------------------------------

def test_create_bulk_results():
    """Test bulk creation of lab results for an entire quotation"""
    data = {
        "quotation_id": 16,   # modify as needed
        "result_values": {
            29: 18.0,
            30: 8.0,
            31: 8.0,
            32: 10.0
        }
    }

    try:
        response = requests.post(f"{BASE_URL}/lab-results/bulk", json=data)
        print(f"✅ Bulk Create Result: {response.status_code}")
        print("📦 Response:")
        print(response.json())
        return response.status_code in [200, 404]
    except Exception as e:
        print(f"❌ Bulk Create Error: {e}")
        return False


# ----------------------------------------
# NEW TESTS
# ----------------------------------------

def test_missing_quotation_items():
    """Detect missing items in quotation → API should return validation error"""
    data = {
        "quotation_id": 16,
        "result_values": {   # intentionally missing some analysis IDs
            29: 10.0
        }
    }

    try:
        response = requests.post(f"{BASE_URL}/lab-results/bulk", json=data)

        if response.status_code in [400, 422]:
            print("✅ Missing items correctly detected")
            print("📌 Message:", response.json())
            return True

        print("❌ Missing items NOT detected:", response.status_code, response.json())
        return False

    except Exception as e:
        print(f"❌ Missing Items Test Error: {e}")
        return False


def test_device_routing():
    """Validate per-device routing for analyses"""
    try:
        response = requests.get(f"{BASE_URL}/routing/check-latest")
        data = response.json()

        if "routes" not in data:
            print("❌ No routing info returned")
            return False

        print("📦 Routing Info:", data)

        ok = True

        for route in data["routes"]:
            if route["device_id"] not in [1, 2, 3, 4]:
                print("❌ Invalid device routing:", route)
                ok = False

        if ok:
            print("✅ Device routing validated")
        return ok

    except Exception as e:
        print(f"❌ Routing Test Error: {e}")
        return False


def test_result_interpretation():
    """Test LOW / NORMAL / HIGH / CRITICAL interpretations via saved results"""
    quotation_id = 16
    # Fetch the results for this quotation
    response = requests.get(f"{BASE_URL}/lab-results/?skip=0&limit=100")
    results = response.json()

    # Map by quotation_item_id
    result_map = {r["quotation_item_id"]: r for r in results if r["quotation_id"] == quotation_id}

    expected_values = {
        29: ("critical", 3.0),
        30: ("normal", 14.0),
        31: ("high", 19.0),
        32: ("critical", 1.0)
    }

    all_ok = True
    for q_item_id, (expected_label, value) in expected_values.items():
        r = result_map.get(q_item_id)
        if not r:
            print(f"❌ Result missing for quotation_item_id {q_item_id}")
            all_ok = False
            continue

        if r["interpretation"] != expected_label:
            print(f"❌ Interpretation mismatch for {q_item_id}: expected {expected_label}, got {r['interpretation']}")
            all_ok = False
        else:
            print(f"✅ Interpretation '{expected_label}' OK for item {q_item_id}")

    return all_ok


def test_all_analyses_created():
    """Ensure all analyses from a quotation were created"""
    quotation_id = 16

    try:
        response = requests.get(f"{BASE_URL}/quotations/{quotation_id}/analyses-check")
        data = response.json()

        expected = data.get("expected_count")
        created = data.get("created_count")

        if expected == created:
            print(f"✅ All analyses created ({created}/{expected})")
            return True

        print(f"❌ Missing analyses: {created}/{expected}")
        return False

    except Exception as e:
        print(f"❌ Analysis Creation Test Error: {e}")
        return False


# ----------------------------------------
# RUN TESTS
# ----------------------------------------

if __name__ == "__main__":
    print("\n🧪 Running Quick Tests\n" + "=" * 50)

    test_api_health()

    # Device connectivity tests
    test_device_connectivity(port=2575)  # CL-900i
    test_device_connectivity(port=2576)  # BS-240
    test_device_connectivity(port=2577)  # BC-30i

    # Bulk creation
    test_create_bulk_results()

    # NEW TESTS
    test_missing_quotation_items()
    test_device_routing()
    test_result_interpretation()
    test_all_analyses_created()

    print("=" * 50 + "\n")
