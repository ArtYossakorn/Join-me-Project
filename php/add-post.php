<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <title>เพิ่มกิจกรรม</title>
</head>

<body>
    <!-- Include Menu -->
    <?php include '../includes/menu.php'; ?>

    <section class="dashboard">
        <!-- Top Bar -->
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>

            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>

            <a href="profile.php">
            <a href="profile.php"><img src="<?= htmlspecialchars($profileImage); ?>" alt=""></a>
            </a>
        </div>

        <!-- Dashboard Content -->
        <div class="dash-content">
            <div class="title">
                <i class="uil uil-focus-add"></i>
                <span class="text">เพิ่มโพสต์</span>
            </div>

            <!-- Container -->
            <div class="container">
                <h2><span class="text">เพิ่มโพสต์</span></h2>
                <a href="home-page.php">
                    <button type="button" class="btn btn-danger">ย้อนกลับ</button>
                </a>
            </div>

            <!-- Add Post Form -->
            <div class="container-add-post">
                <form action="add-post-action.php" method="POST" enctype="multipart/form-data" class="add-activity-form" id="createPostForm">

                    <!-- Activity Type -->
                    <div class="form-group">
                        <label for="postType">ประเภทโพสต์ :</label>
                        <select id="postType" name="postType" required>
                            <option value="" disabled selected>เลือกประเภทโพสต์</option>
                            <option value="ประชาสัมพันธ์">ประชาสัมพันธ์</option>
                            <option value="กิจกรรม">กิจกรรม</option>
                        </select>
                    </div>

                    <!-- Activity Type -->
                    <div class="form-group">
                        <label for="activityType">ประเภทกิจกรรม :</label>
                        <select id="activityType" name="activityType" required>
                            <option value="" disabled selected>เลือกประเภทกิจกรรม</option>
                            <option value="การศึกษา">การศึกษา</option>
                            <option value="กีฬา">กีฬา</option>
                            <option value="บันเทิง">บันเทิง</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>

                    <!-- Activity Description -->
                    <div class="form-group">
                        <label for="description">รายละเอียดของกิจกรรม :</label>
                        <textarea id="description" name="description" rows="5" placeholder="กรุณาใส่รายละเอียดกิจกรรม" required></textarea>
                    </div>

                    <!-- Activity Media Upload -->
                    <label for="activity-media">
                        อัพโหลดรูปภาพ หรือ วีดิโอ :
                    </label>
                    <div class="container-add-image">
                        <div class="form-group">
                            <div class="file-upload-wrapper">
                                <input type="file" id="images" name="images[]" accept="image/*,video/*" multiple>
                                <label for="images" class="file-upload-icon">
                                    <i class="uil uil-image-plus" alt="Upload"></i> <!-- ไอคอนแสดงที่นี่ -->
                                </label>
                            </div>
                        </div>

                        <!-- Image/Video Preview Container -->
                        <div class="image-preview-container" id="image-preview-container"></div>
                    </div>

                    <!-- Activity Name -->
                    <div class="form-group" id="postNameGroup" style="display: none;">
                        <label for="postName">ชื่อกิจกรรม :</label>
                        <input type="text" id="postName" name="postName" placeholder="กรุณาใส่ชื่อกิจกรรม" >
                    </div>

                    <!-- Activity Location -->
                    <div class="form-group" id="locationGroup" style="display: none;">
                        <label for="location">สถานที่จัดกิจกรรม :</label>
                        <input type="text" id="location" name="location" placeholder="กรุณาใส่สถานที่" >
                    </div>

                    <!-- วันที่จัดกิจกรรม -->
                    <div class="form-group" id="dateGroup" style="display: none;">
                        <label for="date">วันที่จัดกิจกรรม :</label>
                        <input type="text" id="date" name="date" placeholder="เลือกวันที่จัดกิจกรรม" >
                    </div>

                    <!-- Activity Start Time -->
                    <div class="form-group" id="startTimeGroup" style="display: none;">
                        <label for="startTime">เวลาเริ่มต้น :</label>
                        <input type="text" id="startTime" name="startTime" placeholder="เลือกเวลาเริ่มต้น" >
                    </div>

                    <!-- Activity End Time -->
                    <div class="form-group" id="endTimeGroup" style="display: none;">
                        <label for="endTime">เวลาสิ้นสุด :</label>
                        <input type="text" id="endTime" name="endTime" placeholder="เลือกเวลาสิ้นสุด" >
                    </div>

                    <!-- Participant Limit -->
                    <div class="form-group" id="limit-group" style="display: none;">
                        <label for="limit">กำหนดจำนวนผู้เข้าร่วมกิจกรรม :</label>
                        <input type="text" id="limit" name="limit" min="1" placeholder="กรุณาใส่จำนวน">
                    </div>


                    <!-- Submit Button -->
                    <!-- <button type="submit" class="btn-submit" name="create-post-btn">สร้างโพสต์</button> -->
                    <button type="submit" class="btn-submit" name="create-post-btn" onclick="confirmCreatePost(event)">สร้างโพสต์</button>
                </form>
            </div>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_image_preview.js"></script>
    <script src="../script/script_confirm.js"></script>
    <script src="../script/script_addPost.js"></script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script src="../script/script_dateAndTime.js"></script>

</body>

</html>