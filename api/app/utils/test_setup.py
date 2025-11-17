import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).resolve().parents[2]))

from sqlalchemy import text  # import sqlalchemy first

from app.database import SessionLocal, engine  # then your app modules
from app.models.lab_device import LabDevice, DeviceMessage, FhirResource


def test_connection():
    """Test database connection"""
    try:
        with engine.connect() as conn:
            result = conn.execute(text("SELECT DATABASE()"))
            db_name = result.scalar()
            print(f"✅ Connected to database: {db_name}")
            return True
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        return False


def test_devices():
    """Test device queries"""
    db = SessionLocal()
    
    try:
        # Get all devices
        devices = db.query(LabDevice).all()
        print(f"\n{'='*50}")
        print(f"✅ Found {len(devices)} devices:")
        print(f"{'='*50}")
        
        for device in devices:
            print(f"\n📱 {device.name}")
            print(f"   Manufacturer: {device.manufacturer}")
            print(f"   Model: {device.model}")
            print(f"   Protocol: {device.protocol_type} v{device.hl7_version or 'N/A'}")
            print(f"   Connection: {device.connection_type}")
            print(f"   IP:Port: {device.ip_address or 'N/A'}:{device.tcp_port or 'N/A'}")
            print(f"   Status: {device.status}")
            print(f"   Active: {'✅' if device.is_active else '❌'}")
            print(f"   Supports Orders: {'✅' if device.supports_orders else '❌'}")
            
            # Count messages for this device
            device_msg_count = db.query(DeviceMessage).filter(
                DeviceMessage.device_id == device.id
            ).count()
            print(f"   Messages: {device_msg_count}")
        
        # Overall statistics
        print(f"\n{'='*50}")
        print("📊 Overall Statistics:")
        print(f"{'='*50}")
        
        total_messages = db.query(DeviceMessage).count()
        print(f"   Total Messages: {total_messages}")
        
        pending_messages = db.query(DeviceMessage).filter(
            DeviceMessage.status == 'pending'
        ).count()
        print(f"   Pending Messages: {pending_messages}")
        
        failed_messages = db.query(DeviceMessage).filter(
            DeviceMessage.status == 'failed'
        ).count()
        print(f"   Failed Messages: {failed_messages}")
        
        fhir_count = db.query(FhirResource).count()
        print(f"   FHIR Resources: {fhir_count}")
        
        # Device status breakdown
        online = db.query(LabDevice).filter(LabDevice.status == 'online').count()
        offline = db.query(LabDevice).filter(LabDevice.status == 'offline').count()
        error = db.query(LabDevice).filter(LabDevice.status == 'error').count()
        
        print(f"\n   Device Status:")
        print(f"      🟢 Online: {online}")
        print(f"      🔴 Offline: {offline}")
        print(f"      ⚠️  Error: {error}")
        
    except Exception as e:
        print(f"❌ Query error: {e}")
        import traceback
        traceback.print_exc()
    finally:
        db.close()


def test_specific_device(device_id: int = 1):
    """Test querying a specific device"""
    db = SessionLocal()
    
    try:
        device = db.query(LabDevice).filter(LabDevice.id == device_id).first()
        
        if device:
            print(f"\n{'='*50}")
            print(f"Device ID {device_id} Details:")
            print(f"{'='*50}")
            print(f"Name: {device.name}")
            print(f"Manufacturer: {device.manufacturer}")
            print(f"Model: {device.model}")
            print(f"Serial: {device.serial_number or 'N/A'}")
            print(f"Status: {device.status}")
            print(f"Last Connected: {device.last_connected or 'Never'}")
            print(f"Last Error: {device.last_error or 'None'}")
            
            # Show custom config
            if device.custom_config:
                print(f"Custom Config: {device.custom_config}")
        else:
            print(f"❌ Device ID {device_id} not found")
            
    except Exception as e:
        print(f"❌ Error querying device: {e}")
    finally:
        db.close()


def main():
    print("Loading order:", importlib.util.find_spec("sqlalchemy.util.concurrency"))
    """Run all tests"""
    print("\n" + "="*50)
    print("🧪 Lab Device System Test")
    print("="*50)
    
    # Test connection
    if not test_connection():
        print("\n❌ Aborting tests - database connection failed")
        return
    
    # Test devices
    test_devices()
    
    # Test specific device
    test_specific_device(1)
    
    print("\n" + "="*50)
    print("✅ All tests completed!")
    print("="*50 + "\n")


if __name__ == "__main__":
    main()
