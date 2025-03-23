// Initialize Firebase (make sure you've already initialized Firebase in your HTML)
const firebaseConfig = {
    apiKey: "AIzaSyCMyqsSw0z5pGJj0ck2eKBENjgCZ6zgZwY",
    authDomain: "join-me-project.firebaseapp.com",
    databaseURL: "https://join-me-project-default-rtdb.firebaseio.com",
    projectId: "join-me-project",
    storageBucket: "join-me-project.firebasestorage.app",
    messagingSenderId: "742799431068",
    appId: "1:742799431068:web:6706644dab8c812e0ba459",
    measurementId: "G-7EPE7DYSRG"
};

firebase.initializeApp(firebaseConfig);
const database = firebase.database();
const auth = firebase.auth();

// Function to read the Excel file
document.getElementById('importForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const fileInput = document.getElementById('excelFile');
    const file = fileInput.files[0];
    if (!file) {
        alert('Please select an Excel file');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const data = e.target.result;
        const workbook = XLSX.read(data, { type: 'binary' });

        // Get the first sheet in the workbook
        const sheetName = workbook.SheetNames[0];
        const sheet = workbook.Sheets[sheetName];

        // Convert the sheet to JSON (array of arrays)
        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        // Iterate through the rows and insert data into Firebase
        jsonData.forEach((row, index) => {
            if (index > 0) { // Skip the header row
                const userData = {
                    email: row[0],               // Assuming the first column is email
                    studentID: row[1],           // Assuming the second column is studentID
                    title: row[2],               // Assuming the third column is title
                    firstName: row[3],           // Assuming the fourth column is firstName
                    lastName: row[4],            // Assuming the fifth column is lastName
                    department: row[5],          // Assuming the sixth column is department
                    major: row[6],               // Assuming the seventh column is major
                    phoneNumber: row[7],         // Assuming the eighth column is phoneNumber
                    userRoles: row[8] || 'User', // Assuming the ninth column is userRoles, default to 'User' if empty
                    statusPost: 'false'          // Default statusPost to false
                };

                // Create a new user in Firebase Authentication
                const password = row[1];  // Use studentID as the password for the example
                const email = row[0];

                auth.createUserWithEmailAndPassword(email, password)
                    .then(userCredential => {
                        const userId = userCredential.user.uid;

                        // Insert user data into Firebase Realtime Database
                        const userRef = database.ref('users/' + userId);
                        userRef.set(userData)
                            .then(() => {
                                console.log('User data added successfully');
                            })
                            .catch(error => {
                                console.error('Error adding user data:', error);
                            });

                        // Set custom claims in Firebase Authentication
                        auth.setCustomUserClaims(userId, {
                            title: userData.title,
                            firstName: userData.firstName,
                            lastName: userData.lastName,
                            userRoles: userData.userRoles,
                            studentID: userData.studentID,
                            department: userData.department,
                            major: userData.major,
                            statusPost: userData.statusPost
                        }).then(() => {
                            console.log('Custom claims set successfully');
                        }).catch(error => {
                            console.error('Error setting custom claims:', error);
                        });

                    })
                    .catch(error => {
                        console.error('Error creating user in Firebase Auth:', error);
                    });
            }
        });

        // Show success message
        document.getElementById('alertMessage').style.display = 'block';
    };

    reader.readAsBinaryString(file);
});
