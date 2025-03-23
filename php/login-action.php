<?php

use Firebase\Auth\Token\Exception\InvalidToken;

session_start();
include '../config/dbconfig.php';  // Make sure dbconfig.php is set up correctly.

if (isset($_POST['login-btn'])) {
    $email = $_POST['email'];
    $clearTextPassword = $_POST['password'];

    try {
        // Firebase Authentication: Sign in with email and password
        $signInResult = $auth->signInWithEmailAndPassword($email, $clearTextPassword);
        $idTokenString = $signInResult->idToken();

        try {
            // Verify the ID token to ensure it's valid
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->claims()->get('sub'); // User ID

            // Store session variables
            $_SESSION['verified_user_id'] = $uid;
            $_SESSION['idTokenString'] = $idTokenString;

            // Fetch user data from Firebase Realtime Database using the user's UID
            $ref_table = "users/" . $uid;  // Access user data using their UID
            $snapshot = $database->getReference($ref_table)->getSnapshot();
            $userData = $snapshot->getValue();  // Get the user's data

            // Check if user data is available and store it in session
            if ($userData) {
                $_SESSION['users'] = $userData; // Store user data from Realtime Database
            }

            // Set session message for successful login
            $_SESSION['status'] = "Logged in Successfully";

            // Redirect to home page
            header("Location: home-page.php");
            exit();
        } catch (InvalidToken $e) {
            echo 'The token is invalid: ' . $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            echo 'The token could not be parsed: ' . $e->getMessage();
        }
    } catch (Exception $e) {
        // Handle authentication errors
        if ($e instanceof \Kreait\Firebase\Exception\Auth\UserNotFound) {
            $_SESSION['status'] = "Invalid Email Address";
        } else {
            $_SESSION['status'] = "Wrong Password";
        }
        header("Location: login.php");
        exit();
    }
}
