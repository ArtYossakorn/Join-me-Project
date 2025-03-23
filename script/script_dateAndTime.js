document.addEventListener("DOMContentLoaded", function () {
    // ตั้งค่า Flatpickr สำหรับวันที่ (ให้เลือกวันที่เดียว)
    flatpickr("#date", {
        dateFormat: "Y-m-d", // รูปแบบที่จะแสดงใน input field
        altInput: true, // จะแสดงวันที่ในรูปแบบที่ใช้ง่ายขึ้น
        altFormat: "d F Y", // วันที่ในรูปแบบที่สวยงาม เช่น 2 มกราคม 2568
        locale: "th", // ใช้ภาษาไทย
        allowInput: true, // อนุญาตให้กรอกข้อมูลเอง
    });

    // ตั้งค่า Flatpickr สำหรับเวลาเริ่มต้น
    flatpickr("#startTime", {
        enableTime: true,  // เปิดใช้งานเวลา
        noCalendar: true,  // ปิดปฏิทิน
        dateFormat: "H:i", // รูปแบบเวลาเป็นชั่วโมง:นาที
        time_24hr: true,   // ใช้เวลาแบบ 24 ชั่วโมง
        altInput: true,    // แสดงเวลาในรูปแบบที่อ่านง่ายขึ้น
        altFormat: "H:i น.", // ตัวอย่าง 15:00 น.
    });

    // ตั้งค่า Flatpickr สำหรับเวลาสิ้นสุด
    flatpickr("#endTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        altInput: true,
        altFormat: "H:i น.",
    });
});
