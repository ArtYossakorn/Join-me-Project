<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/top_style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <title>จัดการข้อมูลสมาชิก</title>
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
                <span class="text"> จัดการข้อมูลสมาชิก </span>
            </div>

            <?php
            if (isset($_SESSION['status'])) {
                echo "<h5 class='alert'>" . $_SESSION['status'] . "</h5>";
                unset($_SESSION['status']);
            }
            ?>

            <div id="alertMessage" style="display:none; padding: 10px; color: white; background-color: green;">
                ข้อมูลผู้ใช้ถูกบันทึกเรียบร้อยแล้ว!
            </div>

            <div class="container-manage">
                <h2>
                    <span class="text"> จัดการข้อมูลสมาชิก </span>
                </h2>
                <!-- <form id="importForm">
                    <input type="file" id="excelFile" accept=".xlsx, .xls" />
                    <button type="submit"> นำเข้าข้อมูลผู้ใช้ </button>
                </form> -->
                <a href="add-user.php">
                    <button> เพิ่มสมาชิก </button>
                </a>
            </div>

            <div class="container-table">
                <div class="table-content">
                    <table>
                        <thead>
                            <tr>
                                <th> ลำดับ </th>
                                <th> บทบาท </th>
                                <th> รหัสนิสิต </th>
                                <th> คำนำหน้า </th>
                                <th> ชื่อ </th>
                                <th> นามสกุล </th>
                                <th> คณะ </th>
                                <th> สาขา </th>
                                <th> เบอร์โทรศัพท์ </th>
                                <th> สิทธิในการโพสต์ </th>
                                <th> สิทธิในการเข้าสู่ระบบ </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../config/dbconfig.php';

                            // ตรวจสอบว่า userRoles ของผู้ที่ล็อกอินเป็น 'Departmentcs' หรือไม่
                            $currentUser = $auth->getUser($uid); // $uid คือ ID ของผู้ใช้ที่ล็อกอิน
                            $userRoles = $currentUser->customClaims['userRoles'] ?? '';

                            if ($userRoles === 'Departmentads') {
                                // กรองข้อมูลเฉพาะผู้ใช้ที่ major เป็น 'cs' หรือ 'cs inter'
                                $ref_table = 'users';
                                $fetchdata = $database->getReference($ref_table)->getValue();

                                if ($fetchdata > 0) {
                                    uasort($fetchdata, function ($a, $b) {
                                        return strcmp($a['studentID'], $b['studentID']);
                                    });

                                    $i = 1;
                                    foreach ($fetchdata as $key => $row) {
                                        // กรองเฉพาะ major ที่เป็น 'cs' หรือ 'cs inter'
                                        if (in_array($row['major'], ['ads'])) {
                                            // แปลบทบาท
                                            $roles = [
                                                'User' => 'ผู้ใช้ทั่วไป',
                                                'Admin' => 'แอดมิน',
                                                'Departmentcs' => 'ภาควิชาวิทยาการคอมพิวเตอร์',
                                                'Departmentads' => 'ภาควิชาวิทยาการข้อมูล'
                                            ];
                                            $userRoles = $roles[$row['userRoles']] ?? 'ไม่มีบทบาท';

                                            $statusP = [
                                                'true' => 'มีสิทธิ์',
                                                'false' => 'ไม่มีสิทธิ์'
                                            ];
                                            $statusPost = $statusP[$row['statusPost']] ?? 'ไม่ระบุ';

                                            // ✅ ดึงค่า disabled จาก Firebase Authentication สำหรับแต่ละผู้ใช้
                                            try {
                                                $userAuth = $auth->getUser($key);
                                                $isDisabled = $userAuth->disabled ? "ไม่มีสิทธิ์" : "มีสิทธิ์";
                                            } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                                                $isDisabled = "Unknown"; // ถ้าหาผู้ใช้ไม่เจอ
                                            }

                                            echo "<tr>
                                                    <td>{$i}</td>
                                                    <td>{$userRoles}</td>
                                                    <td>{$row['studentID']}</td>
                                                    <td>{$row['title']}</td>
                                                    <td>{$row['firstName']}</td>
                                                    <td>{$row['lastName']}</td>
                                                    <td>{$row['department']}</td>
                                                    <td>{$row['major']}</td>
                                                    <td>{$row['phoneNumber']}</td>
                                                    <td>{$statusPost}</td>
                                                    <td>{$isDisabled}</td>
                                                    <td><a href='edit-user.php?userID={$key}'><button>Edit</button></a></td>
                                                </tr>";
                                            $i++;
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='12'>No Record Found</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='12'>คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script src="../script/script_sidebar.js"></script>
    <script src="../script/script_import.js"></script>
</body>

</html>