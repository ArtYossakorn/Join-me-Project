// JavaScript function to handle the click event and send postKey via AJAX
function viewPostDetails(postKey) {
    var formData = new FormData();
    formData.append('postID', postKey);

    fetch('post-detail.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Display the post details or handle response as needed
        document.getElementById('post-details-container').innerHTML = data;
    })
    .catch(error => console.error('Error:', error));
}