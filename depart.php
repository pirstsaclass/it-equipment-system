<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ฟอร์มแจ้งซ่อมโรงเรียน</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .dropdown-row {
            display: flex;
            gap: 15px;
        }
        .dropdown-cell {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <h1>ฟอร์มแจ้งซ่อมโรงเรียน</h1>
    
    <div class="form-container">
        <form id="repairForm">
            <table>
                <thead>
                    <tr>
                        <th>โรงเรียน</th>
                        <th>ตึก</th>
                        <th>ชั้น</th>
                        <th>ห้อง</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label for="school">โรงเรียน <span class="required">*</span></label>
                                <select id="school" required>
                                    <option value="">-- เลือกโรงเรียน --</option>
                                    <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่</option>
                                    <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่</option>
                                    <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="building">ตึก <span class="required">*</span></label>
                                <select id="building" required disabled>
                                    <option value="">-- เลือกตึก --</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="floor">ชั้น <span class="required">*</span></label>
                                <select id="floor" required disabled>
                                    <option value="">-- เลือกชั้น --</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="room">ห้อง <span class="required">*</span></label>
                                <select id="room" required disabled>
                                    <option value="">-- เลือกห้อง --</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="form-group">
                <label for="problemType">ประเภทปัญหา <span class="required">*</span></label>
                <select id="problemType" required>
                    <option value="">-- เลือกประเภทปัญหา --</option>
                    <option value="ไฟฟ้า">ไฟฟ้า (ไฟไม่ติด, ปลั๊กเสีย, ฯลฯ)</option>
                    <option value="ประปา">ประปา (ท่อรั่ว, น้ำไม่ไหล, ฯลฯ)</option>
                    <option value="เฟอร์นิเจอร์">เฟอร์นิเจอร์ (โต๊ะ, เก้าอี้, ตู้)</option>
                    <option value="อาคาร">อาคาร (ผนัง, หน้าต่าง, ประตู)</option>
                    <option value="อื่นๆ">อื่นๆ</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="problemDetail">รายละเอียดปัญหา <span class="required">*</span></label>
                <textarea id="problemDetail" placeholder="อธิบายปัญหาที่พบโดยละเอียด..." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="reporter">ผู้แจ้ง <span class="required">*</span></label>
                <input type="text" id="reporter" placeholder="ชื่อ-นามสกุล" required>
            </div>
            
            <div class="form-group">
                <label for="contact">ช่องทางติดต่อ <span class="required">*</span></label>
                <input type="text" id="contact" placeholder="เบอร์โทรศัพท์ หรือ อีเมล" required>
            </div>
            
            <button type="submit">ส่งคำแจ้งซ่อม</button>
        </form>
    </div>

    <script>
        // ข้อมูลตึกตามโรงเรียน
        const buildingData = {
            "โรงเรียนวารีเชียงใหม่": [
                "ตึก1-อำนวยการ", "ตึก3-ประถม", "ตึก4-ประถม", "ตึก4 มัธยม", 
                "ตึก5-อนุบาล", "ตึก6??", "ตึก7-มัธยม", "ตึก10"
            ],
            "โรงเรียนอนุบาลวารีเชียงใหม่": [
                "ตึก1-อำนวยการม", "ตึก6??"
            ],
            "โรงเรียนนานาชาติวารีเชียงใหม่": [
                "ตึก8", "ตึก9", "ตึก10"
            ]
        };

        // ข้อมูลชั้นตามตึก
        const floorData = {
            "ตึก1-อำนวยการ": ["ชั้น1", "ชั้น2"],
            "ตึก3-ประถม": ["ชั้น1", "ชั้น2", "ชั้น3", "ชั้น4"],
            "ตึก4-ประถม": ["ชั้น1", "ชั้น2", "ชั้น3"],
            "ตึก4 มัธยม": ["ชั้น3", "ชั้น4", "ชั้น5"],
            "ตึก5-อนุบาล": ["ชั้น1", "ชั้น2"],
            "ตึก6??": ["ชั้น1", "ชั้น2"],
            "ตึก7-มัธยม": ["ชั้น1", "ชั้น2", "ชั้น3", "ชั้น4", "ชั้น5", "ชั้น6", "ชั้น7"],
            "ตึก8": ["ชั้น1", "ชั้น2", "ชั้น3", "ชั้น4"],
            "ตึก9": ["ชั้น1", "ชั้น2", "ชั้น3"],
            "ตึก10": ["ชั้น1", "ชั้น2"],
            "ตึก1-อำนวยการม": ["ชั้น1", "ชั้น2"] // สำหรับโรงเรียนอนุบาล
        };

        // ข้อมูลห้องตามชั้น (จำลองข้อมูล)
        const roomData = {
            "ชั้น1": ["ห้อง 101", "ห้อง 102", "ห้อง 103", "ห้อง 104", "ห้อง 105"],
            "ชั้น2": ["ห้อง 201", "ห้อง 202", "ห้อง 203", "ห้อง 204", "ห้อง 205"],
            "ชั้น3": ["ห้อง 301", "ห้อง 302", "ห้อง 303", "ห้อง 304", "ห้อง 305"],
            "ชั้น4": ["ห้อง 401", "ห้อง 402", "ห้อง 403", "ห้อง 404", "ห้อง 405"],
            "ชั้น5": ["ห้อง 501", "ห้อง 502", "ห้อง 503", "ห้อง 504", "ห้อง 505"],
            "ชั้น6": ["ห้อง 601", "ห้อง 602", "ห้อง 603", "ห้อง 604", "ห้อง 605"],
            "ชั้น7": ["ห้อง 701", "ห้อง 702", "ห้อง 703", "ห้อง 704", "ห้อง 705"]
        };

        // อ้างอิงถึง element ต่างๆ
        const schoolSelect = document.getElementById('school');
        const buildingSelect = document.getElementById('building');
        const floorSelect = document.getElementById('floor');
        const roomSelect = document.getElementById('room');

        // เมื่อเลือกโรงเรียน
        schoolSelect.addEventListener('change', function() {
            const selectedSchool = this.value;
            
            // รีเซ็ต dropdown อื่นๆ
            buildingSelect.innerHTML = '<option value="">-- เลือกตึก --</option>';
            floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
            roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            
            // ปิดการใช้งาน dropdown อื่นๆ จนกว่าจะเลือกโรงเรียน
            buildingSelect.disabled = true;
            floorSelect.disabled = true;
            roomSelect.disabled = true;
            
            if (selectedSchool) {
                // เปิดใช้งาน dropdown ตึก
                buildingSelect.disabled = false;
                
                // เพิ่มตัวเลือกตึกตามโรงเรียนที่เลือก
                buildingData[selectedSchool].forEach(building => {
                    const option = document.createElement('option');
                    option.value = building;
                    option.textContent = building;
                    buildingSelect.appendChild(option);
                });
            }
        });

        // เมื่อเลือกตึก
        buildingSelect.addEventListener('change', function() {
            const selectedBuilding = this.value;
            
            // รีเซ็ต dropdown อื่นๆ
            floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
            roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            
            // ปิดการใช้งาน dropdown อื่นๆ จนกว่าจะเลือกตึก
            floorSelect.disabled = true;
            roomSelect.disabled = true;
            
            if (selectedBuilding) {
                // เปิดใช้งาน dropdown ชั้น
                floorSelect.disabled = false;
                
                // เพิ่มตัวเลือกชั้นตามตึกที่เลือก
                floorData[selectedBuilding].forEach(floor => {
                    const option = document.createElement('option');
                    option.value = floor;
                    option.textContent = floor;
                    floorSelect.appendChild(option);
                });
            }
        });

        // เมื่อเลือกชั้น
        floorSelect.addEventListener('change', function() {
            const selectedFloor = this.value;
            
            // รีเซ็ต dropdown ห้อง
            roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            
            // ปิดการใช้งาน dropdown ห้อง จนกว่าจะเลือกชั้น
            roomSelect.disabled = true;
            
            if (selectedFloor) {
                // เปิดใช้งาน dropdown ห้อง
                roomSelect.disabled = false;
                
                // เพิ่มตัวเลือกห้องตามชั้นที่เลือก
                roomData[selectedFloor].forEach(room => {
                    const option = document.createElement('option');
                    option.value = room;
                    option.textContent = room;
                    roomSelect.appendChild(option);
                });
            }
        });

        // เมื่อส่งฟอร์ม
        document.getElementById('repairForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // ตรวจสอบว่าทุกฟิลด์ที่จำเป็นถูกกรอก
            if (!schoolSelect.value || !buildingSelect.value || !floorSelect.value || !roomSelect.value) {
                alert('กรุณาเลือกข้อมูลสถานที่ให้ครบถ้วน');
                return;
            }
            
            // แสดงข้อมูลที่กรอก (ในทางปฏิบัติควรส่งข้อมูลไปยังเซิร์ฟเวอร์)
            alert('ส่งคำแจ้งซ่อมสำเร็จ!\n\n' +
                  'โรงเรียน: ' + schoolSelect.value + '\n' +
                  'ตึก: ' + buildingSelect.value + '\n' +
                  'ชั้น: ' + floorSelect.value + '\n' +
                  'ห้อง: ' + roomSelect.value + '\n' +
                  'ประเภทปัญหา: ' + document.getElementById('problemType').value + '\n' +
                  'รายละเอียด: ' + document.getElementById('problemDetail').value + '\n' +
                  'ผู้แจ้ง: ' + document.getElementById('reporter').value + '\n' +
                  'ช่องทางติดต่อ: ' + document.getElementById('contact').value);
            
            // รีเซ็ตฟอร์ม
            this.reset();
            
            // รีเซ็ตสถานะ dropdown
            buildingSelect.disabled = true;
            floorSelect.disabled = true;
            roomSelect.disabled = true;
        });
    </script>
</body>
</html>