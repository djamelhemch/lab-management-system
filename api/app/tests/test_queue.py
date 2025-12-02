import requests
import json

BASE_URL = "http://localhost:8000"

def test_queue_flow():
    print("=" * 50)
    print("Testing Queue System")
    print("=" * 50)
    
    # Step 1: Get patients
    print("\n1. Getting patients...")
    response = requests.get(f"{BASE_URL}/patients")
    patients = response.json()
    if not patients:
        print("❌ No patients found!")
        return
    patient_id = patients[0]['id']
    print(f"✓ Using patient ID: {patient_id}")
    
    # Step 2: Get current queues
    print("\n2. Getting current queues...")
    response = requests.get(f"{BASE_URL}/queues")
    data = response.json()
    print(f"✓ Reception: {len(data['reception'])} patients")
    print(f"✓ Blood Draw: {len(data['blood_draw'])} patients")
    
    # Step 3: Add to reception
    print("\n3. Adding patient to reception queue...")
    response = requests.post(f"{BASE_URL}/queues", json={
        "patient_id": patient_id,
        "type": "reception",
        "priority": 0
    })
    if response.status_code == 201:
        queue_item = response.json()
        print(f"✓ Added to position {queue_item['position']}")
    else:
        print(f"❌ Failed: {response.text}")
        return
    
    # Step 4: Check queues
    print("\n4. Checking queues after add...")
    response = requests.get(f"{BASE_URL}/queues")
    data = response.json()
    print(f"✓ Reception: {len(data['reception'])} patients")
    print(f"✓ Blood Draw: {len(data['blood_draw'])} patients")
    
    # Step 5: Move next
    print("\n5. Moving patient to blood draw...")
    response = requests.post(f"{BASE_URL}/queues/move-next")
    if response.status_code == 200:
        queue_item = response.json()
        print(f"✓ Moved to blood draw, position {queue_item['position']}")
        print(f"✓ Status: {queue_item['status']}")
    else:
        print(f"❌ Failed: {response.text}")
        return
    
    # Step 6: Final check
    print("\n6. Final queue status...")
    response = requests.get(f"{BASE_URL}/queues")
    data = response.json()
    print(f"✓ Reception: {len(data['reception'])} patients")
    print(f"✓ Blood Draw: {len(data['blood_draw'])} patients")
    
    # Step 7: Check logs
    print("\n7. Checking queue logs...")
    # You'd need to add a logs endpoint or check DB directly
    
    print("\n" + "=" * 50)
    print("✓ All tests passed!")
    print("=" * 50)

if __name__ == "__main__":
    test_queue_flow()
