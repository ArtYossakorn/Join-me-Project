document.addEventListener("DOMContentLoaded", function () {
    const postTypeSelect = document.getElementById("postType");
    
    const postNameGroup = document.getElementById("postNameGroup");
    const locationGroup = document.getElementById("locationGroup");
    const dateGroup = document.getElementById("dateGroup");
    const startTimeGroup = document.getElementById("startTimeGroup");
    const endTimeGroup = document.getElementById("endTimeGroup");
    const limitGroup = document.getElementById("limit-group");

    // ตรวจสอบค่าเริ่มต้น
    toggleFields();

    // ฟังก์ชันซ่อน/แสดงฟิลด์
    function toggleFields() {
        if (postTypeSelect.value === "กิจกรรม") {
            postNameGroup.style.display = "block";
            locationGroup.style.display = "block";
            dateGroup.style.display = "block";
            startTimeGroup.style.display = "block";
            endTimeGroup.style.display = "block";
            limitGroup.style.display = "block";
        } else {
            postNameGroup.style.display = "none";
            locationGroup.style.display = "none";
            dateGroup.style.display = "none";
            startTimeGroup.style.display = "none";
            endTimeGroup.style.display = "none";
            limitGroup.style.display = "none";
        }
    }

    // เรียกใช้งานเมื่อมีการเปลี่ยนค่า
    postTypeSelect.addEventListener("change", toggleFields);
});
