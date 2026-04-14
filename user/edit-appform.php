<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['uid']==0)) {
  header('location:logout.php');
  } else{
if(isset($_POST['submit']))
  {
    $eid=$_GET['editid'];
    $uid=$_SESSION['uid'];
    $coursename=$_POST['coursename'];
    $applicantname = $_POST['applicantname'];
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
    $dec=$_POST['declaration'];
    $sign=$_POST['signature'];
    $query=mysqli_query($con,"update tbladmapplications set CourseApplied='$coursename',ApplicantName='$applicantname',FatherName='$fathername',MotherName='$mothername',DOB='$dob',Nationality='$nationality',Gender='$gender',Category='$category',CorrespondenceAdd='$coradd',PermanentAdd='$peradd',SecondaryBoard='$secboard',SecondaryBoardyop='$secyop',SecondaryBoardper='$secper',SecondaryBoardstream='$secstream',SSecondaryBoard='$ssecboard',SSecondaryBoardyop='$ssecyop',SSecondaryBoardper='$ssecper',SSecondaryBoardstream='$ssecstream',Signature='$sign' where ID='$eid' && UserId='$uid'");
    
    if ($query) // ---------- FILE UPDATE CODE START ----------
{
$allowed = array("jpg","jpeg","png","pdf");

function replaceFile($fileInput,$dbColumn,$eid,$con,$allowed){
    if(!empty($_FILES[$fileInput]['name'])){
        $old = mysqli_fetch_assoc(mysqli_query($con,"select $dbColumn from tbladmapplications where ID='$eid'"));
        if(!empty($old[$dbColumn]) && file_exists("userdocs/".$old[$dbColumn])){
            unlink("userdocs/".$old[$dbColumn]); // delete old file
        }

        $name = $_FILES[$fileInput]['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if(in_array($ext,$allowed)){
            $newfile = md5(time().$name).'.'.$ext;
            move_uploaded_file($_FILES[$fileInput]["tmp_name"],"userdocs/".$newfile);
            mysqli_query($con,"update tbladmapplications set $dbColumn='$newfile' where ID='$eid'");
        }
    }
}

replaceFile("new_tc","TransferCertificate",$eid,$con,$allowed);
replaceFile("new_10th","TenMarksheeet",$eid,$con,$allowed);
replaceFile("new_12th","TwelveMarksheet",$eid,$con,$allowed);
replaceFile("new_eapcet_rank","EapcetRankCard",$eid,$con,$allowed);
replaceFile("new_eapcet_allot","EapcetAllotmentOrder",$eid,$con,$allowed);
replaceFile("new_ecet_rank","EcetRankCard",$eid,$con,$allowed);
replaceFile("new_ecet_allot","EcetAllotmentOrder",$eid,$con,$allowed);
replaceFile("new_rank","polycet_rank",$eid,$con,$allowed);
replaceFile("new_allot","polycet_allot",$eid,$con,$allowed);

// ---------- FILE UPDATE CODE END ----------

echo "<script>
alert('Application Updated Successfully');
window.location.href='addmission-form.php';
</script>";

}
  else
    {
     echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }
}

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


<form name="submit" method="post" enctype="multipart/form-data">    <?php
$eid=$_GET['editid'];
$uid=$_SESSION['uid'];
$ret=mysqli_query($con,"select * from tbladmapplications where ID='$eid' && UserId='$uid'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>       
        <section class="formatter" id="formatter">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Update Addimission Form</h4>

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
<div class="col-xl-6 col-lg-12">
 <fieldset>
  <h5>Course Applied                   </h5>
   <div class="form-group">
   <input type="text" class="form-control white_bg" 
value="<?php echo $row['CourseApplied'];?>" readonly>
    </div>
</fieldset>
                   
</div>

<div class="col-xl-6 col-lg-12">
 <fieldset>
  <h5>Student Pic                   </h5>
   <div class="form-group">
     <img src="userimages/<?php  echo $row['UserPic'];?>" width="100" height="100"> <a href="changeimage.php?editid=<?php echo $row['ID'];?>"> &nbsp; Edit Image</a>
    </div>
</fieldset>                  
</div>
 </div>               
 <div class="row">
  <div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Applicantname Name  </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="applicantname" name="applicantname"  type="text" required='true' value="<?php echo $row['ApplicantName'];?>">
    </div>
</fieldset>               
</div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Fathers Name                   </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="fathername" name="fathername"  type="text" required='true' value="<?php echo $row['FatherName'];?>">
    </div>
</fieldset>               
</div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Mothers Name                 </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="mothername" name="mothername"  type="text" required='true' value="<?php echo $row['MotherName'];?>">
                          </div>
                        </fieldset>
                      </div>
                    </div>
<div class="row">
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>DOB                   </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="dob" name="dob"  type="text" required='true' value="<?php echo $row['DOB'];?>">
          <small class="text-muted">DOB Must be in this format (YYYY-MM-DD)</small>
    </div>

</fieldset>                  
</div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Nationality                </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="nationality" name="nationality"  type="text" required='true' value="<?php echo $row['Nationality'];?>">
                          </div>

                        </fieldset>
                      </div>
<div class="col-xl-4 col-lg-12">
 <fieldset>
  <h5>Gender                </h5>
   <div class="form-group">

   <select class="form-control white_bg" id="gender" name="gender"  required>
    <option value="<?php echo $row['Gender'];?>"><?php echo $row['Gender'];?></option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Transgender">Transgender</option>
   </select>
                          </div>
                        </fieldset>
                      </div>

                    </div>
<div class="row">
  <div class="col-xl-12 col-lg-12">
    <h5>Category : </h5>
   
<select class="form-control white_bg" id="category" name="category"  required>
    <option value="<?php echo $row['Category'];?>"><?php echo $row['Category'];?></option>
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
   <input class="form-control white_bg" id="coradd" name="coradd"  type="text" required='true' value="<?php echo $row['CorrespondenceAdd'];?>">
    </div>
</fieldset>
  </div>
</div>
<div class="row">
  <div class="col-xl-12 col-lg-12">
    <fieldset>
  <h5>Permanent Address                  </h5>
   <div class="form-group">
   <input class="form-control white_bg" id="peradd" name="peradd"  type="text" required='true' value="<?php echo $row['PermanentAdd'];?>">
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
<td>   <input class="form-control white_bg" id="10thboard" name="10thboard" placeholder="Board / University"  type="text" required='true' value="<?php echo $row['SecondaryBoard'];?>"></td>
<td>   <input class="form-control white_bg" id="10thpyeaer" name="10thpyear" placeholder="Year"  type="text" required='true' value="<?php echo $row['SecondaryBoardyop'];?>"></td>
<td>   <input class="form-control white_bg" id="10thpercentage" name="10thpercentage" placeholder="Percentage"  type="text" required='true' value="<?php echo $row['SecondaryBoardper'];?>"></td>
<td>   <input class="form-control white_bg" id="10thstream" name="10thstream" placeholder="Stream"  type="text" required='true' value="<?php echo $row['SecondaryBoardstream'];?>"></td>
</tr>
<tr>
<th>12th(Senior Secondary)</th>
<td>   <input class="form-control white_bg" id="12thboard" name="12thboard" placeholder="Board / University"  type="text" required='true' value="<?php echo $row['SSecondaryBoard'];?>"></td>
<td>   <input class="form-control white_bg" id="12thboard" name="12thpyear" placeholder="Year"  type="text" required='true' value="<?php echo $row['SSecondaryBoardyop'];?>"></td>
<td>   <input class="form-control white_bg" id="12thpercentage" name="12thpercentage" placeholder="Percentage"  type="text" required='true' value="<?php echo $row['SSecondaryBoardper'];?>"></td>
<td>   <input class="form-control white_bg" id="12thstream" name="12thstream" placeholder="Stream"  type="text" required='true' value="<?php echo $row['SSecondaryBoardstream'];?>"></td>
</tr>
</hr>

<div class="row" style="margin-top:2%">
<div class="col-xl-12 col-lg-12">
<h4 class="card-title">Update Uploaded Documents</h4>
<table class="table mb-0">

<?php if(!empty($row['TransferCertificate']) || !empty($row['TenMarksheeet'])) { ?>
<tr>
<?php if(!empty($row['TransferCertificate'])) { ?>
<th>Transfer Certificate</th>
<td>
<a href="userdocs/<?php echo $row['TransferCertificate'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_tc" class="form-control">
</td>
<?php } ?>

<?php if(!empty($row['TenMarksheeet'])) { ?>
<th>10th Memo</th>
<td>
<a href="userdocs/<?php echo $row['TenMarksheeet'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_10th" class="form-control">
</td>
<?php } ?>
</tr>
<?php } ?>


<?php if(!empty($row['TwelveMarksheet'])) { ?>
<tr>
<th>12th Memo</th>
<td>
<a href="userdocs/<?php echo $row['TwelveMarksheet'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_12th" class="form-control">
</td>
</tr>
<?php } ?>


<?php if(!empty($row['EapcetRankCard']) || !empty($row['EapcetAllotmentOrder'])) { ?>
<tr>
<?php if(!empty($row['EapcetRankCard'])) { ?>
<th>EAPCET Rank Card</th>
<td>
<a href="userdocs/<?php echo $row['EapcetRankCard'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_eapcet_rank" class="form-control">
</td>
<?php } ?>

<?php if(!empty($row['EapcetAllotmentOrder'])) { ?>
<th>EAPCET Allotment</th>
<td>
<a href="userdocs/<?php echo $row['EapcetAllotmentOrder'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_eapcet_allot" class="form-control">
</td>
<?php } ?>
</tr>
<?php } ?>


<?php if(!empty($row['EcetRankCard']) || !empty($row['EcetAllotmentOrder'])) { ?>
<tr>
<?php if(!empty($row['EcetRankCard'])) { ?>
<th>ECET Rank Card</th>
<td>
<a href="userdocs/<?php echo $row['EcetRankCard'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_ecet_rank" class="form-control">
</td>
<?php } ?>

<?php if(!empty($row['EcetAllotmentOrder'])) { ?>
<th>ECET Allotment</th>
<td>
<a href="userdocs/<?php echo $row['EcetAllotmentOrder'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_ecet_allot" class="form-control">
</td>
<?php } ?>
</tr>
<?php } ?>


<?php if(!empty($row['polycet_rank']) || !empty($row['polycet_allot'])) { ?>
<tr>
<?php if(!empty($row['polycet_rank'])) { ?>
<th>POLYCET Rank Card</th>
<td>
<a href="userdocs/<?php echo $row['polycet_rank'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_rank" class="form-control">
</td>
<?php } ?>

<?php if(!empty($row['polycet_allot'])) { ?>
<th>POLYCET Allotment</th>
<td>
<a href="userdocs/<?php echo $row['polycet_allot'];?>" target="_blank">View File</a><br><br>
<input type="file" name="new_allot" class="form-control">
</td>
<?php } ?>
</tr>
<?php } ?>
</table>
</div>
</div>
</hr>
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
 <input class="form-control white_bg" id="signature" name="signature" placeholder="Signature"  type="text" value="<?php echo $row['Signature'];?>"> 
 </fieldset>  
</div>
</div><?php 
$cnt=$cnt+1;
}?>
<div class="row" style="margin-top: 2%">
<div class="col-xl-6 col-lg-12">
<button type="submit" name="submit" class="btn btn-info btn-min-width mr-1 mb-1">Update</button>
</div>
</div>
 </div>
                </div>
              </div>
            </div>
          </div>
        </section>
     
        <!-- Formatter end -->
      </form>  
      </div>
    </div>
  </div>
<?php include('includes/footer.php');?>
 
</body>
</html>
<?php  } ?>
