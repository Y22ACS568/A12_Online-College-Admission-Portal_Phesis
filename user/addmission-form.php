<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['uid']==0)) {
  header('location:logout.php');
} else {

if(isset($_POST['submit']))
{
    $uid=$_SESSION['uid'];
    $coursename=$_POST['coursename'];
    $exam_type = isset($_POST['exam_type']) ? $_POST['exam_type'] : "";

    $applicantname = $_POST['applicantname'];
    $branch=$_POST['branch'];
    $fathername=$_POST['fathername'];
    $mothername=$_POST['mothername'];
    $dob=$_POST['dob'];
    $nationality=$_POST['nationality'];
    $gender=$_POST['gender'];
    $category=$_POST['category'];
    $coradd=$_POST['coradd'];
    $peradd=$_POST['peradd'];
    $secboard=$_POST['10thboard'];
    $secyop=$_POST['10thpyear'];
    $secper=$_POST['10thpercentage'];
    $secstream=$_POST['10thstream'];
    $ssecboard=$_POST['12thboard'];
    $ssecyop=$_POST['12thpyear'];
    $ssecper=$_POST['12thpercentage'];
    $ssecstream=$_POST['12thstream'];
    $sign=$_POST['signature'];

    // FILES
    $upic = $_FILES["UserPic"]["name"];
    $tc = $_FILES["tcimage"]["name"];
    $tenmarksheet = $_FILES["hscimage"]["name"];
    $twlevemaksheet = $_FILES["sscimage"]["name"];

    $eapcetrank = $_FILES['eapcetrank']['name'];
    $eapcetallot = $_FILES['eapcetallot']['name'];

    $ecetrank = $_FILES['ecetrank']['name'];
    $ecetallot = $_FILES['ecetallot']['name'];

    $polycet_rank = $_FILES['polycet_rank']['name'];
    $polycet_allot = $_FILES['polycet_allot']['name'];

    /* -------- EXAM VALIDATION -------- */
    if ($coursename != "DIPLOMA") {

        if ($exam_type == "EAPCET") {

            if (empty($eapcetrank) || empty($eapcetallot)) {
                echo "<script>alert('Upload BOTH EAPCET Rank Card & Allotment Order');</script>";
                exit;
            }

            if (!empty($ecetrank) || !empty($ecetallot)) {
                echo "<script>alert('Only ONE exam allowed (EAPCET OR ECET)');</script>";
                exit;
            }

        } elseif ($exam_type == "ECET") {

            if (empty($ecetrank) || empty($ecetallot)) {
                echo "<script>alert('Upload BOTH ECET Rank Card & Allotment Order');</script>";
                exit;
            }

            if (!empty($eapcetrank) || !empty($eapcetallot)) {
                echo "<script>alert('Only ONE exam allowed (EAPCET OR ECET)');</script>";
                exit;
            }

        } else {
            echo "<script>alert('Please select an entrance exam');</script>";
            exit;
        }
    }

    /* FILE EXTENSIONS */
    $extension = strtolower(pathinfo($upic, PATHINFO_EXTENSION));
    $extensiontc = strtolower(pathinfo($tc, PATHINFO_EXTENSION));
    $extensiontm = strtolower(pathinfo($tenmarksheet, PATHINFO_EXTENSION));
    $extensiontwm = strtolower(pathinfo($twlevemaksheet, PATHINFO_EXTENSION));

    $allowed_extensions = array("jpg","jpeg","png","gif","pdf");

    if(!empty($upic) && !in_array($extension,$allowed_extensions))
    {
        echo "<script>alert('Invalid Student Pic format');</script>";
    }
    elseif(!empty($tc) && !in_array($extensiontc,$allowed_extensions))
    {
        echo "<script>alert('Invalid TC format');</script>";
    }
    elseif(!empty($tenmarksheet) && !in_array($extensiontm,$allowed_extensions))
    {
        echo "<script>alert('Invalid 10th Marksheet format');</script>";
    }
    elseif($coursename!="DIPLOMA" && !empty($twlevemaksheet) && !in_array($extensiontwm,$allowed_extensions))
    {
        echo "<script>alert('Invalid 12th Marksheet format');</script>";
    }
    else
    {
        /* ---------- RENAME FILES ---------- */

        // STUDENT PIC
        if(!empty($_FILES["UserPic"]["name"])) {
            $UserPic = md5(time() . basename($_FILES["UserPic"]["name"])) . "." . $extension;
            move_uploaded_file($_FILES["UserPic"]["tmp_name"], "userimages/".$UserPic);
        } else {
            $UserPic = NULL;
        }

        // TC
        if(!empty($_FILES["tcimage"]["name"])) {
            $tc = md5(time() . basename($_FILES["tcimage"]["name"])) . "." . $extensiontc;
            move_uploaded_file($_FILES["tcimage"]["tmp_name"], "userdocs/".$tc);
        } else {
            $tc = NULL;
        }

        // 10TH MARKSHEET
        if(!empty($_FILES["hscimage"]["name"])) {
            $tm = md5(time() . basename($_FILES["hscimage"]["name"])) . "." . $extensiontm;
            move_uploaded_file($_FILES["hscimage"]["tmp_name"], "userdocs/".$tm);
        } else {
            $tm = NULL;
        }

        // 12TH / DIPLOMA MARKSHEET
        if(!empty($_FILES["sscimage"]["name"])) {
            $twm = md5(time() . basename($_FILES["sscimage"]["name"])) . "." . $extensiontwm;
            move_uploaded_file($_FILES["sscimage"]["tmp_name"], "userdocs/".$twm);
        } else {
            $twm = NULL;
        }

        // EXAM FILES
        $erc = NULL;
        $eao = NULL;
        $ec = NULL;
        $eo = NULL;

        if ($exam_type == "EAPCET") {
            move_uploaded_file($_FILES['eapcetrank']['tmp_name'], "userdocs/".$eapcetrank);
            move_uploaded_file($_FILES['eapcetallot']['tmp_name'], "userdocs/".$eapcetallot);

            $erc = $eapcetrank;
            $eao = $eapcetallot;
        }

        if ($exam_type == "ECET") {
            move_uploaded_file($_FILES['ecetrank']['tmp_name'], "userdocs/".$ecetrank);
            move_uploaded_file($_FILES['ecetallot']['tmp_name'], "userdocs/".$ecetallot);

            $ec = $ecetrank;
            $eo = $ecetallot;
        }

        // POLYCET FILES
        $polycet_rank_db = NULL;
        $polycet_allot_db = NULL;

        if ($coursename == "DIPLOMA") {
            move_uploaded_file($_FILES['polycet_rank']['tmp_name'], "userdocs/".$polycet_rank);
            move_uploaded_file($_FILES['polycet_allot']['tmp_name'], "userdocs/".$polycet_allot);

            $polycet_rank_db = $polycet_rank;
            $polycet_allot_db = $polycet_allot;
        }

        // INSERT QUERY
        $query=mysqli_query($con,"INSERT INTO tbladmapplications(
            UserId, CourseApplied, ApplicantName, Branch, FatherName, MotherName, DOB, Nationality, Gender, Category,
            CorrespondenceAdd, PermanentAdd, SecondaryBoard, SecondaryBoardyop, SecondaryBoardper, SecondaryBoardstream,
            SSecondaryBoard, SSecondaryBoardyop, SSecondaryBoardper, SSecondaryBoardstream,
            Signature, UserPic, TransferCertificate, TenMarksheeet, TwelveMarksheet,
            EapcetRankCard, EapcetAllotmentOrder, EcetRankCard, EcetAllotmentOrder,
            polycet_rank, polycet_allot
        ) VALUES (
            '$uid','$coursename','$applicantname','$branch','$fathername','$mothername','$dob','$nationality','$gender','$category',
            '$coradd','$peradd','$secboard','$secyop','$secper','$secstream',
            '$ssecboard','$ssecyop','$ssecper','$ssecstream',
            '$sign','$UserPic','$tc','$tm','$twm',
            '$erc','$eao','$ec','$eo','$polycet_rank_db','$polycet_allot_db'
        )");

        if ($query) {
            echo '<script>alert("Data has been added successfully.")</script>';
            echo "<script>window.location.href ='addmission-form.php'</script>";
        } else {
            echo '<script>alert("Something Went Wrong. Please try again.")</script>';
            echo "<script>window.location.href ='addmission-form.php'</script>";
        }
    }
}
?>
  ?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>

  <title>Bapatla Engineering College Admission Management System|| Admission Form</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
  rel="stylesheet">
  <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
  rel="stylesheet">
  <link rel="icon" href="../assets/images/favicon/bec.ico" type="image/x-icon" />

     <style>
    .errorWrap {
    padding: 10px;
    margin: 20px 0 0px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
    </style>
</head>
<body class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
<?php include('includes/header.php');?>
<?php include('includes/leftbar.php');?>
  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
          <h3 class="content-header-title mb-0 d-inline-block">
           Admission Application Form
          </h3>
          <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a>
                </li>
            
                </li>
                <li class="breadcrumb-item active">Application
                </li>
              </ol>
            </div>
          </div>
        </div>
   
      </div>
      <div class="content-body">
  
<?php 
$stuid=$_SESSION['uid'];
$query=mysqli_query($con,"select tbladmapplications.*,tbluser.*,tbladmapplications.ID as appid  from tbladmapplications 
  join tbluser on tbluser.ID=tbladmapplications.UserId where  UserId=$stuid");
$rw=mysqli_num_rows($query);
if($rw>0)
{
while($row=mysqli_fetch_array($query)){
?>
<p style="font-size:16px; color:red" align="center">Your Admission Form already submitted.</p>
  <div  id="exampl">     
<table class="table mb-0" border="1" width="100%">
<tr>
  
  <th>Registration Date</th>
  <td><?php echo $row['CourseApplieddate'];?></td>
</tr>

<tr>
  <th>Course Applied</th>
  <td><?php echo $row['CourseApplied'];?></td>
  <th>Branch</th>
  <td><?php echo $row['Branch']; ?></td>
  <th>Student Pic</th>
  <td><?php
$imagePath = "userimages/" . $row['UserPic'];
if (!empty($row['UserPic']) && file_exists($imagePath)) {
?>
    <img src="<?php echo $imagePath; ?>" width="100" height="100" style="border-radius:5px; border:1px solid #ccc;">
<?php
} else {
?>
    <img src="userimages/default.png" width="100" height="100" style="border-radius:5px; border:1px solid #ccc;">
<?php
}
?>
<br><br>
<a href="changeimage.php?editid=<?php echo $row['ID']; ?>">Edit Image</a></td>
</tr>
<tr>
  <th>Applicant Name</th>
  <td><?php echo $row['ApplicantName']; ?></td>
  <th>Father's Name</th>
  <td><?php echo $row['FatherName'];?></td>
  <th>Mother's Name</th>
  <td><?php echo $row['MotherName'];?></td>
</tr>
<tr>
  <th>DOB</th>
  <td><?php echo $row['DOB'];?></td>
  <th>Nationality</th>
  <td><?php echo $row['Nationality'];?></td>
</tr>
<tr>
  <th>Gender</th>
  <td><?php echo $row['Gender'];?></td>
  <th>Category</th>
  <td><?php echo $row['Category'];?></td>
</tr>
<tr>
  <th>Correspondence Address</th>
  <td><?php echo $row['CorrespondenceAdd'];?></td>
  <th>Permanent Address</th>
  <td><?php echo $row['PermanentAdd'];?></td>
</tr>
<?php if(!empty($row['TransferCertificate']) || !empty($row['TenMarksheeet'])) { ?>
<tr>

<?php if(!empty($row['TransferCertificate'])) { ?>
  <th>Transfer Certificate</th>
 <td>
<a href="userdocs/<?php echo $row['TransferCertificate']; ?>" target="_blank">View File</a>
</td>
<?php } ?>

<?php if(!empty($row['TenMarksheeet'])) { ?>
  <th>10th Marksheet</th>
  <td>
<a href="userdocs/<?php echo $row['TenMarksheeet']; ?>" target="_blank">View File</a>
</td>
<?php } ?>
</tr>
<?php } ?>
<?php if(!empty($row['TwelveMarksheet'])) { ?>
<tr>
  <th>12th Marksheet</th>
  <td><a href="userdocs/<?php echo $row['TwelveMarksheet'];?>" target="_blank">View File</a></td>
</tr>
<?php } ?>
<?php if(!empty($row['EapcetRankCard']) || !empty($row['EapcetAllotmentOrder'])) { ?>
<tr>

<?php if(!empty($row['EapcetRankCard'])) { ?>
<th>EAPCET Rank Card</th>
<td><a href="userdocs/<?php echo $row['EapcetRankCard']; ?>" target="_blank">View File</a></td>
<?php } ?>

<?php if(!empty($row['EapcetAllotmentOrder'])) { ?>
<th>EAPCET Allotment Order</th>
<td><a href="userdocs/<?php echo $row['EapcetAllotmentOrder']; ?>" target="_blank">View File</a></td>
<?php } ?>

</tr>
<?php } ?>


<?php if(!empty($row['EcetRankCard']) || !empty($row['EcetAllotmentOrder'])) { ?>
<tr>

<?php if(!empty($row['EcetRankCard'])) { ?>
<th>ECET Rank Card</th>
<td><a href="userdocs/<?php echo $row['EcetRankCard']; ?>" target="_blank">View File</a></td>
<?php } ?>

<?php if(!empty($row['EcetAllotmentOrder'])) { ?>
<th>ECET Allotment Order</th>
<td><a href="userdocs/<?php echo $row['EcetAllotmentOrder']; ?>" target="_blank">View File</a></td>
<?php } ?>

</tr>
<?php } ?>
<?php if(!empty($row['polycet_rank']) || !empty($row['polycet_allot'])) { ?>
<tr>

<?php if(!empty($row['polycet_rank'])) { ?>
<th>POLYCET Rank Card</th>
<td><a href="userdocs/<?php echo $row['polycet_rank']; ?>" target="_blank">View File</a></td>
<?php } ?>

<?php if(!empty($row['polycet_allot'])) { ?>
<th>POLYCET Allotment Order</th>
<td><a href="userdocs/<?php echo $row['polycet_allot']; ?>" target="_blank">View File</a></td>
<?php } ?>

</tr>
<?php } ?>

</table>
<table class="table mb-0" border="1" width="100%" style="margin-top:1%">
<tr>
  <th>#</th>
   <th>Board / University</th>
    <th>Year</th>
     <th>Percentage</th>
       <th>Stream</th>
</tr>
<th>10th(Secondary)</th>
  <td><?php echo $row['SecondaryBoard'];?></td>
  <td><?php echo $row['SecondaryBoardyop'];?></td>
   <td><?php echo $row['SecondaryBoardper'];?></td>
   <td><?php echo $row['SecondaryBoardstream'];?></td>
</tr>
<tr>
  <th>12th(Senior Secondary)</th>
  <td><?php echo $row['SSecondaryBoard'];?></td>
   <td><?php echo $row['SSecondaryBoardyop'];?></td>
   <td><?php echo $row['SSecondaryBoardper'];?></td>
    <td><?php echo $row['SSecondaryBoardstream'];?></td>
</tr>

</table>
<?php if($row['AdminStatus']==""):?>
  <?php else: ?>
<table class="table mb-0" border="1" width="100%">
<tr>
  <th>Admin Remark</th>
  <td><?php echo $row['AdminRemark'];?></td>
</tr>
<tr>
  <th>Admin Status</th>
 <td><?php 
                  if($row['AdminStatus']==""){
echo "admin remark is pending";
} 

 if($row['AdminStatus']=="1"){
  echo "Selected";
}
    
if($row['AdminStatus']=="2"){
  echo "Rejected";
}
                    ?></td>
</tr>
<tr>
  <th>Admin Remark Date</th>
  <td><?php echo $row['AdminRemarkDate'];?></td>
</tr>
<?php endif;?>
  <tr>
    <th colspan="2"><font color="red">Declaration : </font>I hereby state that the facts mentioned above are true to the best of my knowledge and belief.<br />
(<?php  echo $row['Signature'];?>)
    </th>
  </tr>
</table>


</div>
<div style="float:right;">
  <button class="btn btn-primary" style="cursor: pointer;"  OnClick="CallPrint(this.value)" >Print</button></div>
<?php 

if ($row['AdminStatus']==""){
?>
<p style="text-align: center;font-size: 20px;"><a href="edit-appform.php?editid=<?php echo $row['appid'];?>">Edit Details</a></p>
<?php }} } else { ?>
<form name="submit" method="post" enctype="multipart/form-data">        
        <section class="formatter" id="formatter">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Addimission Form</h4>

                  <div class="heading-elements">
                    <ul class="list-inline mb-0">
                  
                      <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                      <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      
                    </ul>
                  </div>
                </div>
                <div class="card-content">
                  <div class="card-body">
 

<div class="row">
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Course Applied                   </h5>
   <div class="form-group">
   <select name='coursename' id="coursename" class="form-control white_bg" onchange="courseCheck()" required="true">
     <option value="">Course Applied</option>
      <?php $query=mysqli_query($con,"select * from tblcourse");
              while($row=mysqli_fetch_array($query))
              {
              ?>    
              <option value="<?php echo $row['CourseName'];?>"><?php echo $row['CourseName'];?></option>
                  <?php } ?>  
   </select>
    </div>
</fieldset>
                   
</div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Branch</h5>
   <div class="form-group">

<select class="form-control white_bg" id="branch" name="branch"  required>
<option value="Select">Select</option>
<option value="B>TECH BRANCHES">B.TECH BRANCHES</option>
<option value="CSE">CSE</option>
<option value="AIML">AIML</option>
<option value="CS">CS</option>
<option value="DS">DS</option>
<option value="ECE">ECE</option>
<option value="EEE">EEE</option>
<option value="MECH">MECH</option>
<option value="EIE">EIE</option>
<option value="CVIL">CVIL</option>
<option value="DIPLOMA BRANCHES">DIPLOMA BRANCHES</option>
<option value="CSM">CSM</option>
<option value="AIML">AIML</option>
<option value="CS">CS</option>
<option value="DS">DS</option>
<option value="ECE">ECE</option>
<option value="EEE">EEE</option>
<option value="MECH">MECH</option>
<option value="EIE">EIE</option>
<option value="CVIL">CVIL</option>
   </select>
                          </div>
                        </fieldset>
                      </div>

<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Student Pic                   </h5>
   <div class="form-group">
    <input class="form-control white_bg" id="UserPic" name="UserPic"  type="file" required>
    </div>
</fieldset>                  
</div>
 </div>               
 <div class="row">
<div class="col-xl-4 col-lg-12">
    <fieldset>
      <h5>Applicant Name</h5>
      <div class="form-group">
        <input class="form-control white_bg"
               type="text"
               name="applicantname"
               placeholder="Enter Applicant Name"
               required>
      </div>
    </fieldset>
  </div>
<div class="col-xl-4 col-lg-12">
    <fieldset>
      <h5>Father Name</h5>
      <div class="form-group">
        <input class="form-control white_bg"
               type="text"
               name="fathername"
               placeholder="Enter Father Name"
               required>
      </div>
    </fieldset>
  </div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Mother Name                 </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="mothername" name="mothername"  type="text" placeholder="Enter Mother Name" required>
                          </div>
                        </fieldset>
                      </div>
                    </div>
<div class="row">
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>DOB                   </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="dob" name="dob"  type="text" required>
          <small class="text-muted">DOB Must be in this format (YYYY-MM-DD)</small>
    </div>

</fieldset>                  
</div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Nationality                </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="nationality" name="nationality"  type="text" required>
                          </div>

                        </fieldset>
                      </div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Gender                </h5>
   <div class="form-group">

   <select class="form-control white_bg" id="gender" name="gender"  required>
    <option value="">Select</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Transgender">Others</option>
   </select>
                          </div>
                        </fieldset>
                      </div>

                    </div>
<div class="row">
  <div class="col-xl-12 col-lg-12">
    <h5>Category : </h5>
   
<select class="form-control white_bg" id="category" name="category"  required>
    <option value="">Select Category</option>
<option value="General">General</option>
<option value="OBC">OBC</option>
<option value="SC/ST">SC/ST</option>
<option value="SC/ST">Other</option>
   </select>

  </div>
</div>
<div class="row" style="margin-top:1%">
  <div class="col-xl-12 col-lg-12">
    <fieldset>
  <h5>Correspondence Address                  </h5>
   <div class="form-group">
   <textarea class="form-control white_bg" id="coradd" name="coradd"  type="text" required rows="4"></textarea>
    </div>
</fieldset>
  </div>
</div>
<div class="row">
  <div class="col-xl-12 col-lg-12">
    <fieldset>
  <h5>Permanent Address                  </h5>
   <div class="form-group">
   <textarea class="form-control white_bg" id="peradd" name="peradd"  type="text" required rows="4"></textarea>
    </div>
</fieldset>
  </div>
</div>
<div class="row" style="margin-top: 2%">
  <div class="col-xl-12 col-lg-12"><h4 class="card-title">Education Qualification</h4>
<hr />
  </div>
</div>
<div class="row">
<div class="col-xl-12 col-lg-12">
<table class="table mb-0">
<tr>
  <th>#</th>
   <th>Board / University</th>
    <th>Year</th>
     <th>Percentage</th>
       <th>Stream</th>
</tr>
<tr>
<th>10th(Secondary)</th>
<td>   <input class="form-control white_bg" id="10thboard" name="10thboard" placeholder="Board / University"  type="text" required></td>
<td>   <input class="form-control white_bg" id="10thpyeaer" name="10thpyear" placeholder="Year"  type="text" required></td>
<td>   <input class="form-control white_bg" id="10thpercentage" name="10thpercentage" placeholder="Percentage"  type="text" required></td>
<td>   <input class="form-control white_bg" id="10thstream" name="10thstream" placeholder="Stream"  type="text" required></td>
</tr>
<tr>
<th>12th(Senior Secondary)</th>
<td>   <input class="form-control white_bg" id="12thboard" name="12thboard" placeholder="Board / University"  type="text" required></td>
<td>   <input class="form-control white_bg" id="12thboard" name="12thpyear" placeholder="Year"  type="text" required></td>
<td>   <input class="form-control white_bg" id="12thpercentage" name="12thpercentage" placeholder="Percentage"  type="text" required></td>
<td>   <input class="form-control white_bg" id="12thstream" name="12thstream" placeholder="Stream"  type="text" required></td>
</tr>
</table>
</div>
</div>
</hr>

<div class="row">
  <div class="col-xl-5 col-lg-12">
    <fieldset>
      <h5>Transfer Certificate (TC) *</h5>
      <div class="form-group">
        <input class="form-control white_bg" name="tcimage" type="file" required>
      </div>
    </fieldset>
  </div>

  <div class="col-xl-5 col-lg-12">
    <fieldset>
      <h5>10th Marksheet *</h5>
      <div class="form-group">
        <input class="form-control white_bg" name="hscimage" type="file" required>
      </div>
    </fieldset>
  </div>
</div>
<div id="btechSection">
<div class="row" style="margin-top: 2%" id="examSection">
  <div class="col-xl-12 col-lg-12">
    <h5>Entrance Exam</h5>
    <label>
      <input type="radio" name="exam_type" value="EAPCET"> EAPCET
    </label>
    &nbsp;&nbsp;
    <label>
      <input type="radio" name="exam_type" value="ECET"> ECET
    </label>
  </div>
</div><br/><br/>
<div class="row">
<div class="col-xl-5 col-lg-12">
 <fieldset>
<h5>12th Mark Sheet / Diploma Mark Sheet</h5>
  <div class="form-group">
    <input class="form-control white_bg" id="sscimage" name="sscimage"  type="file" required>
  </div>
</fieldset>                 
</div>
</div>
<div class="row">
<div class="col-xl-5 col-lg-12">
  <fieldset>
  <h5>EAPCET Rank Card</h5>
   <div class="form-group">
    <input class="form-control white_bg" name="eapcetrank" type="file">
    </div>
</fieldset>
 </div>
 <div class="col-xl-5 col-lg-12">
  <fieldset>
  <h5>EAPCET Allotement Order</h5>
   <div class="form-group">
    <input class="form-control white_bg" name="eapcetallot" type="file">
    </div>
</fieldset>
 </div>
</div>        
 <div class="row">
<div class="col-xl-5 col-lg-12">
  <fieldset>
  <h5>ECET Rank Card</h5>
   <div class="form-group">
    <input class="form-control white_bg" name="ecetrank" type="file">
    </div>
</fieldset>
</div>
<div class="col-xl-5 col-lg-12">
  <fieldset>
  <h5>ECET Allotement Order </h5>
   <div class="form-group">
    <input class="form-control white_bg" name="ecetallot" type="file">
    </div>
</fieldset>
</div>
</div>
</div>
<!-- POLYCET SECTION -->
<div id="polycetSection" style="display:none">

<div class="row">
<div class="col-xl-5 col-lg-12">
<fieldset>
<h5>POLYCET Rank Card</h5>
<div class="form-group">
<input class="form-control white_bg" name="polycet_rank" type="file">
</div>
</fieldset>
</div>

<div class="col-xl-5 col-lg-12">
<fieldset>
<h5>POLYCET Allotment Order</h5>
<div class="form-group">
<input class="form-control white_bg" name="polycet_allot" type="file">
</div>
</fieldset>
</div>
</div>
</div>
<div class="row" style="margin-top: 2%">
<div class="col-xl-12 col-lg-12"><h4 class="card-title"><b>Declartion</b></h4> <hr />
</div>
</div>
 <div class="row">
<div class="col-xl-12 col-lg-12">
<h5><b>I hereby state that the facts mentioned above are true to the best of my knowledge and belief.</b></h5>
 </div>
 </div>               
<div class="row"> 
<div class="col-xl-4 col-lg-12">
<fieldset>
  <h5>Signature</h5>
 <input class="form-control white_bg" id="signature" name="signature" placeholder="Signature"  type="text"> 
 </fieldset> 
</div>
</div>
<div class="row" style="margin-top: 2%">
<div class="col-xl-6 col-lg-12">
<button type="submit" name="submit" class="btn btn-info btn-min-width mr-1 mb-1">Submit</button>
</div>
</div>
 </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <?php } ?>
        <!-- Formatter end -->
      </form>  
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

document.querySelectorAll('input[name="exam_type"]').forEach(function(radio){

radio.addEventListener("change", function(){

if(this.value=="EAPCET")
{
document.querySelector("input[name='eapcetrank']").disabled=false;
document.querySelector("input[name='eapcetallot']").disabled=false;

document.querySelector("input[name='ecetrank']").disabled=true;
document.querySelector("input[name='ecetallot']").disabled=true;

document.querySelector("input[name='ecetrank']").value="";
document.querySelector("input[name='ecetallot']").value="";
}

if(this.value=="ECET")
{
document.querySelector("input[name='ecetrank']").disabled=false;
document.querySelector("input[name='ecetallot']").disabled=false;

document.querySelector("input[name='eapcetrank']").disabled=true;
document.querySelector("input[name='eapcetallot']").disabled=true;

document.querySelector("input[name='eapcetrank']").value="";
document.querySelector("input[name='eapcetallot']").value="";
}

});

});
function courseCheck()
{
var course = document.getElementById("coursename").value;

var btech = document.getElementById("btechSection");
var poly = document.getElementById("polycetSection");

if(course.trim()=="DIPLOMA")
{
btech.style.display="none";
poly.style.display="block";

document.querySelectorAll("#btechSection input").forEach(function(el){
el.required = false;
});

document.querySelectorAll("#polycetSection input").forEach(function(el){
el.required = true;
});

}
else
{
btech.style.display="block";
poly.style.display="none";

document.querySelectorAll("#btechSection input").forEach(function(el){
el.required = true;
});

document.querySelectorAll("#polycetSection input").forEach(function(el){
el.required = false;
});
}
}

window.onload = courseCheck;

</script>
</body>
</html>
<?php  } ?>