<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/logo.png" sizes="32x32">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <title>Join Me</title>
</head>

<body>
    <?php
    include '../includes/menu.php';
    ?>

    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>

            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>

            <a href="profile.php"><img src="<?= htmlspecialchars($profileImage); ?>" alt=""></a>
        </div>

        <div class="dash-content">
            <div class="title">
                <i class="uil uil-estate"></i>
                <span class="text">รายละเอียดโพสต์</span>
            </div>
        </div>

        <?php
        if (isset($_SESSION['status'])) {
            echo "<h5 class='alert success'>" . $_SESSION['status'] . "</h5>";
            unset($_SESSION['status']);
        }
        ?>

        <div class="post-content">
            <?php
            include '../config/dbconfig.php';
            $uid = $_SESSION['verified_user_id'];

            // Fetch the postID from the URL or set to null if not provided
            $requestedPostID = isset($_GET['postID']) ? $_GET['postID'] : null;

            // Fetch posts from the "post" node
            $ref_table_post = 'post';
            $fetchPosts = $database->getReference($ref_table_post)->getValue();

            // Fetch users from the "users" node
            $ref_table_users = 'users';
            $fetchUsers = $database->getReference($ref_table_users)->getValue();

            if ($fetchPosts && $fetchUsers) {
                // Loop through the posts
                foreach (array_reverse($fetchPosts) as $postKey => $row) {
                    // If the postKey matches the requested postID, display this post
                    if ($requestedPostID === null || $postKey == $requestedPostID) {

                        // Create DateTime object for the post time
                        $date = new DateTime($row['postTime'], new DateTimeZone('Asia/Bangkok'));  // ตั้งโซนเวลาตรงนี้

                        // Set Thai locale
                        $locale = 'th_TH';

                        // Create IntlDateFormatter
                        $formatter = new IntlDateFormatter(
                            $locale,
                            IntlDateFormatter::NONE,  // No time format
                            IntlDateFormatter::LONG,  // Long month name
                            'Asia/Bangkok',
                            IntlDateFormatter::GREGORIAN,
                            "d MMMM"
                        );

                        // Format the date
                        $formattedDate = $formatter->format($date);

                        // แปลบทบาท
                        $roles = [
                            'User' => 'ผู้ใช้ทั่วไป',
                            'Admin' => 'แอดมิน',
                            'Departmentcs' => 'ภาควิชาวิทยาการคอมพิวเตอร์',
                            'Departmentads' => 'ภาควิชาวิทยาการข้อมูล'
                        ];
                        $userRoles = $roles[$row['userRoles']] ?? 'ไม่มีบทบาท';
            ?>
                        <div class="post-container">
                            <?php
                            // ดึงข้อมูลโปรไฟล์ของผู้โพสต์จาก users table โดยใช้ userID
                            $userID = $row['userID']; // ดึง userID จากโพสต์
                            if (isset($fetchUsers[$userID])) {
                                $userProfile = $fetchUsers[$userID]; // ดึงข้อมูลผู้ใช้งานจาก fetchUsers
                                $userProfileImage = isset($userProfile['profileImage']) ? $userProfile['profileImage'] : '../img/default-profile.png'; // ตรวจสอบว่ามีภาพโปรไฟล์หรือไม่
                            } else {
                                $userProfileImage = '../img/default-profile.png'; // ใช้ภาพโปรไฟล์เริ่มต้นหากไม่พบ
                            }
                            ?>

                            <div class="post-details">
                                <!-- แสดงภาพโปรไฟล์ของผู้โพสต์ -->
                                <img class="user-image" src="<?= htmlspecialchars($userProfileImage); ?>" alt="Profile">
                                <p class="username"><?= htmlspecialchars($row['userPost']); ?></p>
                                <div class="post-meta">
                                    <span><?= htmlspecialchars($row['postType']); ?> |</span>
                                    <span><?= htmlspecialchars($row['activityType']); ?> </span>
                                    <?php if ($row['postType'] === "กิจกรรม") { ?>
                                        <span class="limit">| <?= htmlspecialchars($row['limit']); ?> คน</span>
                                    <?php } ?>
                                </div>

                                <?php
                                $userRoles1 = isset($customClaims['userRoles']) ? $customClaims['userRoles'] : 'ไม่ระบุบทบาท';

                                // ตรวจสอบว่า ผู้ใช้งานที่ล็อกอินเป็นผู้โพสต์หรือแอดมิน
                                if (($uid === $userID) || ($userRoles1 === 'Admin')) {
                                ?>
                                    <div class="dropdown">
                                        <button class="dropbtn">. . .</button>
                                        <div class="dropdown-content">
                                            <a href="edit-post.php?postID=<?= $postKey ?>">แก้ไข</a>
                                            <a href="javascript:void(0);" onclick="confirmDelete('<?= $postKey ?>')">ลบ</a>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>

                            <div class="post-meta-foot">
                                <span class="user-role"><?= htmlspecialchars($userRoles); ?></span>
                                <span class="post-date"><?= $formattedDate; ?></span>
                            </div>

                            <?php if ($row['postType'] === "กิจกรรม") { ?>
                                <p><b>ชื่อกิจกรรม: </b> <?= htmlspecialchars($row['postName']); ?></p>
                            <?php } ?>
                            <p><b>รายละเอียด: </b> <?= htmlspecialchars($row['description']); ?></p>
                            <?php if ($row['postType'] === "กิจกรรม") { ?>
                                <p><b>สถานที่จัดกิจกรรม:</b> <?= htmlspecialchars($row['location']); ?></p>
                                <p><b>วันที่จัดกิจกรรม:</b> <?= htmlspecialchars($row['date']); ?></p>
                                <p><b>เวลาจัดกิจกรรม:</b> <?= htmlspecialchars($row['startTime']); ?> ถึง <?= htmlspecialchars($row['endTime']); ?> น.</p>
                            <?php } ?>

                            <?php
                            // Check if images exist
                            if (!empty($row['images'])) {
                                // If images are in JSON format, decode them
                                $images = is_array($row['images']) ? $row['images'] : json_decode($row['images'], true);

                                // Set gallery class based on image count
                                $galleryClass = count($images) === 1 ? 'single-image' : 'multiple-images';

                                // Start the image gallery container with data-post-id
                                echo '<div class="image-gallery ' . $galleryClass . '" data-post-id="' . htmlspecialchars($postKey) . '">';

                                // Display all images in hidden state initially
                                foreach ($images as $index => $imageUrl) {
                                    $displayStyle = $index === 0 ? 'block' : 'none'; // Show first image only
                                    echo '<img src="' . htmlspecialchars($imageUrl) . '" alt="Image" data-index="' . $index . '" style="display: ' . $displayStyle . ';">';
                                    echo '</a>';
                                }

                                if (count($images) > 1) {
                                    echo '<div class="gallery-buttons">
                                            <button class="prev">&#10094;</button>
                                            <button class="next">&#10095;</button>
                                        </div>';
                                }

                                echo '</div>';
                            } else {
                                echo '<p>No images available.</p>';
                            }
                            ?>

                            <?php
                            if ($row['postType'] === "กิจกรรม") {
                            ?>
                                <div class="foot-button" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <form action="register-action.php" method="POST" style="text-align: left; width: 50%;">
                                        <input type="hidden" name="postID" value="<?= $postKey ?>">
                                        <button type="submit" class="btn-submit" name="reg-btn" style="display: inline-block; margin: 0px;">
                                            ลงชื่อเข้าร่วมกิจกรรม
                                        </button>
                                    </form>

                                    <!-- Facebook Share Button -->
                                    <a href="#" id="shareBtn" style="margin-top: -15px;">
                                        <button class="btn-submit"> แชร์ไปที่ Facebook</button>
                                    </a>
                                </div>

                            <?php
                            }
                            ?>
                        </div>
                <?php
                    }
                }
            } else {
                ?>
                <p>No Post Found</p>
            <?php
            }
            ?>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_confirm.js"></script>
    <script src="../script/script_imageSlide.js"></script>
    <script>
        document.getElementById('shareBtn').addEventListener('click', function() {
            var currentUrl = window.location.href; // Get the current page URL
            var facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(currentUrl);
            window.open(facebookUrl, '_blank'); // Open the share dialog in a new tab
        });
    </script>
</body>

</html>