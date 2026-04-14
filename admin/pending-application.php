<?php  
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['aid']==0)) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <title>Bapatla Engineering College Admission Management System || Pending Application</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/favicon/bec.ico" type="image/x-icon" />

    <style>
        .errorWrap {
            padding: 10px;
            margin: 20px 0 0px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
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

<?php include('includes/header.php'); ?>
<?php include('includes/leftbar.php'); ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">View Application</h3>

                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pending Application</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <table class="table mb-0 table-bordered">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Course Applied</th>
                        <th>Applicant Name</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $ret = mysqli_query($con, "SELECT 
                    tbladmapplications.CourseApplied,
                    tbladmapplications.ApplicantName,
                    tbladmapplications.ID as apid,
                    tbluser.MobileNumber,
                    tbluser.Email
                    FROM tbladmapplications
                    INNER JOIN tbluser ON tbluser.ID = tbladmapplications.UserId
                    WHERE tbladmapplications.AdminStatus IS NULL");

                $cnt = 1;
                while ($row = mysqli_fetch_array($ret)) {
                ?>
                    <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $row['CourseApplied']; ?></td>
                        <td><?php echo $row['ApplicantName']; ?></td>
                        <td><?php echo $row['MobileNumber']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td>
                            <a href="view-appform.php?aticid=<?php echo $row['apid']; ?>" target="_blank">View Details</a>
                        </td>
                    </tr>
                <?php 
                    $cnt = $cnt + 1;
                } 
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>
<?php } ?>