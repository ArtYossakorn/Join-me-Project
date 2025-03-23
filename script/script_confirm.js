function confirmDelete(postID) {
    if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบโพสต์นี้?")) {
        window.location.href = "delete-post-action.php?postID=" + postID;
    }
}

function confirmCreatePost(event) {
    var confirmation = confirm("คุณแน่ใจหรือไม่ว่าต้องการสร้างโพสต์?");
    if (!confirmation) {
        event.preventDefault();  // ป้องกันไม่ให้ฟอร์มถูกส่ง
        return false;
    }
    // ถ้ายืนยันให้ส่งฟอร์ม
    return true;
}

// ปุ่มลงทะเบียน
document.addEventListener("DOMContentLoaded", function () {
    // เลือกปุ่มทั้งหมดที่มี name="reg-btn"
    const submitButtons = document.querySelectorAll('button[name="reg-btn"]');

    // วนลูปผ่านทุกปุ่ม
    submitButtons.forEach(function (submitButton) {
        submitButton.addEventListener('click', function (event) {
            // แสดงกล่องยืนยัน
            const isConfirmed = confirm("คุณต้องการลงชื่อเข้าร่วมกิจกรรมนี้ใช่หรือไม่?");

            // ถ้าผู้ใช้ไม่ยืนยัน ให้หยุดการส่งฟอร์ม
            if (!isConfirmed) {
                event.preventDefault();  // ยกเลิกการ submit ฟอร์ม
            }
        });
    });
});

// ปุ่มแก้ไขสมาชิก
document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.querySelector("button[name='edit']");
    if (editBtn) {
        editBtn.addEventListener("click", function (event) {
            if (!confirm("คุณแน่ใจหรือไม่ว่าต้องการแก้ไขข้อมูล?")) {
                event.preventDefault();
            }
        });
    }
});

// ปุ่มเพิ่มสมาชิก
document.addEventListener("DOMContentLoaded", function() {
    // เลือกปุ่มที่มี name="save"
    const saveButton = document.querySelector('button[name="save"]');

    // เมื่อคลิกปุ่ม
    saveButton.addEventListener('click', function(event) {
        // แสดงกล่องยืนยัน
        var confirmation = confirm("คุณแน่ใจหรือไม่ว่าต้องการเพิ่มข้อมูลผู้ใช้?");
        
        // ถ้าผู้ใช้ไม่ยืนยัน ให้หยุดการส่งฟอร์ม
        if (!confirmation) {
            event.preventDefault();  // ยกเลิกการ submit ฟอร์ม
        }
    });
});

