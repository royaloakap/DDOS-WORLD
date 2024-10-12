<?php
session_start();
$_SESSION['login'] = false;
unset($_SESSION['login'] );
unset($_SESSION['admin_name'] );
header("Location: login.php");
/*
 * <div class="row box" style="width:1002px;direction: rtl">
                <div class="col-md-6">
                    <h4>الاصدار الثاني</h4>
                    <h4><a href="http://baha2.in/">Baha'a Odeh</a></h4>
                    <h4><a href="http://hloun.com">Hloun.com</a></h4>
                </div>
                <div class="col-md-6">
                    <h4>سكربت ليون بوست</h4>
                    <h4>برمجة</h4>
                    <h4>الموقع الرسمي</h4>
                </div>        
            </div>
 */
?>
