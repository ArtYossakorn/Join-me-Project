function previewImage() {
    var file = document.getElementById("profileImage").files[0]; // Get the selected file
    var reader = new FileReader(); // Create a new FileReader instance

    reader.onloadend = function () {
        var preview = document.getElementById("profileImagePreview");
        preview.src = reader.result; // Set the preview image source to the selected file's data URL
        preview.style.display = "block"; // Show the preview image
    };

    if (file) {
        reader.readAsDataURL(file); // Read the selected file as a data URL
    } else {
        alert("ไม่พบไฟล์");
    }
}
