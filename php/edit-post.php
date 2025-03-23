<?php
include '../config/dbconfig.php';

$ref_table = "post";
$id = $_GET['postID'];
$editdata = $database->getReference($ref_table)->getChild($id)->getValue();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>แก้ไขโพสต์</title>
</head>

<body>
    <?php include '../includes/menu.php'; ?>

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
                <i class="uil uil-edit"></i>
                <span class="text">แก้ไขโพสต์</span>
            </div>

            <div class="container">
                <h2><span class="text">แก้ไขโพสต์</span></h2>
                <a href="home-page.php">
                    <button type="button" class="btn btn-danger">ย้อนกลับ</button>
                </a>
            </div>

            <div class="container-add-post">
                <form action="edit-post-action.php" method="POST" enctype="multipart/form-data" class="add-activity-form">
                    <input type="hidden" name="postID" value="<?= $id ?>">

                    <div class="form-group">
                        <label for="postType">ประเภทโพสต์ :</label>
                        <select id="postType" name="postType" required>
                            <option value="ประชาสัมพันธ์" <?= ($editdata['postType'] == 'ประชาสัมพันธ์') ? 'selected' : '' ?>>ประชาสัมพันธ์</option>
                            <option value="กิจกรรม" <?= ($editdata['postType'] == 'กิจกรรม') ? 'selected' : '' ?>>กิจกรรม</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="activityType">ประเภทกิจกรรม :</label>
                        <select id="activityType" name="activityType" required>
                            <option value="การศึกษา" <?= ($editdata['activityType'] == 'การศึกษา') ? 'selected' : '' ?>>การศึกษา</option>
                            <option value="กีฬา" <?= ($editdata['activityType'] == 'กีฬา') ? 'selected' : '' ?>>กีฬา</option>
                            <option value="บันเทิง" <?= ($editdata['activityType'] == 'บันเทิง') ? 'selected' : '' ?>>บันเทิง</option>
                            <option value="อื่นๆ" <?= ($editdata['activityType'] == 'อื่นๆ') ? 'selected' : '' ?>>อื่นๆ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">รายละเอียดของกิจกรรม :</label>
                        <textarea id="description" name="description" rows="5" required><?= $editdata['description'] ?></textarea>
                    </div>

                    <label for="activity-media">อัพโหลดรูปภาพ หรือ วีดิโอ :</label>
                    <div class="container-add-image">
                        <div class="form-group">
                            <div class="file-upload-wrapper">
                                <input type="file" id="images" name="images[]" accept="image/*,video/*" multiple>
                                <label for="images" class="file-upload-icon">
                                    <i class="uil uil-image-plus"></i>
                                </label>
                            </div>
                        </div>

                        <!-- แสดงตัวอย่างรูปที่อัปโหลดไว้ก่อน -->
                        <?php
                        if (!empty($_FILES['images']['name'][0])) {
                            $uploadDir = 'uploads/'; // ตั้งค่าโฟลเดอร์เก็บไฟล์
                            $existingImages = isset($editdata['images']) ? json_decode($editdata['images'], true) : []; // โหลดภาพเก่าจาก DB
                            $newImages = [];

                            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                                $fileName = basename($_FILES['images']['name'][$key]);
                                $targetPath = $uploadDir . $fileName;

                                if (move_uploaded_file($tmpName, $targetPath)) {
                                    $newImages[] = $targetPath; // เพิ่มรูปใหม่เข้าไป
                                }
                            }

                            // รวมรูปเก่ากับรูปใหม่
                            $allImages = array_merge($existingImages, $newImages);

                            // อัปเดตฐานข้อมูลโดยไม่ลบรูปเก่า
                            if (!empty($newImages)) {
                                $imageJson = json_encode($allImages);
                                $query = "UPDATE posts SET images = '$imageJson' WHERE id = '$postID'";
                                mysqli_query($conn, $query);

                                // อัปเดต $editdata['images'] เพื่อแสดงผลทันที
                                $editdata['images'] = $imageJson;
                            }
                        }

                        // ใช้ค่าล่าสุดจากฐานข้อมูล
                        $images = isset($editdata['images']) ? json_decode($editdata['images'], true) : [];

                        if (!empty($images)): ?>
                            <div class="image-gallery">
                                <?php foreach ($images as $imagePath): ?>
                                    <div class="image-preview" id="image-preview-<?= htmlspecialchars($imagePath) ?>">
                                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="Uploaded Image">
                                        <button type="button" class="remove-image" onclick="removeImage('<?= htmlspecialchars($imagePath) ?>')">X</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <?php if ($editdata['postType'] === "กิจกรรม") { ?>
                        <div class="form-group">
                            <label for="postName">ชื่อกิจกรรม :</label>
                            <input type="text" id="postName" name="postName" value="<?= $editdata['postName'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="location">สถานที่จัดกิจกรรม :</label>
                            <input type="text" id="location" name="location" value="<?= $editdata['location'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="date">วันที่จัดกิจกรรม :</label>
                            <input type="text" id="date" name="date" value="<?= $editdata['date'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="startTime">เวลาเริ่มต้น :</label>
                            <input type="text" id="startTime" name="startTime" value="<?= $editdata['startTime'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="endTime">เวลาสิ้นสุด :</label>
                            <input type="text" id="endTime" name="endTime" value="<?= $editdata['endTime'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="limit">กำหนดจำนวนผู้เข้าร่วมกิจกรรม :</label>
                            <input type="text" id="limit" name="limit" value="<?= $editdata['limit'] ?>" required>
                        </div>
                    <?php } ?>

                    <button type="submit" class="btn-submit" name="edit-post-btn">บันทึกการแก้ไข</button>
                </form>
            </div>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_image_preview.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script src="../script/script_dateAndTime.js"></script>

</body>

</html>