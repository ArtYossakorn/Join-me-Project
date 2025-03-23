<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">


    <title>เพิ่มรายชื่อสมาชิก</title>
</head>

<body>
    <?php include '../includes/menu.php' ?>

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
                <i class="uil uil-users-alt"></i>
                <span class="text">เพิ่มรายชื่อสมาชิก</span>
            </div>

            <div class="container">
                <h2>
                    <span class="text">เพิ่มรายชื่อสมาชิก</span>
                </h2>
                <a href="user-manage.php"><button type="submit" name="back" class="btn btn-danger">ย้อนกลับ</button></a>
            </div>

            <div class="container-form">
                <form action="add-user-action.php" method="POST" enctype="multipart/form-data">
                    <!-- <div class="form-group">
                        <label for="userRoles">บทบาท</label>
                        <select name="userRoles" id="userRoles">
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                            <option value="Department">Department</option>
                        </select>
                    </div> -->
                    <!-- ส่วนสำหรับแสดงตัวอย่างรูปภาพที่เลือก -->
                    <div class="form-group profile-container">
                        <img id="profileImagePreview" src="#" alt="โปรไฟล์รูปภาพ">
                    </div>

                    <div class="form-group profile-upload">
                        <label for="profileImage">รูปโปรไฟล์</label>
                        <input type="file" name="profileImage" id="profileImage" accept="image/*" onchange="previewImage()">
                    </div>

                    <div class="form-group">
                        <label for="email">อีเมล</label>
                        <input type="text" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="studentID">รหัสนิสิต</label>
                        <input type="text" name="studentID" id="studentID">
                    </div>
                    <div class="form-group">
                        <label for="title">คำนำหน้า</label>
                        <select name="title" id="title">
                            <option value="นาย">นาย</option>
                            <option value="นางสาว">นางสาว</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Miss">Miss</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="firstName">ชื่อ</label>
                        <input type="text" name="firstName" id="firstName">
                    </div>
                    <div class="form-group">
                        <label for="lastName">นามสกุล</label>
                        <input type="text" name="lastName" id="lastName">
                    </div>
                    <div class="form-group">
                        <label for="department">คณะ</label>
                        <select name="department" id="department">
                            <option value="IT">วิทยาการสารสนเทศ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="major">สาขา</label>
                        <select name="major" id="major">
                            <option value="cs">วิทยาการคอมพิวเตอร์ (CS)</option>
                            <option value="cs inter">วิทยาการคอมพิวเตอร์ หลักสูตรนานาชาติ (CS-INTER)</option>
                            <option value="ads">วิทยาการข้อมูลประยุกต์ (ADS)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">เบอร์โทรศัพท์</label>
                        <input type="text" name="phoneNumber" id="phoneNumber">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="save" class="btn btn-primary">เพิ่มข้อมูลผู้ใช้</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_confirm.js"></script>
    <script src="../script/script_image-profile-previwe.js"></script>
</body>

</html>