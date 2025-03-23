document.addEventListener("DOMContentLoaded", function () {
    // Select all galleries on the page
    const galleries = document.querySelectorAll('.image-gallery');

    // Loop through each gallery
    galleries.forEach(gallery => {
        const images = gallery.querySelectorAll('img');
        const prevButton = gallery.querySelector('.prev');
        const nextButton = gallery.querySelector('.next');

        let currentIndex = 0;

        // Show the current image
        function showImage(index) {
            images.forEach((img, i) => {
                img.style.display = (i === index) ? 'block' : 'none';
            });

            // Hide prev button if it's the first image
            if (index === 0) {
                prevButton.style.visibility = 'hidden'; // Hide prev button
            } else {
                prevButton.style.visibility = 'visible'; // Show prev button
            }

            // Hide next button if it's the last image
            if (index === images.length - 1) {
                nextButton.style.visibility = 'hidden'; // Hide next button
            } else {
                nextButton.style.visibility = 'visible'; // Show next button
            }
        }

        // Show the next image
        nextButton.addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        });

        // Show the previous image
        prevButton.addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        });

        // Initial image display
        showImage(currentIndex);
    });
});
