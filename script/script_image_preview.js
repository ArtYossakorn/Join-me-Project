// Image/Video Preview Logic
const fileInput = document.getElementById('images');
const previewContainer = document.getElementById('image-preview-container');

fileInput.addEventListener('change', () => {
    // Clear previous previews
    previewContainer.innerHTML = '';

    // Loop through selected files
    Array.from(fileInput.files).forEach(file => {
        const filePreview = document.createElement('div');
        filePreview.classList.add('image-item');

        if (file.type.startsWith('image/')) {
            // If the file is an image
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'image-preview';
            filePreview.appendChild(img);
        } else if (file.type.startsWith('video/')) {
            // If the file is a video
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.className = 'video-preview';
            video.controls = true;  // Add controls to allow play, pause, etc.
            filePreview.appendChild(video);
        }

        // Create remove button for image/video preview
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'remove-image';
        removeButton.textContent = 'X';
        removeButton.addEventListener('click', () => {
            // Remove the file preview container
            filePreview.remove();
        });

        filePreview.appendChild(removeButton);
        previewContainer.appendChild(filePreview);
    });
});

// Example removeImage function (if needed for server-side removal):
function removeImage(imagePath) {
    // Find the container of the image to be removed
    var imagePreview = document.querySelector(`img[src='${imagePath}']`).parentElement;

    // Send AJAX request to remove the image from the server (Optional)
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "remove_image.php", true); // Create remove_image.php to handle image deletion
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("imagePath=" + encodeURIComponent(imagePath));

    // Remove the image preview from the DOM
    imagePreview.remove();
}
