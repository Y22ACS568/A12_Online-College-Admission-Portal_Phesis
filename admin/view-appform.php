<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

/* ADD THIS */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
/* END */

if (strlen($_SESSION['aid']==0)) {
    header('location:logout.php');
} else {

if(isset($_POST['submit']))
{
    $cid=$_GET['aticid'];
    $admrmk=$_POST['AdminRemark'];
    $admsta=$_POST['status'];
    $feeamt=$_POST['feeamt'];
    $toemail=$_POST['useremail'];

    $query=mysqli_query($con, "UPDATE tbladmapplications 
        SET AdminRemark='$admrmk', FeeAmount='$feeamt', AdminStatus='$admsta' 
        WHERE ID='$cid'");

    if ($query) {
        $statusText = ($admsta=="1") ? "SELECTED" : "REJECTED";
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vamsitallika4@gmail.com';
            $mail->Password = 'kjahoqztxlelfebb';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('vamsitallika4@gmail.com','College Admission');
            $mail->addAddress($toemail);

            $mail->isHTML(true);
            $mail->Subject = 'Admission Application Status';

            $body = "
            <h2 style='color:#2c3e50;'>Bapatla Engineering College</h2>
            <p>Hello Student,</p>
            <p>Your admission application has been <b>$statusText</b>.</p>
            <p><strong>Admin Remark:</strong> $admrmk</p>
            ";

            if($admsta == "1"){
                $body .= "<p><strong>Fee Amount:</strong> ₹$feeamt</p>
                <p>Please login to the portal for paying the Fee.</p>";
            }

            $body .= "
            <br>
            <p>For any queries <b>Cell: 08643224244</b></p>
            <p>Thank you,<br>Admission Team</p>
            ";

            $mail->Body = $body;
            $mail->send();

            echo "<script>alert('Status updated & Mail sent');</script>";

        } catch (Exception $e) {
            echo "Mail Error: ".$mail->ErrorInfo;
        }
    } else {
        echo "<script>alert('Something Went Wrong');</script>";
    }
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <title>Bapatla Engineering College Admission Management System || View Form</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="icon" href="../assets/images/favicon/bec.ico" type="image/x-icon" />

    <style>
        .errorWrap {
            padding: 10px;
            margin: 20px 0 0px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<?php include('includes/header.php');?>
<?php include('includes/leftbar.php');?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">View Application Form</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Application Form</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div id="exampl">

<?php
$cid = intval($_GET['aticid']);
$userid = intval($_GET['userid']);

$ret = mysqli_query($con, "SELECT tbladmapplications.*, tbluser.FirstName, tbluser.LastName, tbluser.MobileNumber, tbluser.Email 
FROM tbladmapplications 
INNER JOIN tbluser ON tbluser.ID = tbladmapplications.UserId 
WHERE tbladmapplications.ID='$cid' OR tbladmapplications.UserId='$userid'");

$count = mysqli_num_rows($ret);

if($count==0){ ?>
    <p style="color:red">Not applied Yet</p>
<?php } else {
while ($row=mysqli_fetch_array($ret)) {
?>

<table border="1" width="100%" class="table table-bordered mg-b-0">
    <tr>
        <th>Applicant Name</th>
        <td><?php echo $row['ApplicantName']; ?></td>
        <th>Reg Date</th>
        <td><?php echo $row['CourseApplieddate']; ?></td>
    </tr>

    <tr>
        <th>Course Applied</th>
        <td><?php echo $row['CourseApplied']; ?></td>
        <th>Select Branch</th>
        <td><?php echo $row['Branch']; ?></td>
    </tr>

    <tr>
        <th>Student Mob Number</th>
        <td><?php echo $row['MobileNumber']; ?></td>
        <th>Student Email</th>
        <td><?php echo $row['Email']; ?></td>
    </tr>

    <tr>
        <th>Student Pic</th>
        <td>
            <?php
            $imgPath = "../user/userimages/" . $row['UserPic'];
            if(!empty($row['UserPic']) && file_exists($imgPath)) {
            ?>
                <img src="<?php echo $imgPath; ?>" width="200" height="150">
            <?php } else { ?>
                <img src="../user/userimages/default.png" width="200" height="150">
            <?php } ?>
        </td>
        <th>Father Name</th>
        <td><?php echo $row['FatherName']; ?></td>
    </tr>

    <tr>
        <th>Mother Name</th>
        <td><?php echo $row['MotherName']; ?></td>
        <th>DOB</th>
        <td><?php echo $row['DOB']; ?></td>
    </tr>

    <tr>
        <th>Nationality</th>
        <td><?php echo $row['Nationality']; ?></td>
        <th>Gender</th>
        <td><?php echo $row['Gender']; ?></td>
    </tr>

    <tr>
        <th>Category</th>
        <td><?php echo $row['Category']; ?></td>
        <th>Correspondence Address</th>
        <td><?php echo $row['CorrespondenceAdd']; ?></td>
    </tr>

    <tr>
        <th>Permanent Address</th>
        <td><?php echo $row['PermanentAdd']; ?></td>
        <th>Transfer Certificate</th>
        <td>
            <?php if(!empty($row['TransferCertificate'])) { ?>
                <a href="../user/userdocs/<?php echo $row['TransferCertificate']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

    <tr>
        <th>10th Marksheet</th>
        <td>
            <?php if(!empty($row['TenMarksheeet'])) { ?>
                <a href="../user/userdocs/<?php echo $row['TenMarksheeet']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>

        <th>12th Marksheet / Diploma Marksheet</th>
        <td>
            <?php if(!empty($row['TwelveMarksheet'])) { ?>
                <a href="../user/userdocs/<?php echo $row['TwelveMarksheet']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

<?php
// EAPCET
if(!empty($row['EapcetRankCard']) || !empty($row['EapcetAllotmentOrder'])){
?>
    <tr>
        <th>Entrance Exam</th>
        <td>EAPCET</td>
        <th>Rank Card</th>
        <td>
            <?php if(!empty($row['EapcetRankCard'])){ ?>
                <a href="../user/userdocs/<?php echo $row['EapcetRankCard']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

    <tr>
        <th></th>
        <td></td>
        <th>Allotment Order</th>
        <td>
            <?php if(!empty($row['EapcetAllotmentOrder'])){ ?>
                <a href="../user/userdocs/<?php echo $row['EapcetAllotmentOrder']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

<?php
} elseif(!empty($row['EcetRankCard']) || !empty($row['EcetAllotmentOrder'])){
?>
    <tr>
        <th>Entrance Exam</th>
        <td>ECET</td>
        <th>Rank Card</th>
        <td>
            <?php if(!empty($row['EcetRankCard'])){ ?>
                <a href="../user/userdocs/<?php echo $row['EcetRankCard']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

    <tr>
        <th></th>
        <td></td>
        <th>Allotment Order</th>
        <td>
            <?php if(!empty($row['EcetAllotmentOrder'])){ ?>
                <a href="../user/userdocs/<?php echo $row['EcetAllotmentOrder']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

<?php
} elseif(!empty($row['polycet_rank']) || !empty($row['polycet_allot'])){
?>
    <tr>
        <th>Entrance Exam</th>
        <td>POLYCET</td>
        <th>Rank Card</th>
        <td>
            <?php if(!empty($row['polycet_rank'])){ ?>
                <a href="../user/userdocs/<?php echo $row['polycet_rank']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

    <tr>
        <th></th>
        <td></td>
        <th>Allotment Order</th>
        <td>
            <?php if(!empty($row['polycet_allot'])){ ?>
                <a href="../user/userdocs/<?php echo $row['polycet_allot']; ?>" target="_blank">View File</a>
            <?php } else { echo "Not Uploaded"; } ?>
        </td>
    </tr>

<?php
} else {
?>
    <tr>
        <th>Entrance Exam</th>
        <td colspan="3">No Entrance Exam Documents Uploaded</td>
    </tr>
<?php } ?>
</table>

<table class="table mb-0" border="1" width="100%">
    <tr>
        <th>#</th>
        <th>Board / University</th>
        <th>Year</th>
        <th>Percentage</th>
        <th>Stream</th>
    </tr>

    <tr>
        <th>10th(Secondary)</th>
        <td><?php echo $row['SecondaryBoard']; ?></td>
        <td><?php echo $row['SecondaryBoardyop']; ?></td>
        <td><?php echo $row['SecondaryBoardper']; ?></td>
        <td><?php echo $row['SecondaryBoardstream']; ?></td>
    </tr>

    <tr>
        <th>12th(Senior Secondary)</th>
        <td><?php echo $row['SSecondaryBoard']; ?></td>
        <td><?php echo $row['SSecondaryBoardyop']; ?></td>
        <td><?php echo $row['SSecondaryBoardper']; ?></td>
        <td><?php echo $row['SSecondaryBoardstream']; ?></td>
    </tr>

    <tr>
        <th colspan="5">
            <font color="red">Declaration : </font>
            I hereby state that the facts mentioned above are true to the best of my knowledge and belief.<br />
            (<?php echo $row['Signature']; ?>)
        </th>
    </tr>
</table>

<table class="table mb-0" border="1" width="100%">

<?php if($row['AdminRemark']==""){ ?>
<form name="submit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="useremail" value="<?php echo $row['Email']; ?>">

    <tr>
        <th>Application Status :</th>
        <td>
            <select name="status" id="status" class="form-control wd-450" required>
                <option value="">Select Option</option>
                <option value="1">Selected</option>
                <option value="2">Rejected</option>
            </select>
        </td>
    </tr>

    <tr>
        <th>Admin Remark :</th>
        <td>
            <textarea name="AdminRemark" rows="6" cols="14" class="form-control wd-450" required></textarea>
        </td>
    </tr>

    <tr id="fee">
        <th>Fee Amount :</th>
        <td>
            <input name="feeamt" id="feeamt" class="form-control wd-450">
        </td>
    </tr>

    <tr align="center">
        <td colspan="2"><button type="submit" name="submit" class="btn btn-primary">Update</button></td>
    </tr>
</form>

<?php } else { ?>

    <tr>
        <th>Admin Remark</th>
        <td><?php echo $row['AdminRemark']; ?></td>
    </tr>

    <tr>
        <th>Fee Amount</th>
        <td><?php echo $row['FeeAmount']; ?></td>
    </tr>

    <tr>
        <th>Admin Remark date</th>
        <td><?php echo $row['AdminRemarkDate']; ?></td>
    </tr>

    <tr>
        <th>Application Status</th>
        <td>
            <?php
            if($row['AdminStatus']=="1") {
                echo "Selected";
            } elseif($row['AdminStatus']=="2") {
                echo "Rejected";
            }
            ?>
        </td>
    </tr>

<?php } ?>
</table>

<?php }} ?>

            </div>

            <div style="float:right;">
                <button class="btn btn-primary" style="cursor: pointer;" OnClick="CallPrint(this.value)">Print</button>
            </div>

        </div>
    </div>
</div>

<?php include('includes/footer.php');?>

<script>
function CallPrint(strid) {
    var prtContent = document.getElementById("exampl");
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
}
</script>

<script type="text/javascript">
$('#fee').hide();
$(document).ready(function(){
    $('#status').change(function(){
        if($('#status').val()=='1') {
            $('#fee').show();
            jQuery("#feeamt").prop('required',true);
        } else {
            $('#fee').hide();
            jQuery("#feeamt").prop('required',false);
        }
    });
});
</script>

</body>
</html>
<?php } ?>