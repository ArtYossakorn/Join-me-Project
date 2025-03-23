<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">


    <title>แก้ไขข้อมูลสมาชิก</title>
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
                <span class="text">แก้ไขข้อมูลสมาชิก</span>
            </div>

            <div class="container">
                <h2>
                    <span class="text">แก้ไขข้อมูลสมาชิก</span>
                </h2>
                <a href="user-manage.php"><button type="submit" name="back" class="btn btn-danger">ย้อนกลับ</button></a>
            </div>

            <div class="container-form">
                <?php
                include '../config/dbconfig.php';

                $ref_table = "users";
                $id = $_GET['userID'];
                $editdata = $database->getReference($ref_table)->getChild($id)->getValue();

                ?>

                <form action="edit-user-action.php" method="POST">
                    <div class="form-group">
                        <label for="userRoles">บทบาท</label>
                        <select name="userRoles" id="userRoles">
                            <option value="Admin" <?= ($editdata['userRoles'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="User" <?= ($editdata['userRoles'] == 'User') ? 'selected' : '' ?>>User</option>
                            <option value="Departmentcs" <?= ($editdata['userRoles'] == 'Departmentcs') ? 'selected' : '' ?>>DepartmentCS</option>
                            <option value="Departmentads" <?= ($editdata['userRoles'] == 'Departmentads') ? 'selected' : '' ?>>DepartmentADS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statusPost">สิทธิในการโพสต์โพสต์</label>
                        <select name="statusPost" id="statusPost">
                            <option value="true" <?= ($editdata['statusPost'] == 'true') ? 'selected' : '' ?>>มีสิทธิ์ในการโพสต์</option>
                            <option value="false" <?= ($editdata['statusPost'] == 'false') ? 'selected' : '' ?>>ไม่มีสิทธ์ในการโพสต์</option>
                        </select>
                    </div>
                    <?php
                    if (isset($_GET['userID'])) {
                        $uid = $_GET['userID'];
                        try {
                            $user = $auth->getUser($uid);
                            $isDisabled = $user->disabled ? "disable" : "enable";
                    ?>
                            <input type="hidden" name="ena_dis_user_id" value="<?= $uid; ?>">
                            <div class="form-group">
                                <label>สิทธิในการเข้าสู่ระบบ</label>
                                <select name="statusLogin" id="statusLogin">
                                    <option value="enable" <?= ($isDisabled == "enable") ? 'selected' : '' ?>>มีสิทธิ์ในการเข้าสู่ระบบ</option>
                                    <option value="disable" <?= ($isDisabled == "disable") ? 'selected' : '' ?>>ไม่มีสิทธิ์ในการเข้าสู่ระบบ</option>
                                </select>
                            </div>
                    <?php
                        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                            echo $e->getMessage();
                            $isDisabled = "unknown";
                        }
                    } else {
                        echo "No User id Found";
                    }
                    ?>
                    <div class="form-group">
                        <label for="email">อีเมล</label>
                        <input type="text" name="email" id="email" value="<?= $editdata['email'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="studentID">รหัสนิสิต</label>
                        <input type="text" name="studentID" id="studentID" value="<?= $editdata['studentID'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="title">คำนำหน้า</label>
                        <select name="title" id="title">
                            <option value="นาย" <?= ($editdata['title'] == 'นาย') ? 'selected' : '' ?>>นาย</option>
                            <option value="นางสาว" <?= ($editdata['title'] == 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                            <option value="Mr." <?= ($editdata['title'] == 'Mr.') ? 'selected' : '' ?>>Mr.</option>
                            <option value="Miss" <?= ($editdata['title'] == 'Miss') ? 'selected' : '' ?>>Miss</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="firstName">ชื่อ</label>
                        <input type="text" name="firstName" id="firstName" value="<?= $editdata['firstName'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastName">นามสกุล</label>
                        <input type="text" name="lastName" id="lastName" value="<?= $editdata['lastName'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="department">คณะ</label>
                        <select name="department" id="department" value="<?= $editdata['department'] ?>">
                            <option value="IT">วิทยาการสารสนเทศ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="major">สาขา</label>
                        <select name="major" id="major">
                            <option value="cs" <?= ($editdata['major'] == 'cs') ? 'selected' : '' ?>>วิทยาการคอมพิวเตอร์ (CS)</option>
                            <option value="cs inter" <?= ($editdata['major'] == 'cs inter') ? 'selected' : '' ?>>วิทยาการคอมพิวเตอร์ หลักสูตรนานาชาติ (CS-INTER)</option>
                            <option value="ads" <?= ($editdata['major'] == 'ads') ? 'selected' : '' ?>>วิทยาการข้อมูลประยุกต์ (ADS)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">เบอร์โทรศัพท์</label>
                        <input type="text" name="phoneNumber" id="phoneNumber" value="<?= $editdata['phoneNumber'] ?>">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="userID" value="<?= $id ?>">
                        <button type="submit" name="edit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_confirm.js" defer></script>
</body>

</html>